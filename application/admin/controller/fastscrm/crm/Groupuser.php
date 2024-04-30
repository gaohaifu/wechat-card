<?php

namespace app\admin\controller\fastscrm\crm;

use app\admin\controller\fastscrm\Scrmbackend;

/**
 * 群成员
 *
 * @icon fa fa-circle-o
 */
class Groupuser extends Scrmbackend
{

    /**
     * Groupuser模型对象
     * @var \app\admin\model\fastscrm\crm\Groupuser
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\crm\Groupuser;
        $this->view->assign("typeList", $this->model->getTypeList());
        $this->view->assign("joinSceneList", $this->model->getJoinSceneList());
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
            $group_id = $this->request->get('group_id');
            if($group_id>0){
                $list = $this->model
                    ->where($where)
                    ->where('group_id',$group_id)
                    ->order($sort, $order)
                    ->paginate($limit);
            }else{
                $list = $this->model
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);
            }

            foreach ($list as $row) {
                if($row->type==2){
                    $row->avatar    =    \app\admin\model\fastscrm\crm\Customer::where('external_userid',$row->userid)->limit(1)->column('avatar');
                }else{
                    $row->avatar    =    \app\admin\model\fastscrm\company\Worker::where('userid',$row->userid)->limit(1)->column('avatar');
                }
                $row->chat_name    =    \app\admin\model\fastscrm\crm\Groupchat::where('id',$row->group_id)->column('name');

            }
            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }


}
