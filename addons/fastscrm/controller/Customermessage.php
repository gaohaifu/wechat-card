<?php

namespace addons\fastscrm\controller;

use addons\fastscrm\library\Common;
use addons\fastscrm\library\WeWork;
use think\Db;

class Customermessage extends Base
{
    protected $model = null;

    /**
     * 获取配置、初始化AJAX请求过来的用户的身份等
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\sale\Groupmessage;
        $this->view->assign('title', '客户群发');

    }


    /**
     * 查看
     */
    public function index()
    {
        $work = new WeWork('H5');
        $url = addon_url('fastscrm/customermessage/index', [], false, true);
        $sdkConfig = $work->getSdkConfig($url);
        $this->view->assign('sdkConfig', json_encode($sdkConfig));
        return $this->view->fetch();
    }


    /**
     * 新建我的欢迎语
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $material = new \app\admin\model\fastscrm\material\Item;
            $userInfo = $this->auth->getUserinfo();
            $common = new Common();
            $worker = $common->getWorker($userInfo['id']);
            $params = $this->request->post();
            switch ($params['typeData']['value']) {
                case '1':
                    if (empty($params['image_url'])) {
                        return json(array('code' => -1, 'msg' => '请上传图片'));
                    }
                    $data['title'] = $params['title'];
                    $data['type'] = $params['typeData']['value'];
                    $data['image'] = $params['image_url'];
                    break;
                case '3':
                    if (empty($params['image_url'])) {
                        return json(array('code' => -1, 'msg' => '请上传封面'));
                    }
                    $data['title'] = $params['title'];
                    $data['type'] = $params['typeData']['value'];
                    $data['image'] = $params['image_url'];
                    $data['link_url'] = $params['link_url'];
                    $data['remark'] = $params['remark'];
                    break;
                case '5':
                    if (empty($params['video_url'])) {
                        return json(array('code' => -1, 'msg' => '请上传视频'));
                    }
                    $data['title'] = $params['title'];
                    $data['type'] = $params['typeData']['value'];
                    $data['video'] = $params['video_url'];
                    break;
                case '6':
                    if (empty($params['image_url'])) {
                        return json(array('code' => -1, 'msg' => '请上传封面'));
                    }
                    $data['title'] = $params['title'];
                    $data['type'] = $params['typeData']['value'];
                    $data['image'] = $params['image_url'];
                    $data['appid'] = $params['appid'];
                    $data['path'] = $params['path'];
                    break;
                case '7':
                    if (empty($params['file_url'])) {
                        return json(array('code' => -1, 'msg' => '请上传文件'));
                    }
                    $data['title'] = $params['title'];
                    $data['type'] = $params['typeData']['value'];
                    $data['file'] = $params['file_url'];
                    break;
            }
            $data['worker_id'] = $worker['id'];
            $data['creater'] = $worker['name'];
            $data['createtime'] = time();
            $data['updatetime'] = time();
            $item_id = $material->insert($data, false, true);
            //处理媒体信息
            $item = $material->get($item_id);
            $item['domain']= cdnurl('',true);
            $work = new WeWork('App');
            $item = $work->dealMedia($item);
            if(!empty($item['image'])){
                $item['image'] = cdnurl($item['image'], true);
            }

            return json(array('code' => 1 ,'item' => $item));
        }
    }



    /**
     * 上传文件
     */
    public function upload()
    {
        return action('api/common/upload');
    }
}
