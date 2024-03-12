<?php

namespace app\admin\controller\myadmin\web;

use app\common\controller\Backend;
use addons\myadmin\model\WebMould;

/**
 * 产品分类
 *
 * @icon fa fa-circle-o
 */
class ProductCategory extends Backend
{
    protected $model = null;
    protected $noNeedRight = ['selectpage'];
    protected $selectpageFields = 'id,name,company_id';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \addons\myadmin\model\WebProductCategory;

        $statusList = $this->model->getStatusList();
        $this->view->assign("statusList", $statusList);
        $this->assignconfig("statusList", $statusList);

        $mouldList = WebMould::order('weigh desc')->column('name', 'id');
        $this->view->assign("mouldList", $mouldList);
        $this->assignconfig("mouldList", $mouldList);

        $mould = key($mouldList);


        $param_key = [];
        foreach ($param_key as $ov) {
            $this->view->assign("{$ov}", $this->request->param($ov));
            $this->assignconfig("{$ov}", $this->request->param($ov));
        }
    }


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
                ->with(['company'])
                ->order($sort, $order)
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * Selectpage搜索
     *
     * @internal
     */
    public function selectpage()
    {
        return parent::selectpage();
    }
}
