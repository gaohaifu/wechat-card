<?php

namespace app\myadmin\controller\fastscrm\crm;

use addons\fastscrm\library\Job\AddJob;
use app\myadmin\controller\fastscrm\Scrmbackend;
use fast\Http;

/**
 * 客户群管理
 *
 * @icon fa fa-circle-o
 */
class Groupchat extends Scrmbackend
{

    /**
     * Groupchat模型对象
     * @var \app\admin\model\fastscrm\crm\Groupchat
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\crm\Groupchat;
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
                $row->worker_name =   \app\admin\model\fastscrm\company\Worker::where('userid',$row->owner)->column('name');
                $row->member_total =   \app\admin\model\fastscrm\crm\Groupuser::where('group_id',$row->id)->count();
                $row->admin_total =   \app\admin\model\fastscrm\crm\Groupadmin::where('group_id',$row->id)->count();


            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 同步群列表
     */
    public function sync()
    {
        $job =  new AddJob();
        $param['admin_id'] = $this->auth->isLogin() ? $this->auth->id : 0;
        $param['username'] = $this->auth->isLogin() ? $this->auth->username : __('Unknown');
        $param['task'] = 'groupChat';
        $param['ip'] = request()->ip();
        $job->add($param);
        $this->success('同步任务已挂起');
    }
}
