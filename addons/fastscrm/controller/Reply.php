<?php

namespace addons\fastscrm\controller;

use addons\fastscrm\library\WeWork;
use think\Db;

class Reply extends Base
{
    protected $model = null;

    /**
     * 获取配置、初始化AJAX请求过来的用户的身份等
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\convert\Replyitem;
        $this->view->assign('title', '快捷回复');

    }


    /**
     * 查看
     */
    public function index()
    {
        $work = new WeWork('H5');
        $url = addon_url('fastscrm/reply/index', [], false, true);
        $sdkConfig = $work->getSdkConfig($url);
        $this->view->assign('sdkConfig', json_encode($sdkConfig));
        return $this->view->fetch();
    }

    /**
     * 分组
     */
    public function getgrouplist()
    {
        if ($this->request->isPost()) {
            $list = \app\admin\model\fastscrm\convert\Replygroup::select();
            return json(array('list' => $list));
        }

    }

    /**
     * 话术列表
     */
    public function getData()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isPost()) {
            $groupId = $this->request->post('groupId');
            $keyword = $this->request->post('keyword');
            $limit = 10;
            $where = [];
            if (!empty($keyword)) {
                $where['title|content'] = ['like', '%' . $keyword . '%'];
            }
            if ($groupId > 0) {
                $where['group_id'] = $groupId;
            }
            $list = $this->model
                ->where($where)
                ->order('weigh desc')
                ->paginate($limit);
            foreach ($list as $row) {
                $row->admin_nickname = \app\admin\model\Admin::where('id', $row->admin_id)->value('nickname');
                $row->createtime = date('Y-m-d H:i:s', $row->createtime);
                if ($row->typedata == 2) {
                    $row->material = \app\admin\model\fastscrm\material\Item::where('id', $row->item_id)->find();
                    switch ($row->material['type']) {
                        case 1:
                            $row->msgtype = 'image';
                            break;
                        case 3:
                            $row->msgtype = 'news';
                            $row->material['image'] = cdnurl($row->material['image'], true);
                            break;
                        case 5:
                            $row->msgtype = 'video';
                            break;
                        case 6:
                            $row->msgtype = 'miniprogram';
                            $row->material['image'] = cdnurl($row->material['image'], true);
                            break;
                        case 7:
                            $row->material['filename'] = Db::name('attachment')->where('url',
                                $row->material['file'])->value('filename');
                            $row->msgtype = 'file';
                            break;
                    }
                } else {
                    $row->msgtype = 'text';
                }
            }
            $result = array('limit' => $limit, "total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
    }

    /**
     * 话术列表
     */
    public function getMedia()
    {
        $id = $this->request->post('id');
        $item = Db::name('fastscrm_material_item')->where('id', $id)->find();
        $item['domain'] = cdnurl('', true);
        $work = new WeWork('H5');
        $result = $work->dealMedia($item);
        return json(array('item' => $result));
    }

    /**
     * 记录快捷回复分享次数
     */
    public function addShareLog()
    {
        $id = $this->request->post('id');
        $entry = $this->request->post('entry');
        $user_id = $this->request->post('userId');
        $chat_id = $this->request->post('chatId');
        $user = $this->auth->getUser();
        $worker_id = \app\admin\model\fastscrm\company\Worker::where('fauser_id',$user->id)->value('id');
        $data['reply_id'] = $id;
        $data['entry'] = $entry;
        $data['worker_id'] = $worker_id;
        $data['user_id'] = $user_id;
        $data['chat_id'] = $chat_id;
        $data['createtime'] = time();
        \app\admin\model\fastscrm\convert\Replylog::insert($data);
        return json();
    }
}
