<?php

namespace app\myadmin\controller\general;

use addons\epay\library\Service;
use addons\myadmin\library\Backend;
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
    protected $companyModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \addons\myadmin\model\CompanyWithdraw;
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
            $company_id = COMPANY_ID;
            $money = $params['money'];
            $type = isset($params['type']) ? $params['type'] : 'bank';
            $name = $params['name'];
            $account = $params['account'];
            $memo = $params['memo'];
            $rs = \addons\myadmin\model\CompanyWithdraw::apply($company_id, $money, $type, $name, $account, $memo);
            if ($rs !== true) {
                return  $this->error($rs);
            }
            return $this->success('申请提现成功');
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
            if ($row['status'] !== 'created') {
                $this->error(__('当前状态禁止操作'));
            }
            // 过滤字段
            $this->excludeFields = ['money', 'settledmoney', 'handrate', ' handfee', ' taxerate', 'taxefee'];
            return parent::edit($ids);
        }
        $company = $this->companyModel->get($this->auth->company_id);
        $this->view->assign("company", $company);
        $this->assignconfig("company", $company);
        return parent::edit($ids);
    }

    public function  del($ids = "")
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
