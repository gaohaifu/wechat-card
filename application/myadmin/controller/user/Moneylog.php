<?php

namespace app\myadmin\controller\user;

use addons\myadmin\library\Backend;

/**
 * 余额日志
 *
 * @icon fa fa-money
 */
class Moneylog extends Backend
{

    /**
     * Log模型对象
     * @var \app\admin\model\user\money\Log
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \addons\myadmin\model\UserMoneyLog;
        $param_key = ['user_id'];
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
            $list = $this->model->with(['user'])->where($where)->order($sort, $order)->paginate($limit);
            $result = array("total" => $list->total(), "rows" => $list->items());
            return json($result);
        }
        return $this->view->fetch();
    }
    public function add()
    {
        if ($this->request->isPost()) {
            $row = $this->request->post("row/a");
            $user_id = isset($row['user_id']) ? $row['user_id'] : 0;
            $money = isset($row['money']) ? $row['money'] : 0;
            $memo = isset($row['memo']) ? $row['memo'] : '';
            if (!$user_id) {
                $this->error("请选择会员");
            }
            if ($money == 0) {
                $this->error("请设置变动金额");
            }
            \addons\myadmin\model\UserBase::money($user_id, $money,  $memo);
            $this->success("添加成功");
        }
        return parent::add();
    }
}
