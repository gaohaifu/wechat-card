<?php


namespace addons\fastscrm\controller\api;

use addons\fastscrm\library\Common;
use addons\fastscrm\library\Message;
use addons\fastscrm\library\WeWork;
use think\Db;

class Task extends Base
{
    protected $noNeedLogin = [];

    protected $userInfo = false;

    public function _initialize()
    {
        parent::_initialize();
    }


    /**
     * 查询任务
     */
    public function list()
    {
        $page = $this->request->post('page');
        $keyword = $this->request->post('keyword');
        $type = $this->request->post('type');
        $userid = $this->request->post('userid');
        $result = $this->checkauth($userid);
        if (!empty($result)) {
            $pagesize = 20;
            $type == 0 ? $basetable = 'fastscrm_customer_sale' : $basetable = 'fastscrm_moments_sale';
            $list = Db::name($basetable)
                ->where('title', 'like', '%' . $keyword . '%')
                ->where('creater', $userid)
                ->order('createtime', 'desc')
                ->page($page, $pagesize)
                ->select();
            $this->success('ok', [
                'list' => $list,
                'pagesize' => $pagesize,
                'total' => count($list)
            ]);
        } else {
            $this->error('非法用户');
        }

    }

    /**
     *  创建任务基础信息
     *
     */
    public function addinfo()
    {
        $userid = $this->request->post('userid');
        $storeid = $this->request->post('storeid');
        $result = $this->checkauth($userid);
        if (!empty($result)) {
            $workers = Db::name('fastscrm_worker')
                ->where('find_in_set(:id,store_id)', ['id' => $storeid])
                ->select();
            foreach ($workers as &$worker) {
                $worker['check'] = false;
            }
            $items = Db::name('fastscrm_material_item')
                ->select();
            $this->success('ok', [
                'items' => array($items),
                'workers' => $workers,
            ]);
        } else {
            $this->error('非法用户');
        }

    }

    public function submit()
    {
        $workers = $this->request->post('workers');
        $workers = json_decode(htmlspecialchars_decode($workers), true);
        $title = $this->request->post('title');
        $content = $this->request->post('content');
        $type = $this->request->post('type');
        $choseitem = $this->request->post('choseitem');
        $creater = $this->request->post('userid');
        $result = $this->checkauth($creater);
        if (!empty($result)) {
            $insdata = array();
            $insdata['title'] = $title;
            $insdata['content'] = $content;
            $insdata['creater'] = $creater;
            $insdata['item_id'] = $choseitem;
            $insdata['status'] = 1;
            $insdata['createtime'] = time();
            $insdata['updatetime'] = time();
            foreach ($workers as $worker) {
                if ($worker['check']) {
                    $insdata['worker_id'][] = $worker['userid'];
                }
            }
            $insdata['worker_id'] = implode(',', $insdata['worker_id']);


            if ($type == 0) {
                Db::name('fastscrm_customer_sale')
                    ->insert($insdata, false, true);
            } else {
                Db::name('fastscrm_moments_sale')
                    ->insert($insdata, false, true);
            }

            $this->success('ok', [
            ]);
        } else {
            $this->error('非法用户');
        }
    }

    /**
     * 执行任务
     */
    public function taskPost()
    {
        $id = $this->request->post('id');
        $type = $this->request->post('type');
        $userid = $this->request->post('userid');
        $result = $this->checkauth($userid);
        if (!empty($result)) {
            $param['admin_id'] = 0;
            $param['username'] = __('Unknown');
            $param['id'] = $id;
            if ($type == 0) {
                $this->customerSale($param);
            } else {
                $this->momentsSale($param);
            }

            $this->success('ok', [
            ]);
        } else {
            $this->error('非法用户');
        }

    }

