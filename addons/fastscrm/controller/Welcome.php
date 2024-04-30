<?php

namespace addons\fastscrm\controller;

use addons\fastscrm\library\Common;
use addons\fastscrm\library\WeWork;
use think\Db;

class Welcome extends Base
{
    protected $model = null;

    /**
     * 获取配置、初始化AJAX请求过来的用户的身份等
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\sale\Welcome;
        $this->view->assign('title', '欢迎语');

    }


    /**
     * 查看
     */
    public function index()
    {
        $work = new WeWork('H5');
        $url = addon_url('fastscrm/welcome/index', [], false, true);
        $sdkConfig = $work->getSdkConfig($url);
        $userInfo = $this->auth->getUserinfo();
        $common = new Common();
        $worker = $common->getWorker($userInfo['id']);
        $this->view->assign('user', $worker);
        $this->view->assign('sdkConfig', json_encode($sdkConfig));
        return $this->view->fetch();
    }

    /**
     * 我的欢迎语
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
                ->order('updatetime', 'desc')
                ->paginate($limit);
            $now = $this->getMyWelcome();
            foreach ($list as &$item) {
                if ($item['id'] == $now['id']) {
                    $item['is_now'] = true;
                } else {
                    $item['is_now'] = false;
                }
                $item['content'] = str_replace('{{员工姓名}}', $worker['name'], $item['content']);
                $item['content'] = str_replace('{{客户昵称}}', 'XXX', $item['content']);
                $item['material'] = \app\admin\model\fastscrm\material\Item::where('id', $item['item_id'])->find();
                switch ($item['material']['type']) {
                    case 1:
                        $item['msgtype'] = 'image';
                        break;
                    case 3:
                        $item['msgtype'] = 'news';
                        $item['material']['image'] = cdnurl($item['material']['image'], true);
                        break;
                    case 5:
                        $item['msgtype'] = 'video';
                        break;
                    case 6:
                        $item['msgtype'] = 'miniprogram';
                        $item['material']['image'] = cdnurl($item['material']['image'], true);
                        break;
                    case 7:
                        $item['material']['filename'] = Db::name('attachment')->where('url',
                            $item['material']['file'])->value('filename');
                        $filesize = Db::name('attachment')->where('url',
                            $item['material']['file'])->value('filesize');
                        $item['material']['filesize'] = intval($filesize / 1024);
                        $item['msgtype'] = 'file';
                        break;
                }
            }
            unset($item);

            $result = array('limit' => $limit, "total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
    }

    /**
     * 企业欢迎语
     */
    public function coCodes()
    {
        if ($this->request->isPost()) {
            $limit = 20;
            $userInfo = $this->auth->getUserinfo();
            $common = new Common();
            $worker = $common->getWorker($userInfo['id']);
            $list = $this->model
                ->where('worker_id', '=', '')
                ->order('updatetime', 'desc')
                ->paginate($limit);
            $now = $this->getMyWelcome();
            foreach ($list as &$item) {
                if ($item['id'] == $now['id']) {
                    $item['is_now'] = true;
                } else {
                    $item['is_now'] = false;
                }
                $item['content'] = str_replace('{{员工姓名}}', $worker['name'], $item['content']);
                $item['content'] = str_replace('{{客户昵称}}', 'XXX', $item['content']);
                $item['material'] = \app\admin\model\fastscrm\material\Item::where('id', $item['item_id'])->find();
                switch ($item['material']['type']) {
                    case 1:
                        $item['msgtype'] = 'image';
                        break;
                    case 3:
                        $item['msgtype'] = 'news';
                        $item['material']['image'] = cdnurl($item['material']['image'], true);
                        break;
                    case 5:
                        $item['msgtype'] = 'video';
                        break;
                    case 6:
                        $item['msgtype'] = 'miniprogram';
                        $item['material']['image'] = cdnurl($item['material']['image'], true);
                        break;
                    case 7:
                        $item['material']['filename'] = Db::name('attachment')->where('url',
                            $item['material']['file'])->value('filename');
                        $filesize = Db::name('attachment')->where('url',
                            $item['material']['file'])->value('filesize');
                        $item['material']['filesize'] = intval($filesize / 1024);
                        $item['msgtype'] = 'file';
                        break;
                }
            }
            unset($item);
            $result = array('limit' => $limit, "total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
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

            $welcome['title'] = $params['title'];
            $welcome['content'] = $params['content'];
            $welcome['item_id'] = $item_id;
            $welcome['user_id'] = $worker['userid'];
            $welcome['worker_id'] = $worker['id'];
            $welcome['creater'] = $worker['name'];
            $welcome['createtime'] = time();
            $welcome['updatetime'] = time();
            $this->model->insert($welcome);

            return json(array('code' => 1));
        }
    }


    /**
     * 查询当前我的欢迎语
     */
    public function getMyWelcome()
    {
        $userInfo = $this->auth->getUserinfo();
        $common = new Common();
        $worker = $common->getWorker($userInfo['id']);
        return Db::name('fastscrm_sale_welcome')->where('find_in_set(:id,user_id)',
            ['id' => $worker['userid']])->order('updatetime', 'desc')->find();
    }

    /**
     * 设置我的欢迎语
     */
    public function setWelcome()
    {
        if ($this->request->isPost()) {
            $id = $this->request->post('id');
            $item = Db::name('fastscrm_sale_welcome')->where('id', $id)->find();
            if (!empty($item)) {
                $user_id = explode(',', $item['user_id']);
                $userInfo = $this->auth->getUserinfo();
                $common = new Common();
                $worker = $common->getWorker($userInfo['id']);
                $user_id[] = $worker['userid'];
                $user_id = implode(',', $user_id);
                Db::name('fastscrm_sale_welcome')->where('id', $id)->update([
                    'user_id' => $user_id,
                    'updatetime' => time()
                ]);
                return json(array('code' => 1));
            } else {
                return json(array('code' => -1, 'msg' => '当前欢迎语不存在'));
            }


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
