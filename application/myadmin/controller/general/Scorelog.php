<?php

namespace app\myadmin\controller\general;

use addons\myadmin\library\Backend;
use addons\myadmin\model\Company;

/**
 * 积分日志
 *
 * @icon fa fa-circle-o
 */
class Scorelog extends Backend
{
    protected $model = null;
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \addons\myadmin\model\CompanyScoreLog;
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
                ->with(['company'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);
            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        $score = Company::where('id', COMPANY_ID)->value('score');
        $this->view->assign('score', number_format($score,2));
        return $this->view->fetch();
    }
    public function add()
    {
        $this->error("没有权限");
    }

    public function edit($ids = null)
    {
        $this->error("没有权限");
    }

    public function del($ids = null)
    {
        $this->error("没有权限");
    }
}
