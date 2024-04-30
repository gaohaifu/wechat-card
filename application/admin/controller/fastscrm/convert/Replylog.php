<?php

namespace app\myadmin\controller\fastscrm\convert;

use app\common\controller\Backend;

/**
 * 话术分享日志
 *
 * @icon fa fa-circle-o
 */
class Replylog extends Backend
{

    /**
     * Replylog模型对象
     * @var \app\admin\model\fastscrm\convert\Replylog
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\convert\Replylog;
        $this->view->assign("entryList", $this->model->getEntryList());
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
            $reply_id = $this->request->get('reply_id');
            if($reply_id>0){
                $list = $this->model
                    ->where($where)
                    ->where('reply_id',$reply_id)
                    ->order($sort, $order)
                    ->paginate($limit);
            }else{
                $list = $this->model
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);
            }

            foreach ($list as $row) {
                $row->reply_name = \app\admin\model\fastscrm\convert\Replyitem::where('id',$row->reply_id)->value('title');
                $row->worker_name = \app\admin\model\fastscrm\company\Worker::where('id',$row->worker_id)->value('name');
                $row->chat_name = \app\admin\model\fastscrm\crm\Groupchat::where('chat_id',$row->chat_id)->value('name');
                $row->user_name = \app\admin\model\fastscrm\crm\Customer::where('external_userid',$row->user_id)->value('name');
            }
            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }
}
