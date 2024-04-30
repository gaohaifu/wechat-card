<?php

namespace app\admin\controller\fastscrm\risk;

use addons\fastscrm\library\WeWork;
use app\common\controller\Backend;
use think\Db;
use think\exception\PDOException;

/**
 * 敏感词管理
 *
 * @icon fa fa-circle-o
 */
class Intercept extends Backend
{

    /**
     * Intercept模型对象
     * @var \app\admin\model\fastscrm\risk\Intercept
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\risk\Intercept;
        $this->view->assign("typedataList", $this->model->getTypedataList());
        $this->view->assign("interceptTypeList", $this->model->getInterceptTypeList());
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
                ->with('group')
                ->where($where)
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
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isPost()) {
            $params  = $this->request->post();
            $workers = [];
            $departs = [];
            if ($params['typedata'] == '2') {
                $departs                                     = explode(',', $params['departs']);
                $data['applicable_range']['department_list'] = $departs;
            } else {
                $workers                               = array_values(array_unique(explode(',', $params['workers'])));
                $workers                               = array_slice($workers, 0, 1000);
                $data['applicable_range']['user_list'] = $workers;
            }
            $params['word_list']    = str_replace('，', ',', $params['word_list']);
            $work                   = new WeWork('App');
            $data['rule_name']      = $params['rule_name'];
            $data['word_list']      = explode(',', $params['word_list']);
            $data['semantics_list'] = isset($params['semantics_list']) ? $params['semantics_list'] : array();
            $data['intercept_type'] = $params['intercept_type'];
            $result                 = $work->addInterceptRule($data);
            if (!empty($result)) {
                unset($params['departs']);
                unset($params['workers']);
                $params['rule_id']        = $result['rule_id'];
                $params['semantics_list'] = isset($params['semantics_list']) ? implode(',',
                    $params['semantics_list']) : '';
                $params['creater']        = $this->auth->isLogin() ? $this->auth->username : __('Unknown');
                $params['createtime']     = time();
                $id                       = $this->model->insert($params, false, true);
                foreach ($workers as $worker) {
                    $worker_temp['worker_id']    = $worker;
                    $worker_temp['intercept_id'] = $id;
                    $worker_temp['createtime']   = time();
                    Db::name('fastscrm_intercept_workers')->insert($worker_temp);
                }
                foreach ($departs as $depart) {
                    $depart_temp['depart_id']    = $depart;
                    $depart_temp['intercept_id'] = $id;
                    $depart_temp['createtime']   = time();
                    Db::name('fastscrm_intercept_departs')->insert($depart_temp);
                }
            }
            $this->success();
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $ids = $ids ? $ids : $this->request->request('id');
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isPost()) {
            $params  = $this->request->post();
            $workers = [];
            $departs = [];
            if ($params['typedata'] == '2') {
                $departs                                         = explode(',', $params['departs']);
                $data['add_applicable_range']['department_list'] = $departs;
            } else {
                $workers                                   = array_values(array_unique(explode(',',
                    $params['workers'])));
                $workers                                   = array_slice($workers, 0, 1000);
                $data['add_applicable_range']['user_list'] = $workers;
            }
            $params['word_list']    = str_replace('，', ',', $params['word_list']);
            $work                   = new WeWork('App');
            $data['rule_id']        = $row->rule_id;
            $data['rule_name']      = $params['rule_name'];
            $data['word_list']      = explode(',', $params['word_list']);
            $data['semantics_list'] = isset($params['semantics_list']) ? $params['semantics_list'] : array();
            $data['intercept_type'] = $params['intercept_type'];
            $old_workers            = Db::name('fastscrm_intercept_workers')->where('intercept_id',
                $row->id)->column('worker_id');
            $old_departs            = Db::name('fastscrm_intercept_departs')->where('intercept_id',
                $row->id)->column('depart_id');
            if (!empty($old_workers)) {
                $data['remove_applicable_range']['user_list'] = $old_workers;
            }
            if (!empty($old_departs)) {
                $data['remove_applicable_range']['department_list'] = $old_departs;
            }
            $result = $work->editInterceptRule($data);
            if (!empty($result)) {
                unset($params['departs']);
                unset($params['workers']);
                $params['semantics_list'] = isset($params['semantics_list']) ? implode(',',
                    $params['semantics_list']) : '';
                $params['updatetime']     = time();
                $this->model->where('id', $row->id)->update($params);
                Db::name('fastscrm_intercept_workers')->where('intercept_id', $row->id)->delete();
                foreach ($workers as $worker) {
                    $worker_temp['worker_id']    = $worker;
                    $worker_temp['intercept_id'] = $row->id;
                    $worker_temp['createtime']   = time();
                    Db::name('fastscrm_intercept_workers')->insert($worker_temp);
                }
                Db::name('fastscrm_intercept_departs')->where('intercept_id', $row->id)->delete();
                foreach ($departs as $depart) {
                    $depart_temp['depart_id']    = $depart;
                    $depart_temp['intercept_id'] = $row->id;
                    $depart_temp['createtime']   = time();
                    Db::name('fastscrm_intercept_departs')->insert($depart_temp);
                }
            }
            $this->success();
        }
        $workers      = Db::name('fastscrm_intercept_workers')->where('intercept_id', $row->id)->select();
        $workers_list = [];
        foreach ($workers as $worker) {
            $user = Db::name('fastscrm_worker')->where('userid', $worker['worker_id'])->find();
            if (!empty($user)) {
                $workers_list[] = $user;
            }
        }
        $departs      = Db::name('fastscrm_intercept_departs')->where('intercept_id', $row->id)->select();
        $departs_list = [];
        foreach ($departs as $depart) {
            $temp = Db::name('fastscrm_depart')->where('depart_id', $depart['depart_id'])->find();
            if (!empty($temp)) {
                $departs_list[] = $temp;
            }
        }
        $row->semantics_list = explode(',', $row->semantics_list);
        $row->workers        = $workers_list;
        $row->departs        = $departs_list;
        $this->assignconfig("row", $row);
        return $this->view->fetch();
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if (!$this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ? $ids : $this->request->post("ids");
        if ($ids) {
            $pk       = $this->model->getPk();
            $adminIds = $this->getDataLimitAdminIds();
            if (is_array($adminIds)) {
                $this->model->where($this->dataLimitField, 'in', $adminIds);
            }
            $list  = $this->model->where($pk, 'in', $ids)->select();
            $work  = new WeWork('App');
            $count = 0;
            Db::startTrans();
            try {
                foreach ($list as $k => $v) {
                    $work->deleteInterceptRule(array('rule_id' => $v->rule_id));
                    $count += $v->delete();
                    Db::name('fastscrm_intercept_workers')->where('intercept_id', $v->id)->delete();
                    Db::name('fastscrm_intercept_departs')->where('intercept_id', $v->id)->delete();
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
                $this->success();
            } else {
                $this->error(__('No rows were deleted'));
            }
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }
}
