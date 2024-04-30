<?php

namespace app\admin\controller\fastscrm\crm;

use addons\fastscrm\library\Job\AddJob;
use app\common\controller\Backend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 离职继承
 *
 * @icon fa fa-circle-o
 */
class Resigned extends Backend
{

    /**
     * Resigned模型对象
     * @var \app\admin\model\fastscrm\crm\Resigned
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\crm\Resigned;
        $this->view->assign("typeList", $this->model->getTypeList());
        $this->view->assign("statusList", $this->model->getStatusList());
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post();
            if ($params) {
                $temp['type']                = $params['type'];
                $temp['createtime']          = time();
                $temp['handover_userid']     = $params['handover_workers']['userid'];
                $temp['handover_name']       = $params['handover_workers']['name'];
                $temp['handover_department'] = $params['handover_workers']['depart_name'];
                $temp['takeover_userid']     = $params['takeover_workers']['userid'];
                $temp['takeover_name']       = $params['takeover_workers']['name'];
                $temp['takeover_department'] = $params['takeover_workers']['depart_name'];
                $id                          = $this->model->insert($temp, false, true);
                if (!empty($params['customers'])) {
                    $customers = explode(',', $params['customers']);
                    foreach ($customers as $customer) {
                        \app\admin\model\fastscrm\crm\Resignedcustomer::insert([
                            'resigned_id' => $id,
                            'external_userid' => $customer,
                            'createtime' => time()
                        ]);
                    }
                }
                if (!empty($params['chats'])) {
                    $chats = explode(',', $params['chats']);
                    foreach ($chats as $chat) {
                        \app\admin\model\fastscrm\crm\Resignedchat::insert([
                            'resigned_id' => $id,
                            'chat_id' => $chat,
                            'createtime' => time()
                        ]);
                    }
                }

                $this->success();
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }

    /**
     * 执行任务
     */
    public function action($ids)
    {
        $job =  new AddJob();
        $param['admin_id'] = $this->auth->isLogin() ? $this->auth->id : 0;
        $param['username'] = $this->auth->isLogin() ? $this->auth->username : __('Unknown');
        $param['ip'] = request()->ip();
        $param['id'] = $ids;
        $param['domain'] = cdnurl('', true);
        $row = $this->model->get(['id' => $ids]);
        if($row->type=='customer'){
            $param['task'] = 'resignedCustomer';
        }else{
            $param['task'] = 'resignedChat';
        }
        $result = $job->add($param);
        if ($result){
            $this->model->where(['id' => $ids])->update(['status'=>1]);
            $this->success('继承任务已挂起');
        }else{
            $this->error('执行失败');
        }
    }

    /**
     * 同步客户接替状态
     */
    public function sync($ids)
    {
        $row = $this->model->get(['id' => $ids]);
        if($row->status!=1){
            $this->error('请先执行任务');
        }
        $job =  new AddJob();
        $param['admin_id'] = $this->auth->isLogin() ? $this->auth->id : 0;
        $param['username'] = $this->auth->isLogin() ? $this->auth->username : __('Unknown');
        $param['ip'] = request()->ip();
        $param['id'] = $ids;
        $param['domain'] = cdnurl('', true);
        $param['task'] = 'resignedCustomerSync';
        $result = $job->add($param);
        if ($result){
            $this->model->where(['id' => $ids])->update(['status'=>1]);
            $this->success('同步客户接替状态任务已挂起');
        }else{
            $this->error('同步失败');
        }
    }
}
