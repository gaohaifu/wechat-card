<?php

namespace app\admin\controller\fastscrm\sale;

use app\admin\controller\fastscrm\Scrmbackend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 欢迎语
 *
 * @icon fa fa-circle-o
 */
class Welcome extends Scrmbackend
{

    /**
     * Welcome模型对象
     * @var \app\admin\model\fastscrm\sale\Welcome
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\sale\Welcome;

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
                  foreach ($list as $item) {
                        $mitem = Db::name('fastscrm_material_item')->where('id', $item['item_id'])->find();
                        $item->itemname = isset($mitem['title'])?$mitem['title']:'';
                        $workers  = Db::name('fastscrm_worker')->where('userid','in', $item['user_id'])->column('name');
                        $item->workername = implode(',',$workers);
                        if (!empty($item['store_id'])){
                              $store  = Db::name('fastscrm_store')->where('id','in', $item['store_id'])->column('store_name');
                              $item->storename =implode(',',$store);
                        }else{
                              $item->storename ='-';
                        }
                  }
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

}
