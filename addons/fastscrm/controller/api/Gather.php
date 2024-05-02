<?php

namespace addons\fastscrm\controller\api;

use addons\fastscrm\library\Common;
use addons\fastscrm\library\WeWork;
use app\admin\library\Auth;
use app\admin\model\fastscrm\kf\Account;
use think\Db;
use addons\fastscrm\library\callback\WXBizMsgCrypt;

class Gather extends Base
{
    protected $noNeedLogin = ['*'];

    protected $info = '';

    public function _initialize()
    {
        parent::_initialize();

    }

    public function init()
    {
        $config         = get_fastscrm_config_by_server($_SERVER);
        $corpId         = $config['corp_id'];
        $token          = $config['agent_Token'];
        $encodingAesKey = $config['agent_EncodingAESKey'];
        $wxcpt          = new WXBizMsgCrypt($token, $encodingAesKey, $corpId);


        if (isset($_GET['echostr'])) {
            $sVerifyMsgSig    = $_GET['msg_signature'];
            $sVerifyTimeStamp = $_GET['timestamp'];
            $sVerifyNonce     = $_GET['nonce'];
            $sVerifyEchoStr   = $_GET['echostr'];
            // 需要返回的明文
            $sEchoStr = "";
            $errCode  = $wxcpt->VerifyURL($sVerifyMsgSig, $sVerifyTimeStamp, $sVerifyNonce, $sVerifyEchoStr, $sEchoStr);
            if ($errCode == 0) {
                echo $sEchoStr;
            } else {
                print("ERR: " . $errCode . "\n\n");
            }
            exit;
        } else {
            $sReqMsgSig    = $_GET['msg_signature'];
            $sReqTimeStamp = $_GET['timestamp'];
            $sReqNonce     = $_GET['nonce'];
            $sReqData      = file_get_contents('php://input');
            $sMsg          = "";  // 解析之后的明文
            $errCode       = $wxcpt->DecryptMsg($sReqMsgSig, $sReqTimeStamp, $sReqNonce, $sReqData, $sMsg);
            if ($errCode == 0) {
                $string = $sMsg;
                libxml_disable_entity_loader(true);
                if (preg_match('/(\<\!DOCTYPE|\<\!ENTITY)/i', $string)) {
                    return false;
                }
                $string     = preg_replace('/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f\\x7f]/', '', $string);
                $obj        = simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA, '', false);
                $data       = json_decode(json_encode($obj), true);
                $this->info = $data;
                if ($data['Event'] == 'kf_account_auth_change') {
                    $this->kf_change();
                }
                if ($data['Event'] == 'change_external_contact') {
                    switch ($data['ChangeType']) {
                        case 'add_external_contact':
                            $this->customerAdd();
                            break;
                        case 'edit_external_contact':
                            $this->customerUpdate();
                            break;
                        case 'del_external_contact':
                            $this->customerDelete();
                            break;
                        case 'del_follow_user':
                            $this->userDelete();
                            break;
                    }
                }
                if ($data['Event'] == 'change_external_chat') {
                    switch ($data['ChangeType']) {
                        case 'create':
                            $this->chatCreate();
                            break;
                        case 'update':
                            $this->chatUpdate();
                            break;
                        case 'dismiss':
                            $this->chatDelete();
                            break;

                    }
                }
                if ($data['Event'] == 'change_external_tag') {
                    switch ($data['ChangeType']) {
                        case 'create':
                            $this->tagCreate();
                            break;
                        case 'update':
                            $this->tagUpdate();
                            break;
                        case 'delete':
                            $this->tagDelete();
                            break;
                        case 'shuffle':
//                                        $this->tagShuffle();
                            break;


                    }
                }
            } else {
                print("ERR: " . $errCode . "\n\n");
            }
        }
    }

    /**
     * 客户账号授权变更回调
     */
    public function kf_change()
    {
        $data                 = $this->info;
        $accountModel         = new Account();
        $upDate['updatetime'] = time();
        //新增授权
        if (isset($data['AuthAddOpenKfId'])) {
            if (is_array($data['AuthAddOpenKfId'])) {
                $data['AuthAddOpenKfId'] = implode(',', $data['AuthAddOpenKfId']);
            }
            $upDate['manage_privilege'] = 1;
            $accountModel->where('open_kfid', 'in', $data['AuthAddOpenKfId'])->update($upDate);
        }
        //取消授权
        if (isset($data['AuthDelOpenKfId'])) {
            if (is_array($data['AuthDelOpenKfId'])) {
                $data['AuthDelOpenKfId'] = implode(',', $data['AuthDelOpenKfId']);
            }
            $upDate['manage_privilege'] = '';
            $accountModel->where('open_kfid', 'in', $data['AuthDelOpenKfId'])->update($upDate);
        }
        $this->success('ok', [
            'worker' => 1
        ]);
    }

    /**
     * 添加企业客户事件
     */
    public function customerAdd()
    {
        $info = $this->info;
        //获取客户详细信息
        $work   = new WeWork('App');
        $params = $info['ExternalUserID'];

        $item    = $work->customerget($params);
        $fl_user = array();
        foreach ($item['follow_user'] as $fl) {
            if ($fl['userid'] == $info['UserID']) {
                $fl_user = $fl;
            }
        }
        $common = new Common();
        $data   = [
            'external_userid' => $item['external_contact']['external_userid'],
            'name' => $common->removeEmoji($item['external_contact']['name']),
            'avatar' => $item['external_contact']['avatar'],
            'type' => $item['external_contact']['type'],
            'gender' => $item['external_contact']['gender'],
            'updatetime' => time()
        ];

        isset($fl_user['userid']) ? $data['fl_userid'] = $fl_user['userid'] : '';
        isset($fl_user['remark']) ? $data['fl_remark'] = $common->removeEmoji($fl_user['remark']) : '';
        isset($fl_user['description']) ? $data['fl_description'] = $fl_user['description'] : '';
        isset($fl_user['createtime']) ? $data['fl_createtime'] = $fl_user['createtime'] : '';
        isset($fl_user['add_way']) ? $data['fl_add_way'] = $fl_user['add_way'] : '';
        isset($info['State']) ? $data['state'] = $info['State'] : '';


        if (isset($item['external_contact']['position'])) {
            $data['position'] = $item['external_contact']['position'];
        }
        if (isset($item['external_contact']['corp_name'])) {
            $data['corp_name'] = $item['external_contact']['corp_name'];
        }
        if (isset($item['external_contact']['corp_full_name'])) {
            $data['corp_full_name'] = $item['external_contact']['corp_full_name'];
        }
        if (isset($item['external_contact']['unionid'])) {
            $data['unionid'] = $item['external_contact']['unionid'];
        }
        if (isset($fl_user['tags'])) {
            if (!empty($fl_user['tags'])) {
                foreach ($fl_user['tags'] as $tag) {
                    $data['fl_tags'][] = $tag['tag_id'];
                }

            }
        }
        if (isset($fl_user['remark_mobiles'])) {
            if (!empty($fl_user['remark_mobiles'])) {
                $data['fl_remark_mobiles'] = json_encode($fl_user['remark_mobiles']);
                foreach ($fl_user['remark_mobiles'] as $remark_mobile) {
                    $batch = Db::name('fastscrm_customer_batch')->where('mobile', $remark_mobile)->find();
                    if (!empty($batch)) {
                        Db::name('fastscrm_customer_batch')
                            ->where('id', $batch['id'])
                            ->update(['status' => 3, 'addtime' => $data['fl_createtime']]);
                        $tags                     = explode(',', $batch['tags']);
                        $param['userid']          = $data['fl_userid'];
                        $param['external_userid'] = $data['external_userid'];
                        foreach ($tags as $tag) {
                            $temp[]           = $tag;
                            $param['add_tag'] = $temp;
                            $result           = $work->notifyUsersTags($param);
                            if ($result) {
                                $data['fl_tags'][] = $tag;
                            }
                        }
                    }
                }
            }
        }
        if (isset($data['state'])) {
            $channelcode              = Db::name('fastscrm_channel_code')->where('name',
                $data['state'])->field('id')->find();
            $tags                     = Db::name('fastscrm_channel_tags')->where('code_id',
                $channelcode['id'])->select();
            $param['userid']          = $data['fl_userid'];
            $param['external_userid'] = $data['external_userid'];
            foreach ($tags as $tag) {
                $code_temp[]      = $tag['tag_id'];
                $param['add_tag'] = $code_temp;
                $result           = $work->notifyUsersTags($param);
                if ($result) {
                    $data['fl_tags'][] = $tag['tag_id'];
                }
            }
        }
        if (!empty($data['fl_tags'])) {
            $data['fl_tags'] = json_encode($data['fl_tags']);
        }
        $log = Db::name('fastscrm_customer')->where('external_userid', $data['external_userid'])->where('fl_userid',
            $data['fl_userid'])->find();
        if (empty($log)) {
            Db::name('fastscrm_customer')
                ->insert($data, false, true);
            $this->welcome($info['WelcomeCode'], $info['UserID'], $info['ExternalUserID']);
        }


        $this->success('ok', [
            'worker' => 1
        ]);
    }

    /**
     * 修改企业客户事件
     */
    public function customerUpdate()
    {
        $info = $this->info;
        //获取客户详细信息
        $work    = new WeWork('App');
        $params  = $info['ExternalUserID'];
        $item    = $work->customerget($params);
        $fl_user = array();
        foreach ($item['follow_user'] as $fl) {
            if ($fl['userid'] == $info['UserID']) {
                $fl_user = $fl;
            }
        }
        $common = new Common();
        $data   = [
            'external_userid' => $item['external_contact']['external_userid'],
            'name' => $common->removeEmoji($item['external_contact']['name']),
            'avatar' => $item['external_contact']['avatar'],
            'type' => $item['external_contact']['type'],
            'gender' => $item['external_contact']['gender'],
            'updatetime' => time()
        ];

        isset($fl_user['userid']) ? $data['fl_userid'] = $fl_user['userid'] : '';
        isset($fl_user['remark']) ? $data['fl_remark'] = $common->removeEmoji($fl_user['remark']) : '';
        isset($fl_user['description']) ? $data['fl_description'] = $fl_user['description'] : '';
        isset($fl_user['createtime']) ? $data['fl_createtime'] = $fl_user['createtime'] : '';
        isset($fl_user['add_way']) ? $data['fl_add_way'] = $fl_user['add_way'] : '';


        if (isset($item['external_contact']['position'])) {
            $data['position'] = $item['external_contact']['position'];
        }
        if (isset($item['external_contact']['corp_name'])) {
            $data['corp_name'] = $item['external_contact']['corp_name'];
        }
        if (isset($item['external_contact']['corp_full_name'])) {
            $data['corp_full_name'] = $item['external_contact']['corp_full_name'];
        }
        if (isset($item['external_contact']['unionid'])) {
            $data['unionid'] = $item['external_contact']['unionid'];
        }
        if (isset($fl_user['tags'])) {
            foreach ($fl_user['tags'] as $tag) {
                $data['fl_tags'][] = $tag['tag_id'];
            }

            $data['fl_tags'] = json_encode($data['fl_tags']);


        }
        if (isset($fl_user['remark_mobiles'])) {
            if (!empty($fl_user['remark_mobiles'])) {
                $data['fl_remark_mobiles'] = json_encode($fl_user['remark_mobiles']);

            }
        }
        Db::name('fastscrm_customer')
            ->where('external_userid', $data['external_userid'])
            ->where('fl_userid', $data['fl_userid'])
            ->update($data);

        $this->success('ok', [
            'worker' => 1
        ]);
    }


    /**
     * 员工删除成员事件
     */
    public function customerDelete()
    {

        $data = $this->info;

        $customer = Db::name('fastscrm_customer')
            ->where('external_userid', $data['ExternalUserID'])
            ->where('fl_userid', $data['UserID'])
            ->find();

        $customer['del_type']   = 0;
        $customer['createtime'] = time();
        unset($customer['id']);

        Db::name('fastscrm_customer_lose')
            ->insert($customer, false, true);


        Db::name('fastscrm_customer')
            ->where('external_userid', $data['ExternalUserID'])
            ->where('fl_userid', $data['UserID'])
            ->delete();
        $this->success('ok', [
            'worker' => 1
        ]);
    }

    /**
     * 客户删除员工事件
     */
    public function userDelete()
    {
        $data     = $this->info;
        $customer = Db::name('fastscrm_customer')
            ->where('external_userid', $data['ExternalUserID'])
            ->where('fl_userid', $data['UserID'])
            ->find();

        $customer['del_type']   = 1;
        $customer['createtime'] = time();

        unset($customer['id']);

        Db::name('fastscrm_customer_lose')
            ->insert($customer, false, true);


        Db::name('fastscrm_customer')
            ->where('external_userid', $data['ExternalUserID'])
            ->where('fl_userid', $data['UserID'])
            ->delete();
        $this->success('ok', [
            'worker' => 1
        ]);
    }


    /**
     * 客户群创建事件
     */
    public function chatCreate()
    {
        $info     = $this->info;
        $auth     = Auth::instance();
        $admin_id = $auth->isLogin() ? $auth->id : 0;
        $username = $auth->isLogin() ? $auth->username : 'api通知';
        $work     = new WeWork('App');

        $params = array(
            'chat_id' => $info['ChatId'],
            'need_name' => 1
        );

        $detail = $work->getGroupChatDetail($params, $admin_id, $username);

        $data               = [
            'chat_id' => $params['chat_id'],
            'status' => 0,
        ];
        $common             = new Common();
        $data['name']       = $common->removeEmoji($detail['group_chat']['name']);
        $data['owner']      = $detail['group_chat']['owner'];
        $data['createtime'] = $detail['group_chat']['create_time'];
        if (!empty($detail['group_chat']['notice'])) {
            $data['notice'] = $detail['group_chat']['notice'];
        }
        $data['updatetime'] = time();

        $group_id = Db::name('fastscrm_group_chat')
            ->insert($data, false, true);


        if (!empty($detail['group_chat']['admin_list'])) {
            foreach ($detail['group_chat']['admin_list'] as $admin) {
                $find_admin               = Db::name('fastscrm_group_admin')
                    ->where('group_id', $group_id)
                    ->where('userid', $admin['userid'])
                    ->field('id')->find();
                $admin_data['group_id']   = $group_id;
                $admin_data['userid']     = $admin['userid'];
                $admin_data['updatetime'] = time();
                if (empty($find_admin)) {
                    Db::name('fastscrm_group_admin')
                        ->insert($admin_data, false, true);
                } else {
                    Db::name('fastscrm_group_admin')
                        ->where('id', $find_admin['id'])
                        ->update($admin_data);
                }
            }
        }
        if (!empty($detail['group_chat']['member_list'])) {
            foreach ($detail['group_chat']['member_list'] as $member) {
                $find_member             = Db::name('fastscrm_group_user')
                    ->where('group_id', $group_id)
                    ->where('userid', $member['userid'])
                    ->field('id')->find();
                $member_data['group_id'] = $group_id;
                $member_data['userid']   = $member['userid'];
                $member_data['type']     = $member['type'];
                if (!empty($member['unionid'])) {
                    $member_data['unionid'] = $member['unionid'];
                }
                $member_data['join_time']  = $member['join_time'];
                $member_data['join_scene'] = $member['join_scene'];
                if (!empty($member['invitor'])) {
                    $member_data['invitor'] = json_encode($member['invitor']);
                }
                $member_data['group_nickname'] = $member['group_nickname'];
                $member_data['name']           = $member['name'];
                $member_data['updatetime']     = time();
                if (empty($find_member)) {
                    Db::name('fastscrm_group_user')
                        ->insert($member_data, false, true);
                } else {
                    Db::name('fastscrm_group_user')
                        ->where('id', $find_member['id'])
                        ->update($member_data);
                }
            }
        }


        $this->success('ok', [
            'worker' => 1
        ]);
    }

    /**
     * 客户群变更事件
     */
    public function chatUpdate()
    {
        $info     = $this->info;
        $auth     = Auth::instance();
        $admin_id = $auth->isLogin() ? $auth->id : 0;
        $username = $auth->isLogin() ? $auth->username : 'api通知';
        $work     = new WeWork('App');

        $params = array(
            'chat_id' => $info['ChatId'],
            'need_name' => 1
        );

        $detail = $work->getGroupChatDetail($params, $admin_id, $username);

        $common             = new Common();
        $data['name']       = $common->removeEmoji($detail['group_chat']['name']);
        $data['owner']      = $detail['group_chat']['owner'];
        $data['createtime'] = $detail['group_chat']['create_time'];
        if (!empty($detail['group_chat']['notice'])) {
            $data['notice'] = $detail['group_chat']['notice'];
        }

        $data['updatetime'] = time();

        $group = Db::name('fastscrm_group_chat')
            ->where('chat_id', $params['chat_id'])
            ->field('id')
            ->find();


        Db::name('fastscrm_group_chat')
            ->where('id', $group['id'])
            ->update($data);
        $group_id = $group['id'];


        if ($info['UpdateDetail'] == 'add_member' || $info['UpdateDetail'] == 'del_member' || $info['UpdateDetail'] == 'change_notice') {

            //异步处理群成员信息
            sleep(1);
            $detail = $work->getGroupChatDetail($params, $admin_id, $username);

            if (!empty($detail['group_chat']['admin_list'])) {
                foreach ($detail['group_chat']['admin_list'] as $admin) {
                    $find_admin               = Db::name('fastscrm_group_admin')
                        ->where('group_id', $group_id)
                        ->where('userid', $admin['userid'])
                        ->field('id')->find();
                    $admin_data['group_id']   = $group_id;
                    $admin_data['userid']     = $admin['userid'];
                    $admin_data['updatetime'] = time();
                    if (empty($find_admin)) {
                        Db::name('fastscrm_group_admin')
                            ->insert($admin_data, false, true);
                    } else {
                        Db::name('fastscrm_group_admin')
                            ->where('id', $find_admin['id'])
                            ->update($admin_data);
                    }
                }
                $common = new Common();
                $common->difCpr('fastscrm_group_admin', array('group_id' => $group_id), 'userid',
                    $detail['group_chat']['admin_list']);
            }
            if (!empty($detail['group_chat']['member_list'])) {
                foreach ($detail['group_chat']['member_list'] as $member) {
                    $find_member             = Db::name('fastscrm_group_user')
                        ->where('group_id', $group_id)
                        ->where('userid', $member['userid'])
                        ->field('id')->find();
                    $member_data['group_id'] = $group_id;
                    $member_data['userid']   = $member['userid'];
                    $member_data['type']     = $member['type'];
                    if (!empty($member['unionid'])) {
                        $member_data['unionid'] = $member['unionid'];
                    }
                    $member_data['join_time']  = $member['join_time'];
                    $member_data['join_scene'] = $member['join_scene'];
                    if (!empty($member['invitor'])) {
                        $member_data['invitor'] = json_encode($member['invitor']);
                    }
                    $member_data['group_nickname'] = $member['group_nickname'];
                    $member_data['name']           = $member['name'];
                    $member_data['updatetime']     = time();
                    if (empty($find_member)) {
                        Db::name('fastscrm_group_user')
                            ->insert($member_data, false, true);
                    } else {
                        Db::name('fastscrm_group_user')
                            ->where('id', $find_member['id'])
                            ->update($member_data);
                    }
                }
                $common = new Common();
                $common->difCpr('fastscrm_group_user', array('group_id' => $group_id), 'userid',
                    $detail['group_chat']['member_list']);
            }


        }


        $this->success('ok', [
            'worker' => 1
        ]);
    }

    /**
     * 客户群解散事件
     */
    public function chatDelete()
    {

        $data = $this->info;

        $group = Db::name('fastscrm_group_chat')
            ->where('chat_id', $data['ChatId'])
            ->field('id')
            ->find();

        Db::name('fastscrm_group_user')
            ->where('group_id', $group['id'])
            ->delete();


        Db::name('fastscrm_group_chat')
            ->where('chat_id', $data['ChatId'])
            ->delete();
        $this->success('ok', [
            'worker' => 1
        ]);
    }


    /**
     * 企业客户标签创建事件
     */
    public function tagCreate()
    {
        $info = $this->info;

        $work = new WeWork('App');
        if ($info['TagType'] == 'tag') {
            $params = array(
                'tag_id' => array(
                    $info['Id']
                )
            );
        } else {
            $params = array(
                'group_id' => array(
                    $info['Id']
                )
            );
        }
        $list = $work->tagListSingle($params);
        if (empty($list)) {
            return false;
        }
        foreach ($list as $item) {
            $group = Db::name('fastscrm_tag_group')
                ->where('group_id', $item['group_id'])
                ->field('id')
                ->find();
            $data  = [
                'group_name' => $item['group_name'],
                'order' => $item['order'],
                'updatetime' => time(),
            ];
            if (!empty($group)) {
                Db::name('fastscrm_tag_group')
                    ->where('id', $group['id'])
                    ->update($data);
                $last_group_id = $group['id'];
            } else {
                $data['group_id']   = $item['group_id'];
                $data['createtime'] = $item['create_time'];
                $last_group_id      = Db::name('fastscrm_tag_group')
                    ->insert($data, false, true);
            }
            foreach ($item['tag'] as $v) {
                $tag      = Db::name('fastscrm_tag')
                    ->where('group_id', $last_group_id)
                    ->where('tag_id', $v['id'])
                    ->field('id')
                    ->find();
                $tag_data = [
                    'name' => $v['name'],
                    'order' => $v['order'],
                    'updatetime' => time(),
                ];
                if (!empty($tag)) {
                    Db::name('fastscrm_tag')
                        ->where('id', $tag['id'])
                        ->update($tag_data);
                } else {
                    $tag_data['group_id']   = $last_group_id;
                    $tag_data['tag_id']     = $v['id'];
                    $tag_data['createtime'] = $item['create_time'];
                    Db::name('fastscrm_tag')
                        ->insert($tag_data, false, true);
                }
            }

        }


        $this->success('ok', [
            'worker' => 1
        ]);
    }

    /**
     * 企业客户标签变更事件
     */
    public function tagUpdate()
    {
        $info = $this->info;

        $work = new WeWork('App');
        if ($info['TagType'] == 'tag') {
            $params = array(
                'tag_id' => array(
                    $info['Id']
                )
            );
        } else {
            $params = array(
                'group_id' => array(
                    $info['Id']
                )
            );
        }
        $list = $work->tagListSingle($params);
        if (empty($list)) {
            return false;
        }
        foreach ($list as $item) {
            $group = Db::name('fastscrm_tag_group')
                ->where('group_id', $item['group_id'])
                ->field('id')
                ->find();
            $data  = [
                'group_name' => $item['group_name'],
                'order' => $item['order'],
                'updatetime' => time(),
            ];
            if (!empty($group)) {
                Db::name('fastscrm_tag_group')
                    ->where('id', $group['id'])
                    ->update($data);
                $last_group_id = $group['id'];
            } else {
                $data['group_id']   = $item['group_id'];
                $data['createtime'] = $item['create_time'];
                $last_group_id      = Db::name('fastscrm_tag_group')
                    ->insert($data, false, true);
            }
            foreach ($item['tag'] as $v) {
                $tag      = Db::name('fastscrm_tag')
                    ->where('group_id', $last_group_id)
                    ->where('tag_id', $v['id'])
                    ->field('id')
                    ->find();
                $tag_data = [
                    'name' => $v['name'],
                    'order' => $v['order'],
                    'updatetime' => time(),
                ];
                if (!empty($tag)) {
                    Db::name('fastscrm_tag')
                        ->where('id', $tag['id'])
                        ->update($tag_data);
                } else {
                    $tag_data['group_id']   = $last_group_id;
                    $tag_data['tag_id']     = $v['id'];
                    $tag_data['createtime'] = $item['create_time'];
                    Db::name('fastscrm_tag')
                        ->insert($tag_data, false, true);
                }
            }

        }


        $this->success('ok', [
            'worker' => 1
        ]);
    }

    /**
     * 企业客户标签重排事件
     */
    public function tagShuffle()
    {
        $info = $this->info;
        sleep(3);

        $work = new WeWork('App');

        if (!empty($info['Id'])) {
            $params = array(
                'group_id' => array(
                    $info['Id']
                )
            );
            $list   = $work->tagListSingle($params);
        } else {
            $list = $work->tagList(0, 'api调用');
        }

        if (empty($list)) {
            return false;
        }
        foreach ($list as $item) {
            $group = Db::name('fastscrm_tag_group')
                ->where('group_id', $item['group_id'])
                ->field('id')
                ->find();
            $data  = [
                'group_name' => $item['group_name'],
                'order' => $item['order'],
                'updatetime' => time(),
            ];
            if (!empty($group)) {
                Db::name('fastscrm_tag_group')
                    ->where('id', $group['id'])
                    ->update($data);
                $last_group_id = $group['id'];
            } else {
                $data['group_id']   = $item['group_id'];
                $data['createtime'] = $item['create_time'];
                $last_group_id      = Db::name('fastscrm_tag_group')
                    ->insert($data, false, true);
            }
            foreach ($item['tag'] as $v) {
                $tag      = Db::name('fastscrm_tag')
                    ->where('group_id', $last_group_id)
                    ->where('tag_id', $v['id'])
                    ->field('id')
                    ->find();
                $tag_data = [
                    'name' => $v['name'],
                    'order' => $v['order'],
                    'updatetime' => time(),
                ];
                if (!empty($tag)) {
                    Db::name('fastscrm_tag')
                        ->where('id', $tag['id'])
                        ->update($tag_data);
                } else {
                    $tag_data['group_id']   = $last_group_id;
                    $tag_data['tag_id']     = $v['id'];
                    $tag_data['createtime'] = $item['create_time'];
                    Db::name('fastscrm_tag')
                        ->insert($tag_data, false, true);
                }
            }

        }
        $this->success('ok', [
            'worker' => 1
        ]);
    }


    /**
     * 企业客户标签删除事件
     */
    public function tagDelete()
    {
        $info = $this->info;
        if ($info['TagType'] == 'tag') {

            //更新客户标签
            $customers = \app\admin\model\fastscrm\crm\Customer::where('locate(:tag_id,fl_tags)')->bind(['tag_id' => $info['Id']])->field('id,fl_tags')->select();
            foreach ($customers as $customer) {
                $fl_tags = array_values(array_diff(json_decode($customer->fl_tags), [$info['Id']]));
                \app\admin\model\fastscrm\crm\Customer::where('id',
                    $customer->id)->update(['fl_tags' => json_encode($fl_tags)]);
            }

            $tag = Db::name('fastscrm_tag')
                ->where('tag_id', $info['Id'])
                ->field('id')
                ->find();
            Db::name('fastscrm_tag')
                ->where('tag_id', $info['Id'])
                ->delete();

        } else {
            $group = Db::name('fastscrm_tag_group')
                ->where('group_id', $info['Id'])
                ->field('id')
                ->find();

            $list = \app\admin\model\fastscrm\crm\Tag::where('group_id', $group['id'])->select();
            foreach ($list as $v) {
                $customers = \app\admin\model\fastscrm\crm\Customer::where('locate(:tag_id,fl_tags)')->bind(['tag_id' => $v->tag_id])->field('id,fl_tags')->select();
                foreach ($customers as $customer) {
                    $fl_tags = array_values(array_diff(json_decode($customer->fl_tags), [$v->tag_id]));
                    \app\admin\model\fastscrm\crm\Customer::where('id',
                        $customer->id)->update(['fl_tags' => json_encode($fl_tags)]);
                }
                \app\admin\model\fastscrm\crm\Tag::where('id', $v->id)->delete();
            }


            Db::name('fastscrm_tag')
                ->where('group_id', $group['id'])
                ->delete();
            Db::name('fastscrm_tag_group')
                ->where('group_id', $info['Id'])
                ->delete();
        }
        $this->success('ok', [
            'worker' => 1
        ]);
    }

   
    /**
     * 发送欢迎语
     */
    public function welcome($WelcomeCode, $UserID, $ExternalUserID)
    {
        $work   = new WeWork('App');
        $worker = Db::name('fastscrm_worker')->where('userid', $UserID)->find();

        $customer = Db::name('fastscrm_customer')->where('external_userid', $ExternalUserID)->find();

        $welitem = Db::name('fastscrm_sale_welcome')->where('find_in_set(:id,user_id)',
            ['id' => $UserID])->order('updatetime', 'desc')->find();


        if (empty($welitem)) {
            $storeids = explode(',', $worker['store_id']);
            foreach ($storeids as $storeid) {
                $welitem = Db::name('fastscrm_sale_welcome')
                    ->where('find_in_set(:id,store_id)', ['id' => $storeid])
                    ->order('updatetime', 'desc')
                    ->find();
                if (!empty($welitem)) {
                    break;
                }
            }
        }
        if (!empty($welitem)) {
            $Ext  = new WeWork('App');
            $params         = array();

            $params['welcome_code'] = $WelcomeCode;

            $content = $welitem['content'];

            $content = str_replace('{{员工姓名}}', $worker['name'], $content);
            $content = str_replace('{{客户昵称}}', $customer['name'], $content);

            $params['text']['content'] = $content;
            $item = Db::name('fastscrm_material_item')->where('id', $welitem['item_id'])->find();
            if(!empty($welitem['item_id']) && !empty($item)){
                //处理媒体信息
                $item['domain'] = cdnurl('', true);
                $item           = $Ext->dealMedia($item);
                $params['attachments'] = array();
                switch ($item['type']) {
                    case 1:
                        $params['attachments'][] = array(
                            'msgtype' => "image",
                            'image' => array(
                                "media_id" => $item['media_id'],
                            )
                        );
                        break;
                    case 3:
                        $params['attachments'][] = array(
                            'msgtype' => "link",
                            'link' => array(
                                "title" => $item['title'],
                                "picurl" => cdnurl($item['image'], true),
                                "desc" => $item['remark'],
                                "url" => $item['link_url'],
                            )
                        );
                        break;
                    case 5:
                        $params['attachments'][] = array(
                            'msgtype' => "video",
                            'video' => array(
                                "media_id" => $item['media_id'],
                            )
                        );
                        break;
                    case 6:
                        $params['attachments'][] = array(
                            'msgtype' => "miniprogram",
                            'miniprogram' => array(
                                "title" => $item['title'],
                                "pic_media_id" => $item['media_id'],
                                "appid" => $item['appid'],
                                "page" => $item['path'],
                            )
                        );
                        break;
                    case 7:
                        $params['attachments'][] = array(
                            'msgtype' => "file",
                            'file' => array(
                                "media_id" => $item['media_id'],
                            )
                        );
                        break;

                }
            }

            $work->sendWelcome($params);
        }


    }

}