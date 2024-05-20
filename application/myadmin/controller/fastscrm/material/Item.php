<?php

namespace app\myadmin\controller\fastscrm\material;

use app\myadmin\controller\fastscrm\Scrmbackend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use Exception;
/**
 * 素材管理
 *
 * @icon fa fa-circle-o
 */
class Item extends Scrmbackend
{

    /**
     * Item模型对象
     * @var \app\admin\model\fastscrm\material\Item
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\material\Item;
        $this->view->assign("typeList", $this->model->getTypeList());
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

                  $filter = $this->request->get("filter", '');
                  $filter = (array)json_decode($filter, true);

                  $list = $this->model
                      ->with(["group"])
                      ->where($where)
                      ->order($sort, $order)
                      ->paginate($limit);

                  $result = array("total" => $list->total(), "rows" => $list->items());

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
                        return $this->selectpage();
                  }
                  list($where, $sort, $order, $offset, $limit) = $this->buildparams();

                  $filter = $this->request->get("filter", '');
                  $filter = (array)json_decode($filter, true);

                  $list = $this->model
                      ->with(["group"])
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
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = false;
                $params['creater'] = $this->auth->isLogin() ? $this->auth->username : __('Unknown');
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
      public function searchfind()
      {
            $departs  = Db::name('fastscrm_material_item')->where('id','>', 0)->select();

            $searchlist = [];
            foreach ($departs as $key => $value) {
                  $searchlist[] = ['id' => $value['id'], 'name' => $value['title']];
            }
            $data = ['searchlist' => $searchlist];
            $this->success('', null, $data);
      }


}
