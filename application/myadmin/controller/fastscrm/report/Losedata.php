<?php

namespace app\myadmin\controller\fastscrm\report;

use app\myadmin\controller\fastscrm\Scrmbackend;
use think\Db;

/**
 * 客户流失统计
 */
class Losedata extends Scrmbackend
{

    /**
     * Losedata模型对象
     * @var \app\admin\model\fastscrm\report\Losedata
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\report\Losedata;
        $this->view->assign("typeList", $this->model->getTypeList());
        $this->view->assign("genderList", $this->model->getGenderList());
        $this->view->assign("flAddWayList", $this->model->getFlAddWayList());
        $this->view->assign("delTypeList", $this->model->getDelTypeList());
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
                $fl_tags   =   json_decode($row->fl_tags);
                if(!empty($fl_tags)){
                    $fl_tags    =   implode(',',$fl_tags);
                    $tags      = Db::name('fastscrm_tag')->where('tag_id', 'in' ,$fl_tags)->select();
                    $row->tags    = $tags;
                }else{
                    $row->tags    = [];
                }
                $fl_user_name      = Db::name('fastscrm_worker')->where('userid', $row->fl_userid)->value('name');
                $row->fl_user_name    = $fl_user_name;
            }
            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

}
