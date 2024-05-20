<?php

namespace app\myadmin\controller\fastscrm\guide;

use addons\fastscrm\library\Export;
use addons\fastscrm\library\Job\AddJob;
use app\admin\library\Auth;
use app\myadmin\controller\fastscrm\Scrmbackend;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 批量添加客户
 *
 * @icon fa fa-circle-o
 */
class Batch extends Scrmbackend
{

    /**
     * Batch模型对象
     * @var \app\admin\model\fastscrm\guide\Batch
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\guide\Batch;
        $this->view->assign("statusList", $this->model->getStatusList());
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {

                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);
            foreach ($list as $row) {
                if (!empty($row->tags)) {
                    $tags      = Db::name('fastscrm_tag')->where('tag_id', 'in', $row->tags)->select();
                    $row->tags = $tags;
                } else {
                    $row->tags = [];
                }
                $worker_name      = Db::name('fastscrm_worker')->where('userid', $row->worker_id)->value('name');
                $row->worker_name = $worker_name;
            }
            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 导出模板
     */
    public function export()
    {
        $export = new Export();
        $title  = ['手机号', '备注'];
        $export->excelExport('批量添加客户导入模版', $title, array());
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $list    = $this->getImport();
            $tags    = $this->request->request('tags');
            $workers = array_unique(explode(',', $this->request->request('workers')));
            $temp    = [];
            foreach ($list as $item) {
                if (!in_array(trim($item['mobile']), $temp) && !empty($item['mobile'])) {
                    $mobile             = trim($item['mobile']);
                    $remark             = trim($item['remark']);
                    $item[]             = $mobile;
                    $data['mobile']     = $mobile;
                    $data['remark']     = $remark;
                    $data['tags']       = $tags;
                    $data['status']     = 1;
                    $data['branchnum']  = 1;
                    $data['createtime'] = time();
                    if (!empty($workers)) {
                        foreach ($workers as $worker) {
                            $data['worker_id'] = $worker;
                            $this->model->insert($data);
                        }
                    } else {
                        $this->model->insert($data);
                    }

                }
            }
            $job                      = new AddJob();
            $param['admin_id']        = $this->auth->isLogin() ? $this->auth->id : 0;
            $param['username']        = $this->auth->isLogin() ? $this->auth->username : __('Unknown');
            $param['task']            = 'sendMessage';
            $param['ip']              = request()->ip();
            $param['data']['type']    = 1;
            $param['data']['workers'] = implode('|', $workers);
            $param['data']['title']   = '分配客户通知';
            $param['data']['content'] = '系统为您分配了新客户，请尽快添加';
            $param['data']['url']     = addon_url('fastscrm/batch/index', [], false, true);
            $param['data']['btnTxt']  = '查看详情';
            $job->add($param);
            $this->success();
        }
        return $this->view->fetch();
    }

    /**
     * 批量更新
     */
    public function multi($ids = "")
    {
        if (!$this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ? $ids : $this->request->post("ids");
        if ($ids) {
            $worker = $this->request->post("worker");
            if ($worker) {
                $values['worker_id'] = $worker;
                $count               = 0;
                Db::startTrans();
                try {
                    $list = $this->model->where($this->model->getPk(), 'in', $ids)->select();
                    foreach ($list as $index => $item) {
                        $values['branchnum'] = $item['branchnum'] + 1;
                        $count               += $item->allowField(true)->isUpdate(true)->save($values);
                    }
                    Db::commit();
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($count) {
                    $job                      = new AddJob();
                    $param['admin_id']        = $this->auth->isLogin() ? $this->auth->id : 0;
                    $param['username']        = $this->auth->isLogin() ? $this->auth->username : __('Unknown');
                    $param['task']            = 'sendMessage';
                    $param['ip']              = request()->ip();
                    $param['data']['type']    = 1;
                    $param['data']['workers'] = $worker;
                    $param['data']['title']   = '分配客户通知';
                    $param['data']['content'] = '系统为您分配了新客户，请尽快添加';
                    $param['data']['url']     = addon_url('fastscrm/batch/index', [], false, true);
                    $param['data']['btnTxt']  = '查看详情';
                    $job->add($param);
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }

    /**
     * 获取导入数据
     */
    protected function getImport()
    {
        $file = $this->request->request('file');
        if (!$file) {
            $this->error(__('Parameter %s can not be empty', 'EXCEL'));
        }
        $filePath = ROOT_PATH . DS . 'public' . DS . $file;
        if (!is_file($filePath)) {
            $this->error(__('No results were found'));
        }
        //实例化reader
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        if (!in_array($ext, ['csv', 'xls', 'xlsx'])) {
            $this->error(__('Unknown data format'));
        }
        if ($ext === 'csv') {
            $file     = fopen($filePath, 'r');
            $filePath = tempnam(sys_get_temp_dir(), 'import_csv');
            $fp       = fopen($filePath, "w");
            $n        = 0;
            while ($line = fgets($file)) {
                $line     = rtrim($line, "\n\r\0");
                $encoding = mb_detect_encoding($line, ['utf-8', 'gbk', 'latin1', 'big5']);
                if ($encoding != 'utf-8') {
                    $line = mb_convert_encoding($line, 'utf-8', $encoding);
                }
                if ($n == 0 || preg_match('/^".*"$/', $line)) {
                    fwrite($fp, $line . "\n");
                } else {
                    fwrite($fp, '"' . str_replace(['"', ','], ['""', '","'], $line) . "\"\n");
                }
                $n++;
            }
            fclose($file) || fclose($fp);

            $reader = new Csv();
        } elseif ($ext === 'xls') {
            $reader = new Xls();
        } else {
            $reader = new Xlsx();
        }

        //导入文件首行类型,默认是注释,如果需要使用字段名称请使用name
        $importHeadType = isset($this->importHeadType) ? $this->importHeadType : 'comment';

        $table    = $this->model->getQuery()->getTable();
        $database = \think\Config::get('database.database');
        $fieldArr = [];
        $list     = db()->query("SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?",
            [$table, $database]);
        foreach ($list as $k => $v) {
            if ($importHeadType == 'comment') {
                $fieldArr[$v['COLUMN_COMMENT']] = $v['COLUMN_NAME'];
            } else {
                $fieldArr[$v['COLUMN_NAME']] = $v['COLUMN_NAME'];
            }
        }

        //加载文件
        $insert = [];
        try {
            if (!$PHPExcel = $reader->load($filePath)) {
                $this->error(__('Unknown data format'));
            }
            $currentSheet    = $PHPExcel->getSheet(0);  //读取文件中的第一个工作表
            $allColumn       = $currentSheet->getHighestDataColumn(); //取得最大的列号
            $allRow          = $currentSheet->getHighestRow(); //取得一共有多少行
            $maxColumnNumber = Coordinate::columnIndexFromString($allColumn);
            $fields          = [];
            for ($currentRow = 1; $currentRow <= 1; $currentRow++) {
                for ($currentColumn = 1; $currentColumn <= $maxColumnNumber; $currentColumn++) {
                    $val      = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                    $fields[] = $val;
                }
            }

            for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
                $values = [];
                for ($currentColumn = 1; $currentColumn <= $maxColumnNumber; $currentColumn++) {
                    $val      = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                    $values[] = is_null($val) ? '' : $val;
                }
                $row  = [];
                $temp = array_combine($fields, $values);
                foreach ($temp as $k => $v) {
                    if (isset($fieldArr[$k]) && $k !== '') {
                        $row[$fieldArr[$k]] = $v;
                    }
                }
                if ($row) {
                    $insert[] = $row;
                }
            }
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
        if (!$insert) {
            $this->error(__('No rows were updated'));
        }

        try {
            //是否包含admin_id字段
            $has_admin_id = false;
            foreach ($fieldArr as $name => $key) {
                if ($key == 'admin_id') {
                    $has_admin_id = true;
                    break;
                }
            }
            if ($has_admin_id) {
                $auth = Auth::instance();
                foreach ($insert as &$val) {
                    if (!isset($val['admin_id']) || empty($val['admin_id'])) {
                        $val['admin_id'] = $auth->isLogin() ? $auth->id : 0;
                    }
                }
            }
            return $insert;

        } catch (PDOException $exception) {
            $msg = $exception->getMessage();
            $this->error($msg);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * 指定员工提醒
     */
    public function send()
    {
        $worker_id = $this->request->post("worker_id");
        $job                      = new AddJob();
        $param['admin_id']        = $this->auth->isLogin() ? $this->auth->id : 0;
        $param['username']        = $this->auth->isLogin() ? $this->auth->username : __('Unknown');
        $param['task']            = 'sendMessage';
        $param['ip']              = request()->ip();
        $param['data']['type']    = 1;
        $param['data']['workers'] = $worker_id;
        $param['data']['title']   = '分配客户通知';
        $param['data']['content'] = '系统为您分配了新客户，请尽快添加';
        $param['data']['url']     = addon_url('fastscrm/batch/index', [], false, true);
        $param['data']['btnTxt']  = '查看详情';
        $job->add($param);
        $this->success('提醒任务已挂起');
    }

}
