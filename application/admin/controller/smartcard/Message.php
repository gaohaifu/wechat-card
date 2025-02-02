<?php

namespace app\admin\controller\smartcard;

use app\common\controller\Backend;

/**
 * 消息管理
 *
 * @icon fa fa-circle-o
 */
class Message extends Backend
{
    
    /**
     * Message模型对象
     * @var \app\admin\model\smartcard\Message
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\smartcard\Message;
        $this->view->assign("statusdataList", $this->model->getStatusdataList());
    }

    public function import()
    {
        parent::import();
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
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            
            $admin_id = $this->auth->id;
            $company_id = $this->auth->company_id;
            $wheres['message.company_id'] = $company_id;
            if($admin_id!=1){
                $list = $this->model
                    ->with(['smartcardcompany','user'])
                    ->where($where)
                    ->where($wheres)
                    ->order($sort, $order)
                    ->paginate($limit);
            }else{
                $list = $this->model
                    ->with(['smartcardcompany','user'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);
            }


            

            foreach ($list as $row) {
                
                $row->getRelation('smartcardcompany')->visible(['name']);
				$row->getRelation('user')->visible(['nickname']);
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
        $admin_id = $this->auth->id;
        $company_id = $this->auth->company_id;
        
        if($admin_id!=1){
            $company_id = $company_id;
        }else{
            $company_id = '';
        }
       
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
        
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
        $this->view->assign("company_id", $company_id);
        return $this->view->fetch();
    }

}