    /**
     * 客户消息群发
     */
    protected function customerSale($param)
    {
        $admin_id = $param['admin_id'];
        $username = $param['username'];
        $id = $param['id'];
        $task = Db::name('fastscrm_customer_sale')->where('id', $id)->find();
        $item = Db::name('fastscrm_material_item')->where('id', $task['item_id'])->find();
        $work = new WeWork('App');

        //处理媒体信息
        $item['domain']= cdnurl('',true);
        $item = $work->dealMedia($item);
        //消息推送
        switch ($task['typedata']) {
            case 1:
                $owners = explode(',', $task['worker_id']);
                break;
            case 2:
                $common = new Common();
                $departids = $common->dealDepart($task['depart_id']);
                $owners = array();
                foreach ($departids as $departid) {
                    $workers = Db::name('fastscrm_worker')->where('status',
                        '1')->where('find_in_set(:depart_id,department)', ['depart_id' => $departid])->column('userid');
                    $owners = array_merge($owners, $workers);

                }
                break;
            case 3:
                $groups = Db::name('fastscrm_group_chat')->field('owner')->column('owner');
                $stores = explode(',', $task['store_id']);
                foreach ($stores as $store) {
                    $workers = Db::name('fastscrm_worker')->where('status',
                        '1')->where('find_in_set(:store_id,store_id)', ['store_id' => $store])->column('userid');
                    foreach ($workers as $worker) {
                        if (in_array($worker, $groups)) {
                            $owners[] = $worker;
                        }
                    }
                }
                break;
        }
        $params['attachments'] = array();
        switch ($item['type']) {
            case 0:
                $params['text'] = array('content' => $task['content']);
                break;
            case 1:
                $params['text'] = array('content' => $task['content']);
                if (!empty($item)) {
                    $params['attachments'][] = array(
                        'msgtype' => "image",
                        'image' => array(
                            "media_id" => $item['media_id'],
                        )
                    );
                }
                break;
            case 3:
                $params['text'] = array('content' => $task['content']);
                if (!empty($item)) {
                    $params['attachments'][] = array(
                        'msgtype' => "link",
                        'link' => array(
                            "title" => $item['title'],
                            "picurl" => cdnurl($item['image'], true),
                            "desc" => $item['remark'],
                            "url" => $item['link_url'],
                        )
                    );
                }
                break;
            case 5:
                $params['text'] = array('content' => $task['content']);
                if (!empty($item)) {
                    $params['attachments'][] = array(
                        'msgtype' => "video",
                        'video' => array(
                            "media_id" => $item['media_id'],
                        )
                    );
                }
                break;
            case 6:
                $params['text'] = array('content' => $task['content']);
                if (!empty($item)) {
                    $params['attachments'][] = array(
                        'msgtype' => "miniprogram",
                        'miniprogram' => array(
                            "title" => $item['title'],
                            "pic_media_id" => $item['media_id'],
                            "appid" => $item['appid'],
                            "page" => $item['path'],
                        )
                    );
                }
                break;
            case 7:
                $params['text'] = array('content' => $task['content']);
                if (!empty($item)) {
                    $params['attachments'][] = array(
                        'msgtype' => "file",
                        'file' => array(
                            "media_id" => $item['media_id'],
                        )
                    );
                }
                break;
        }
        $params['chat_type'] = 'single';
        $message = new Message();
        $owners = array_values(array_unique($owners));
        foreach ($owners as $owner) {
            $params['sender'] = $owner;
            $result = $work->messageSend($params, $admin_id, $username, request()->ip());
            $data['sale_id'] = $task['id'];
            $data['userid'] = $owner;
            $data['type'] = 2;
            $data['createtime'] = time();
            if ($result['errcode'] !== 0) {
                $data['status'] = 0;
                $data['message'] = $message->getError($result['errcode']);
            } else {
                $data['msgid'] = $result['msgid'];
                $data['status'] = 1;
                $data['message'] = '执行成功';
            }
            $mycustomers = Db::name('fastscrm_customer')->where('fl_userid',
                $owner)->field('external_userid')->select();
            foreach ($mycustomers as $mygroup) {
                $data['external_userid'] = $mygroup['external_userid'];
                Db::name('fastscrm_sale_log')->insert($data);
            }
        }

        return true;
    }

