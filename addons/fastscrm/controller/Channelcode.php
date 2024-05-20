<?php

namespace addons\fastscrm\controller;

use addons\fastscrm\library\Common;
use addons\fastscrm\library\Message;
use addons\fastscrm\library\WeWork;
use think\Db;

class Channelcode extends Base
{
    protected $model = null;

    /**
     * 获取配置、初始化AJAX请求过来的用户的身份等
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\guide\Channelcode;
        $this->view->assign('title', '渠道活码');

    }


    /**
     * 查看
     */
    public function index()
    {
        $work = new WeWork('H5');
        $url = addon_url('fastscrm/channelcode/index', [], false, true);
        $sdkConfig = $work->getSdkConfig($url);
        $this->view->assign('sdkConfig', json_encode($sdkConfig));
        return $this->view->fetch();
    }

    /**
     * 我的渠道活码
     */
    public function myCodes()
    {
        if ($this->request->isPost()) {
            $limit = 20;
            $userInfo = $this->auth->getUserinfo();
            $common = new Common();
            $worker = $common->getWorker($userInfo['id']);
            $list = $this->model
                ->where('worker_id', $worker['id'])
                ->where('scene', 2)
                ->paginate($limit);
            $result = array('limit' => $limit, "total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
    }

    /**
     * 企业渠道活码
     */
    public function coCodes()
    {
        if ($this->request->isPost()) {
            $limit = 20;
            $list = $this->model
                ->where('worker_id', '=','')
                ->where('scene', 2)
                ->paginate($limit);
            $result = array('limit' => $limit, "total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
    }

    /**
     * 新建我的渠道活码
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params    = $this->request->post();
            $workers = array_values(array_unique(explode(',', $params['workers'])));
            if($params['type']==2){
                $workers = array_slice($workers,0,100);
            }else{
                $workers = array_slice($workers,0,1);
            }
            $tags = explode(',', $params['tags']);
            $work = new WeWork('App');
            $data['type'] = $params['type'];
            $data['scene'] = 2;
            $data['remark'] = $params['remark'];
            $data['state'] = $params['name'];
            $data['skip_verify'] = filter_var($params['skip_verify'], FILTER_VALIDATE_BOOLEAN);
            $data['is_exclusive'] = filter_var($params['is_exclusive'], FILTER_VALIDATE_BOOLEAN);
            $data['user'] = $workers;
            $result = $work->appContactWay($data);
            if ($result['errcode'] !== 0) {
                $message = new Message();
                return json(array('code'=>-1,'msg'=>$message->getError($result['errcode'])));
            } else {
                unset($params['tags']);
                unset($params['tagsText']);
                unset($params['workers']);
                unset($params['workersText']);
                $params['config_id'] = $result['config_id'];
                $params['qr_code'] = isset($result['qr_code'])?$result['qr_code']:'';
                $params['skip_verify'] = filter_var($params['skip_verify'], FILTER_VALIDATE_BOOLEAN)?1:0;
                $params['is_exclusive'] = filter_var($params['is_exclusive'], FILTER_VALIDATE_BOOLEAN)?1:0;
                $userInfo = $this->auth->getUserinfo();
                $common = new Common();
                $worker = $common->getWorker($userInfo['id']);
                $params['worker_id'] = $worker['id'];
                $params['creater'] = $worker['name'];
                $params['createtime'] = time();
                $id = $this->model->insert($params, false, true);
                foreach ($workers as $worker) {
                    $worker_temp['worker_id'] = $worker;
                    $worker_temp['code_id'] = $id;
                    $worker_temp['createtime'] = time();
                    Db::name('fastscrm_channel_workers')->insert($worker_temp);
                }
                foreach ($tags as $tag) {
                    $tag_temp['tag_id'] = $tag;
                    $tag_temp['code_id'] = $id;
                    $tag_temp['createtime'] = time();
                    Db::name('fastscrm_channel_tags')->insert($tag_temp);
                }
            }

            return json(array('code'=>1));
        }
    }
}
