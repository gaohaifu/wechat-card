<?php

namespace app\myadmin\controller\fastscrm\company;

use addons\fastscrm\library\Job\AddJob;
use addons\fastscrm\library\WeWork;
use app\myadmin\controller\fastscrm\Scrmbackend;
use fast\Http;
use fast\Tree;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use Exception;
/**
 * 部门管理
 *
 * @icon fa fa-circle-o
 */
class Depart extends Scrmbackend
{

    /**
     * Depart模型对象
     * @var \app\admin\model\fastscrm\company\Depart
     */
    protected $model = null;
    protected $rulelist = [];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\company\Depart;
        // 必须将结果集转换为数组
        $ruleList = collection($this->model->where('company_id',COMPANY_ID)->order('parentid ASC,order DESC')->select())->toArray();
        Tree::instance()->init($ruleList, 'parentid');
        $this->rulelist = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'name');
        $ruledata = [];
        foreach ($this->rulelist as $k => &$v) {
            $ruledata[$v['id']] = $v['name'];
        }


        $this->view->assign('ruledata', $ruledata);
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
        if ($this->request->isAjax()) {
            $list = $this->rulelist;
            $total = count($this->rulelist);

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

      public function find()
      {
            //设置过滤方法
            $this->request->filter(['strip_tags', 'trim']);
            if ($this->request->isAjax()) {
                  //如果发送的来源是Selectpage，则转发到Selectpage
                  if ($this->request->request('keyField')) {

                      return  $this->selectpage();
                  }
                  list($where, $sort, $order, $offset, $limit) = $this->buildparams();

                  $list = $this->model
                      ->where($where)
                      ->order('depart_id', 'asc')
                      ->paginate($limit);

                  $result = array("total" => $list->total(), "rows" => $list->items());

                  return json($result);
            }
            return $this->view->fetch();
      }

      public function searchfind()
      {
            $departs  = Db::name('fastscrm_depart')->where('id','>', 0)->select();
            $searchlist = [];
            foreach ($departs as $key => $value) {
                  $searchlist[] = ['id' => $value['depart_id'], 'name' => $value['name']];
            }
            $data = ['searchlist' => $searchlist];
            $this->success('', null, $data);
      }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $work = new WeWork();
                $depart_id = $work->createDepart($params);
                $params['depart_id'] = $depart_id;
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
     * 编辑
     */
    public function edit($ids = null)
    {
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
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                $work = new WeWork();
                $work->updateDepart($params);
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
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
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
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
            $pk = $this->model->getPk();
            $adminIds = $this->getDataLimitAdminIds();
            if (is_array($adminIds)) {
                $this->model->where($this->dataLimitField, 'in', $adminIds);
            }
            $list = $this->model->where($pk, 'in', $ids)->select();
            $work = new WeWork();
            $count = 0;
            Db::startTrans();
            try {
                foreach ($list as $k => $v) {

                    $work->deleteDepart($v->depart_id);
                    $count += $v->delete();
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
    /**
     * 真实删除
     */
    public function destroy($ids = "")
    {
        if (!$this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ? $ids : $this->request->post("ids");
        $pk = $this->model->getPk();
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            $this->model->where($this->dataLimitField, 'in', $adminIds);
        }
        if ($ids) {
            $this->model->where($pk, 'in', $ids);
        }
        $count = 0;
        Db::startTrans();
        try {
            $list = $this->model->onlyTrashed()->select();
            foreach ($list as $k => $v) {
                $count += $v->delete(true);
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
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }


    /**
     * 期初同步
     */
    public function start()
    {
        $job =  new AddJob();
        $param['admin_id'] = $this->auth->isLogin() ? $this->auth->id : 0;
        $param['username'] = $this->auth->isLogin() ? $this->auth->username : __('Unknown');
        $param['task'] = 'start';
        $param['ip'] = request()->ip();
        $job->add($param);
        $this->success('期初同步任务已挂起');
    }

    /**
     * 同步企微部门
     */
    public function sync()
    {
        $job =  new AddJob();
        $param['admin_id'] = $this->auth->isLogin() ? $this->auth->id : 0;
        $param['username'] = $this->auth->isLogin() ? $this->auth->username : __('Unknown');
        $param['task'] = 'depart';
        $param['ip'] = request()->ip();
        $job->add($param);
        $this->success('同步任务已挂起');
    }

}
