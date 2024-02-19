<?php

namespace app\admin\controller\myadmin\company;

use addons\epay\library\Service;
use app\common\controller\Backend;
use think\Exception;
use Yansongda\Pay\Pay;

/**
 * 提现管理
 *
 * @icon fa fa-circle-o
 */
class Withdraw extends Backend
{

    protected $model = null;
    protected $company = null;
    protected $searchFields = 'name,orderid,account,company.name';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \addons\myadmin\model\CompanyWithdraw;
        $this->company = new \addons\myadmin\model\Company;
        $this->view->assign("statusList", $this->model->getStatusList());

        // 类型
        $typeList = $this->model->getTypeList();
        $typeList_key = array_keys($typeList);
        $default_type = reset($typeList_key);
        $this->view->assign("default_type", $default_type);
        $this->view->assign("typeList", $typeList);
        $this->assignconfig("typeList", $typeList);
    }

    /**
     * 查看
     */
    public function index()
    {
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->with(['company'])
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['company'])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

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
            $params = $this->request->post("row/a");
            if ($params['money'] < 100) {
                $this->error(__('提现金额最少100元'));
            }
            $company_id = $params['company_id'];
            $money = $params['money'];
            $name = $params['name'];
            $type = isset($params['type']) ? $params['type'] : 'bank';
            $account = $params['account'];
            $memo = $params['memo'];
            $rs = \addons\myadmin\model\CompanyWithdraw::apply($company_id, $money, $type, $name, $account, $memo);
            if ($rs !== true) {
                return  $this->error($rs);
            }
            return $this->success('申请提现成功');
        }

        $company_id = $this->company->value('id');
        $company = $this->company->get($this->request->param('company_id', $company_id));
        if (!$company) {
            $company['id'] = '0';
            $company['money'] = '0.00';
            $company['settledmoney'] = '0.00';
            $company['handfee'] = '0.00';
            $company['handrate'] = '0.00';
            $company['taxefee'] = '0.00';
            $company['taxerate'] = '0.00';
        }
        $info = ['account' => '', 'name' => '', 'memo' => ''];
        $this->view->assign("info", $info);
        $this->view->assign("company", $company);
        $this->assignconfig("company", $company);
        return parent::add();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($row['status'] != 'created') {
                $this->excludeFields = ['status'];
            }
            if ($params['status'] == 'rejected' && $row['status'] == 'created' && !$row['transactionid']) {
                \addons\myadmin\model\CompanyBase::money($row['money'], $row['company_id'], '拒绝提现');
            }
            return parent::edit($ids);
        }
        return parent::edit($ids);
    }
}
