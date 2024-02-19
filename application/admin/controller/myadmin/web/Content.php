<?php

namespace app\admin\controller\myadmin\web;

use app\common\controller\Backend;
use addons\myadmin\model\WebMould;
use addons\myadmin\model\WebCategory;


/**
 * 内容管理
 */
class Content extends Backend
{
    protected $noNeedRight = ['selectpage'];
    protected $selectpageFields = 'id,name,title,company_id';
    protected $searchFields = 'id,name,title';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \addons\myadmin\model\WebContent;

        $statusList = $this->model->getStatusList();
        $this->view->assign("statusList", $statusList);
        $this->assignconfig("statusList", $statusList);

        $mouldList = WebMould::order('weigh desc')->column('name', 'id');
        $this->view->assign("mouldList", $mouldList);
        $this->assignconfig("mouldList", $mouldList);

        $mould = key($mouldList);
        $mould_id = $this->request->param('mould_id',$mould);
        $this->view->assign("mould_id", $mould_id);
        $this->assignconfig("mould_id", $mould_id);
        

        $typeList = $this->model->getTypeList();
        $this->view->assign("typeList", $typeList);
        $this->assignconfig("typeList", $typeList);

        $category_id = $this->request->param('category_id');
        $this->view->assign("category_id", $category_id);
        $this->assignconfig("category_id", $category_id);

        $company_id = $this->request->param('company_id');
        if ($category_id) {
            $company_id = WebCategory::where('id', $category_id)->value('company_id');
        }
        $this->view->assign("company_id", $company_id);
        $this->assignconfig("company_id", $company_id);
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
                ->with(['category', 'company'])
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
