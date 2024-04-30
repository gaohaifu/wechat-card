<?php

namespace addons\fastscrm\controller;

use addons\fastscrm\library\Common;
use addons\fastscrm\library\WeWork;
use think\Db;


class Index extends Base
{
    /**
     * 获取配置、初始化AJAX请求过来的用户的身份等
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->view->assign('title', '工作台');

    }

    /**
     * 查看
     */
    public function index()
    {
        $work = new WeWork('H5');
        $url = addon_url('fastscrm/index', [], false, true);
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
            $department = explode(',', $worker['department']);
            $worker['department'] = Db::name('fastscrm_depart')->where('depart_id', $department[0])->value('name');
            $starttime = strtotime(date('Y-m-d', time()));
            $endtime = $starttime + 86400;
            $worker['cus_num'] = Db::name('fastscrm_customer')
                ->where('fl_userid', $worker['userid'])
                ->whereTime('fl_createtime', 'between', [$starttime, $endtime])
                ->count();

            $worker['cus_lose'] = Db::name('fastscrm_customer_lose')
                ->where('fl_userid', $worker['userid'])
                ->whereTime('createtime', 'between', [$starttime, $endtime])
                ->count();
            $ranks = Db::name('fastscrm_customer')
                ->field('fl_userid,count(1) as count,fl_userid')
                ->group('fl_userid')
                ->limit(10)
                ->select();
            $common = new Common();
            $ranks = $common->multi_array_sort($ranks, 'count', SORT_DESC);
            foreach ($ranks as &$rank) {
                $user =  Db::name('fastscrm_worker')->where('userid',
                    $rank['fl_userid'])->field('name,avatar')->find();
                $rank['avatar']=$user['avatar'];
                $rank['name']=$user['name'];
            }
            unset($rank);
            $result = array('worker' => $worker, 'ranks' => $ranks);

            return json($result);
        }
    }
}
