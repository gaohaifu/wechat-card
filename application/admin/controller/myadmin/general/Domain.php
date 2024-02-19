<?php

namespace app\admin\controller\myadmin\general;

use app\common\controller\Backend;
use Exception;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use addons\myadmin\library\helper\Common;

/**
 * 域名管理
 *
 * @icon fa fa-circle-o
 */
class Domain extends Backend
{
    protected $model = null;
    protected $searchFields = 'name';
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \addons\myadmin\model\Domain;

        $statusList = $this->model->getStatusList();

        $this->view->assign("statusList", $statusList);
        $this->assignconfig("statusList", $statusList);

        $company_id = $this->request->param('company_id');
        $this->assignconfig('company_id', $company_id);
        $this->view->assign('company_id', $company_id);
    }

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
                ->with(['company'])
                ->order($sort, $order)
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $row = $this->model->get($params['name']);
            if ($row) {
                $this->error(__('域名已经使用'));
            }
            if ($params) {
                $params = $this->preExcludeFields($params);

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    $result = $this->model->allowField(true)->save($params);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }

    /**
     * 安装证书
     */
    public function install()
    {
        $config = get_addon_config('myadmin');
        $ssldir = isset($config['ssldir']) && $config['ssldir']?$config['ssldir']:RUNTIME_PATH . "ssl/";
        $domain_id = $this->request->param('domain_id');
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if (!$row = $this->model->get($params['domain_id'])) {
                $this->error(__('No Results were found'));
            }
            $save_dir = $params['install_dir'] ? $params['install_dir'] :  $ssldir. $row['name'];
            if (!$row['ssl_certificate'] || !$row['ssl_certificate_key']) {
                $this->error(__('找不到证书文件', ''));
            }
            $ssl_certificate = cdnurl($row['ssl_certificate'], true);
            $ssl_certificate_key = cdnurl($row['ssl_certificate_key'], true);
            $ssl = Common::getFile($ssl_certificate, $save_dir,  'fullchain.pem', 1);
            $ssl_key = Common::getFile($ssl_certificate_key, $save_dir, 'privkey.pem', 1);
            if ($ssl && $ssl_key) {
                $row->save(['install' => 'yes', 'install_dir' => $save_dir]);
                $this->success();
            }
            $this->error(__('安装失败', ''));
        }
        $this->view->assign("ssldir", $ssldir);
        $this->view->assign("domain_id", $domain_id);
        return $this->view->fetch();
    }


    /**
     * Selectpage搜索
     *
     * @internal
     */
    public function selectpage()
    {
        return parent::selectpage();
    }
}
