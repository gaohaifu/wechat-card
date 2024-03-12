<?php

namespace app\admin\controller\myadmin\company;

use app\common\controller\Backend;

/**
 * 余额日志
 *
 * @icon fa fa-money
 */
class Moneylog extends Backend
{

    /**
     * Log模型对象
     * @var \addons\myadmin\model\Usermoney\Log
     */
    protected $model = null;
    protected $searchFields = 'memo,company.name';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \addons\myadmin\model\CompanyMoneyLog;
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
        return $this->view->fetch();
    }
    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $row = $this->request->post("row/a");
            $company_id = isset($row['company_id']) ? $row['company_id'] : 0;
            $money = isset($row['money']) ? $row['money'] : 0;
            $memo = isset($row['memo']) ? $row['memo'] : '';
            if (!$company_id || !$money) {
                $this->error("金额和会员ID不能为空");
            }
            $rs = \addons\myadmin\model\CompanyBase::money($money, $company_id, $memo);
            if($rs){                
                $this->success("添加成功");
            }
            $this->error("操作失败");
        }
        return parent::add();
    }
}
