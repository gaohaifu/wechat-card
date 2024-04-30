<?php

namespace app\myadmin\controller\fastscrm\sale;

use app\myadmin\controller\fastscrm\Scrmbackend;
use think\Db;

/**
 * 营销日志管理
 *
 * @icon fa fa-circle-o
 */
class Chatreport extends Scrmbackend
{

    /**
     * Chatreprot模型对象
     * @var \app\admin\model\fastscrm\sale\Chatreport
     */
    protected $model = null;



    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\sale\Chatreport;
        $this->view->assign("typeList", $this->model->getTypeList());
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("sendStatusList", $this->model->getSendStatusList());
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
            $sale_id = $this->request->get('sale_id');
            if($sale_id>0){
                $list = $this->model
                    ->where($where)
                    ->where('sale_id',$sale_id)
                    ->where('type','1')
                    ->order($sort, $order)
                    ->paginate($limit);
            }else{
                $list = $this->model
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);
            }

            foreach ($list as $row) {
                $row->sale_title = Db::name('fastscrm_group_chat_sale')->where('id',$row->sale_id)->value('title');
                $row->owner_name = Db::name('fastscrm_worker')->where('userid',$row->userid)->value('name');
                $group_id = Db::name('fastscrm_group_chat')->where('chat_id',$row->chat_id)->value('id');
                $row->user_total = Db::name('fastscrm_group_user')->where('group_id',$group_id)->count();
                $row->group_name = Db::name('fastscrm_group_chat')->where('id',$group_id)->value('name');
            }
            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

}
