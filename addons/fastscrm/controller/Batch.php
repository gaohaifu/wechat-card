<?php

namespace addons\fastscrm\controller;

use addons\fastscrm\library\Common;
use addons\fastscrm\library\WeWork;
use think\Db;

class Batch extends Base
{
    protected $model = null;

    /**
     * 获取配置、初始化AJAX请求过来的用户的身份等
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\guide\Batch;
        $this->view->assign('title', '批量添加客户');

    }


    /**
     * 查看
     */
    public function index()
    {
        $work = new WeWork('H5');
        $url = addon_url('fastscrm/batch/index', [], false, true);
        $sdkConfig = $work->getSdkConfig($url);
        $this->view->assign('sdkConfig', json_encode($sdkConfig));
        return $this->view->fetch();
    }


    /**
     * 话术列表
     */
    public function getData()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isPost()) {
            $groupTab = $this->request->post('groupTab');
            $limit = 30;
            $where = [];
            if ($groupTab > 0) {
                $where['status'] = $groupTab;
            }
            $userInfo = $this->auth->getUserinfo();
            $common = new Common();
            $worker = $common->getWorker($userInfo['id']);
            $list = $this->model
                ->where($where)
                ->where('worker_id', $worker['userid'])
                ->order('id desc')
                ->paginate($limit);
            foreach ($list as $row) {
                $row->createtime = date('Y-m-d H:i:s', $row->createtime);

            }
            $result = array('limit' => $limit, "total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
    }

    /**
     * 修改状态
     */
    public function update()
    {
        if ($this->request->isPost()) {
            $id = $this->request->post('id');
            if ($id > 0) {
                $this->model
                    ->where('id', $id)
                    ->update(['status' => 2]);
            }
            return json();
        }
    }
}
