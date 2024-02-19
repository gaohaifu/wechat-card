<?php

namespace app\admin\controller\myadmin\company;

use app\common\controller\Backend;

/**
 * 企业分组
 *
 * @icon fa fa-circle-o
 */
class Group extends Backend
{
    protected $model = null;

    protected $noNeedRight = ['selectpage'];
    protected $selectpageFields = 'id,type,name,label,price';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \addons\myadmin\model\CompanyGroup;

        $statusList = $this->model->getStatusList();
        $this->view->assign("statusList", $statusList);
        $this->assignconfig("statusList", $statusList);

        $myadmin_config = get_addon_config('myadmin');
        $typeList = isset($myadmin_config['companytype']) ? $myadmin_config['companytype'] : [];
        $typeList_key = array_keys($typeList);
        $default_type = reset($typeList_key);
        $this->view->assign("default_type", $default_type);

        $this->view->assign("typeList", $typeList);
        $this->assignconfig("typeList", $typeList);

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
                ->with(['company'])
                ->order($sort, $order)
                ->paginate($limit);
            foreach ($list as $ov) {
                $ov->append(['labelname']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }
    
    public function add()
    {
        return parent::add();
    }
    

    public function edit($ids = null)
    {
        if ($this->request->isPost()) {
            return parent::edit($ids);
        }
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
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
