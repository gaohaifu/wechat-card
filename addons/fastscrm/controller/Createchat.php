<?php

namespace addons\fastscrm\controller;

use addons\fastscrm\library\Common;
use addons\fastscrm\library\WeWork;
use think\Db;

class Createchat extends Base
{
    protected $model = null;

    /**
     * 获取配置、初始化AJAX请求过来的用户的身份等
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\crm\Customer;
        $this->view->assign('title', '一键拉群');

    }


    /**
     * 查看
     */
    public function index()
    {
        $work = new WeWork('H5');
        $url = addon_url('fastscrm/createchat/index', [], false, true);
        $sdkConfig = $work->getSdkConfig($url);
        $this->view->assign('sdkConfig', json_encode($sdkConfig));
        return $this->view->fetch();
    }


    /**
     * 满足条件客户数量
     */
    public function getTotal()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isPost()) {
            $where = $this->buildparams($this->request->post());
            $userInfo = $this->auth->getUserinfo();
            $common = new Common();
            $worker = $common->getWorker($userInfo['id']);
            $total = $this->model
                ->where($where)
                ->where('fl_userid', $worker['userid'])
                ->count();
            $result = array("total" => $total);
            return json($result);
        }
    }

    /**
     * 查看满足条件的客户列表
     */
    public function detail()
    {
        if ($this->request->isPost()) {
            $where = $this->buildparams($this->request->post());
            $limit = 20;
            $userInfo = $this->auth->getUserinfo();
            $common = new Common();
            $worker = $common->getWorker($userInfo['id']);
            $list = $this->model
                ->where($where)
                ->where('fl_userid', $worker['userid'])
                ->paginate($limit);
            $result = array('limit' => $limit, "total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
    }

    protected function buildparams($params)
    {
        $where = [];
        foreach ($params as $k=>$param) {
            if($param != ''){
                switch ($k){
                    case 'sex':
                        $where['gender'] = $param;
                        break;
                    case 'add':
                        $where['fl_add_way'] = $param;
                        break;
                    case 'tags':
                        $tags = explode(',',$param);
                        if(count($tags) === 1){
                            $where['fl_tags'] = ['like','%'.$param.'%'];
                        }else{
                            foreach($tags as $k => $v) {
                                $where['fl_tags'][] = ['like','%'.$v.'%'];
                            }
                            $where['fl_tags'][] = 'or';
                        }
                        break;
                    case 'startTime':
                        $where['fl_createtime'] = ['between time', [strtotime($params['startTime']), strtotime(date('Y-m-d 23:59:59',strtotime($params['endTime'])))]];
                        break;
                }
            }
        }
        return $where;
    }
}
