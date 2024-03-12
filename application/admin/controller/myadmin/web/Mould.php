<?php

namespace app\admin\controller\myadmin\web;

use app\common\controller\Backend;

/**
 * 模型管理
 *
 * @icon fa fa-circle-o
 */
class Mould extends Backend
{
    protected $model = null;
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \addons\myadmin\model\WebMould;
        $statusList = $this->model->getStatusList();
        $this->view->assign("statusList", $statusList);
        $this->assignconfig("statusList", $statusList);
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
