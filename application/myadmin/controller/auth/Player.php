<?php

namespace app\myadmin\controller\auth;

use addons\myadmin\library\Backend;

/**
 * 企业身份
 *
 * @icon fa fa-address-book
 */
class Player extends Backend
{
    protected $model = null;
    protected $selectpageFields = 'id,name,title,company_id';
    protected $searchFields = 'id,name,title';
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \addons\myadmin\model\CompanyPlayer;
        $statusList = $this->model->getStatusList();
        $this->view->assign("statusList", $statusList);
        $this->assignconfig("statusList", $statusList);


        $param_key = ['player_id', 'company_id'];
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
                ->with(['player', 'company'])
                ->order($sort, $order)
                ->paginate($limit);
            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }
    /**
     * 编辑
     */
    public function add()
    {
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        return $this->view->fetch();
    }
    /**
     * 删除
     */
    public function del($ids = '')
    {
        $this->error(__('No rows were deleted'));
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