    /**
     * 朋友圈消息
     */
    protected function momentsSale($param)
    {

        $admin_id = $param['admin_id'];
        $username = $param['username'];
        $id = $param['id'];
        $task = Db::name('fastscrm_moments_sale')->where('id', $id)->find();
        $items = Db::name('fastscrm_material_item')->where('id', 'in', $task['item_id'])->select();
        $work = new WeWork('App');

        //处理媒体信息
        foreach ($items as &$item) {
            $item['domain']= cdnurl('',true);
            $item = $work->fj_dealMedia($item);
        }
        unset($item);
        //消息推送
        $params['visible_range']['sender_list'] = array();
        switch ($task['typedata']) {
            case 1:
                $owners = explode(',', $task['worker_id']);
                $owners = array_values(array_unique($owners));
                $params['visible_range']['sender_list']['user_list'] = $owners;
                break;
            case 2:
                $departs = explode(',', $task['depart_id']);
                $params['visible_range']['sender_list']['department_list'] = $departs;
                break;
            case 3:
                $groups = Db::name('fastscrm_group_chat')->field('owner')->column('owner');
                $stores = explode(',', $task['store_id']);
                foreach ($stores as $store) {
                    $workers = Db::name('fastscrm_worker')->where('status',
                        '1')->where('find_in_set(:store_id,store_id)', ['store_id' => $store])->column('userid');
                    foreach ($workers as $worker) {
                        if (in_array($worker, $groups)) {
                            $owners[] = $worker;
                        }
                    }
                }
                break;
        }
        $params['attachments'] = array();
        foreach ($items as $item) {
            switch ($item['type']) {
                case 0:
                    $params['text'] = array('content' => $task['content']);
                    break;
                case 1:
                    $params['text'] = array('content' => $task['content']);
                    $params['attachments'][] = array(
                        'msgtype' => "image",
                        'image' => array(
                            "media_id" => $item['fj_media_id'],
                        )
                    );
                    break;
                case 3:
                    $params['text'] = array('content' => $task['content']);
                    $params['attachments'][] = array(
                        'msgtype' => "link",
                        'link' => array(
                            "title" => $item['title'],
                            "url" => $item['link_url'],
                            "media_id" => $item['fj_media_id'],
                        )
                    );
                    break;
                case 5:
                    $params['text'] = array('content' => $task['content']);
                    $params['attachments'][] = array(
                        'msgtype' => "video",
                        'video' => array(
                            "media_id" => $item['fj_media_id'],
                        )
                    );
                    break;
                case 6:
                    $params['text'] = array('content' => $task['content']);
                    $params['attachments'][] = array(
                        'msgtype' => "miniprogram",
                        'miniprogram' => array(
                            "title" => $item['title'],
                            "pic_media_id" => $item['fj_media_id'],
                            "appid" => $item['appid'],
                            "page" => $item['path'],
                        )
                    );
                    break;
                case 7:
                    $params['text'] = array('content' => $task['content']);
                    $params['attachments'][] = array(
                        'msgtype' => "file",
                        'file' => array(
                            "media_id" => $item['fj_media_id'],
                        )
                    );
                    break;

            }
        }

        $message = new Message();
        $result = $work->momentSend($params, $admin_id, $username, request()->ip());
        if ($result['errcode'] !== 0) {
            $data['status'] = 0;
            $data['message'] = $message->getError($result['errcode']);
        } else {
            $data['jobid'] = $result['jobid'];
            $data['status'] = 1;
        }
        foreach ($owners as $owner) {
            $data['sale_id'] = $task['id'];
            $data['userid'] = $owner;
            $data['createtime'] = time();
            Db::name('fastscrm_moments_sale_log')->insert($data);
        }

        return true;
    }


    public function taskDelete()
    {
        $id = $this->request->post('id');
        $type = $this->request->post('type');
        $userid = $this->request->post('userid');
        $result = $this->checkauth($userid);
        if (!empty($result)) {
            $type == 0 ? $basetable = 'fastscrm_customer_sale' : $basetable = 'fastscrm_moments_sale';

            Db::name($basetable)
                ->where('id', $id)
                ->delete();
            $this->success('ok', [
            ]);
        } else {
            $this->error('非法用户');
        }

    }


}