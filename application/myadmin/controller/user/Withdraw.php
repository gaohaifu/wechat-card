<?php

namespace app\myadmin\controller\user;

use addons\myadmin\library\Backend;
use think\Exception;
use Yansongda\Pay\Pay;
use addons\myadmin\model\UserWithdraw;
use addons\myadmin\model\UserBase;

/**
 * 提现管理
 *
 * @icon fa fa-circle-o
 */
class Withdraw extends Backend
{

    protected $model = null;
    protected $companyModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \addons\myadmin\model\UserWithdraw;
        $this->companyModel = new \addons\myadmin\model\Company;
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
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['user'])
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
            // 检测余额
            $money_sum = $this->model->where('status', 'created')->where('company_id', COMPANY_ID)->sum('money');
            $company_money =  $this->companyModel->where('id', COMPANY_ID)->value('money');
            if ($company_money < $money_sum + $params['money']) {
                $has_money = $company_money - $money_sum;
                $this->error('余额不足，最多可提￥' . ($has_money > 0 ? $has_money : 0.00) . '元');
            }
            if ($params['status'] != 'created') {
                $this->error(__('非法操作'));
            }
            return parent::add();
        }
        $company = $this->companyModel->get($this->auth->company_id);
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
            if ($row['status'] != 'created') {
                $this->excludeFields = ['status'];
            }
            $params = $this->request->post("row/a");
            if ($params['status'] == 'rejected' && $row['status'] == 'created' && !$row['transactionid']) {
                UserBase::money($row['company_user_id'], $row['money'], '拒绝提现');
            }
            return parent::edit($ids);
        }
        return parent::edit($ids);
    }

    public function del($ids = "")
    {
        if ($this->request->isPost()) {
            $pk = $this->model->getPk();
            $list = $this->model->where($pk, 'in', $ids)->where('status', 'rejected')->where('company_id', COMPANY_ID)->column($pk);
            if ($list) {
                return parent::del($list);
            }
            $this->error(__('删除失败'));
        }
        $this->error(__('非法操作'));
    }
}
