<?php

namespace app\admin\controller\myadmin\auth;

use app\common\controller\Backend;

/**
 * 企业身份
 *
 * @icon fa fa-address-book
 */
class Player extends Backend
{
    protected $model = null;

    protected $noNeedRight = ['selectpage'];
    protected $selectpageFields = 'id,type,name,label,price';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \addons\myadmin\model\AuthPlayer;

        $statusList = $this->model->getStatusList();
        $this->view->assign("statusList", $statusList);
        $this->assignconfig("statusList", $statusList);

        $param_key = ['type'];
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
                ->order($sort, $order)
                ->paginate($limit);
            foreach ($list as $ov) {
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }
    
    public function add()
    {
        $nodeList = \addons\myadmin\model\AuthRule::getTreeList();
        $this->assign("nodeList", $nodeList);
        return parent::add();
    }
    

    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $rules = explode(',', $row['rules']);
        $nodeList = \addons\myadmin\model\AuthRule::getTreeList($rules);
        $this->assign("nodeList", $nodeList);
        return parent::edit($ids);
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
