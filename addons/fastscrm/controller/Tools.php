<?php

namespace addons\fastscrm\controller;

use addons\fastscrm\library\Common;
use addons\fastscrm\library\WeWork;
use think\Db;


class Tools extends Base
{
    /**
     * 获取配置、初始化AJAX请求过来的用户的身份等
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->view->assign('title', '运营工具');

    }

    /**
     * 查看
     */
    public function index()
    {
        $work = new WeWork('H5');
        $url = addon_url('fastscrm/tools/index', [], false, true);
        $sdkConfig = $work->getSdkConfig($url);
        $this->view->assign('sdkConfig', json_encode($sdkConfig));
        return $this->view->fetch();
    }

    /**
     * 工作台信息
     */
    public function getData()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isPost()) {
            $userInfo = $this->auth->getUserinfo();
            $common = new Common();
            $worker = $common->getWorker($userInfo['id']);
            $item['cus_num'] = Db::name('fastscrm_customer')
                ->where('fl_userid', $worker['userid'])
                ->count();
            $item['cus_total'] = Db::name('fastscrm_customer')
                ->where('fl_userid', $worker['userid'])
                ->count();
            $starttime = strtotime(date('Y-m-d', time()));
            $endtime = $starttime + 86400;
            $item['cus_num'] = Db::name('fastscrm_customer')
                ->where('fl_userid', $worker['userid'])
                ->whereTime('fl_createtime', 'between', [$starttime, $endtime])
                ->count();

            $item['cus_lose'] = Db::name('fastscrm_customer_lose')
                ->where('fl_userid', $worker['userid'])
                ->whereTime('createtime', 'between', [$starttime, $endtime])
                ->count();
            $result = array('item' => $item);

            return json($result);
        }
    }
}
