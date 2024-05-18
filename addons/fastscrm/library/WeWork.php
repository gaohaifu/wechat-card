<?php

namespace addons\fastscrm\library;

use app\common\controller\Backend;
use think\Cache;
use think\Db;
use addons\fastscrm\library\Wework\App;
use think\Exception;

class WeWork extends Backend
{
    /**
     * @var App
     */
    protected $app;

    /**
     * WeWork constructor.
     *
     * @param string $default
     */
    public function __construct($default = '')
    {
        $config = get_fastscrm_config_by_server($_SERVER);
        $default ? $secret = $config[$default . '_' . 'address_secret'] : $secret = $config['address_secret'];
        $default == 'H5' ? $agent_id = $config[$default . '_' . 'agent_id'] : $agent_id = 0;
        $param     = [
            'corpid' => $config['corp_id'],
            'corpsecret' => $secret,
            'agent_id' => $agent_id,
        ];
        $this->app = new App($param);
    }


    /**
     * 同步企微部门列表
     */
    public function getDepart($admin_id, $username, $ip)
    {
        $result = $this->app->post('department/list', array());
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $message->getError($result['errcode']);
            $message->addLog('同步企微部门', $message->getError($result['errcode']), 0, $admin_id, $username, $ip);
            return false;
        } else {
            $message = new Message();
            $message->getError($result['errcode']);
            $message->addLog('同步企微部门', $message->getError($result['errcode']), 1, $admin_id, $username, $ip);
            return $result['department'];
        }
    }

    /**
     * 创建企微部门
     */
    public function createDepart($param)
    {

        $common            = new Common();
        $param['parentid'] = $common->getPid($param['parentid']);
        $result            = $this->app->post('department/create', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $this->error($message->getError($result['errcode']));
        } else {
            return $result['id'];
        }
    }

    /**
     * 修改企微部门
     */
    public function updateDepart($param)
    {
        $common            = new Common();
        $param['parentid'] = $common->getPid($param['parentid']);
        $param['id']       = $param['depart_id'];
        unset($param['depart_id']);
        $result = $this->app->post('department/update', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $this->error($message->getError($result['errcode']));
        } else {
            return true;
        }
    }

    /**
     * 删除企微部门
     */
    public function deleteDepart($param)
    {
        $result = $this->app->get('department/delete', array('id' => $param));
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $this->error($message->getError($result['errcode']));
        } else {
            return true;
        }
    }

    /**
     * 批量获取部门成员 1.0.2更新
     */
    public function getUserList($department_id, $admin_id, $username, $ip)
    {
        $result = $this->app->get('user/list', array('department_id' => $department_id));
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $message->addLog('同步部门成员', $message->getError($result['errcode']), 0, $admin_id, $username, $ip);
            return false;
        } else {
            $message = new Message();
            $message->addLog('同步部门成员', $message->getError($result['errcode']), 1, $admin_id, $username, $ip);
            return $result['userlist'];
        }
    }

    /**
     * 读取成员 1.0.2更新
     */
    public function getWorker($param)
    {
        $result = $this->app->get('user/get', array('userid' => $param));
        if ($result['errcode'] !== 0) {
            return false;
        } else {
            return $result;
        }
    }

    /**
     * 获取单个部门详情 1.0.2更新
     */
    public function getOneDepart($param)
    {
        $result = $this->app->get('department/get', array('id' => $param));
        if ($result['errcode'] !== 0) {
            return false;
        } else {
            return $result;
        }
    }

    /**
     * 创建企微成员
     */
    public function createWorker($param)
    {
        $result = $this->app->post('user/create', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $this->error($message->getError($result['errcode']));
        } else {
            return true;
        }
    }

    /**
     * 邀请企微成员
     */
    public function inviterWorker($param)
    {
        $result = $this->app->post('batch/invite', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $this->error($message->getError($result['errcode']));
        } else {
            return true;
        }
    }

    /**
     * 修改企微成员
     */
    public function updateWorker($param)
    {
        $result = $this->app->post('user/update', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $this->error($message->getError($result['errcode']));
        } else {
            return true;
        }
    }

    /**
     * 删除企微成员
     */
    public function deleteWorker($param)
    {
        $result = $this->app->get('user/delete', array('userid' => $param));
        if ($result['errcode'] !== 0) {
            $message = new Message();
            throw new Exception($message->getError($result['errcode']));
        } else {
            return true;
        }
    }

    /**
     * 获取客户列表
     */
    public function customerList($param, $admin_id, $username, $ip)
    {
        $result = $this->app->post('externalcontact/batch/get_by_user', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $message->getError($result['errcode']);
            $message->addLog('同步客户列表', $message->getError($result['errcode']), 0, $admin_id, $username, $ip);
            return false;
        } else {
            $message = new Message();
            $message->getError($result['errcode']);
            $message->addLog('同步客户列表', $message->getError($result['errcode']), 1, $admin_id, $username, $ip);
            return $result;
        }
    }

    /**
     * 获取客户详情
     */
    public function customerget($param)
    {
        $result = $this->app->get('externalcontact/get', array('external_userid' => $param));
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $this->error($message->getError($result['errcode']));
            return false;
        } else {
            return $result;
        }
    }


    /**
     * 消息推送
     */
    public function messageSend($params, $admin_id, $username, $ip)
    {
        return $this->app->post('externalcontact/add_msg_template', $params);
    }

    /**
     * 朋友圈推送
     */
    public function momentSend($params, $admin_id, $username, $ip)
    {
        return $this->app->post('externalcontact/add_moment_task', $params);
    }

    /**
     * 新增联系「联系我」方式
     */
    public function contactWay($params)
    {
        $result = $this->app->post('externalcontact/add_contact_way', $params);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $this->error($message->getError($result['errcode']));
            return false;
        } else {
            return $result;
        }
    }

    /**
     * 更新联系「联系我」方式
     */
    public function editContactWay($params)
    {
        $result = $this->app->post('externalcontact/update_contact_way', $params);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $this->error($message->getError($result['errcode']));
            return false;
        } else {
            return $result;
        }
    }

    /**
     * 获取朋友圈任务创建结果
     */
    public function getMomentTask($params)
    {
        return $this->app->get('externalcontact/get_moment_task_result', $params);

    }

    /**
     * 获取朋友圈成员发布状态
     */
    public function getMomentStatus($params)
    {
        return $this->app->post('externalcontact/get_moment_task', $params);
    }

    /**
     * 发送新客户欢迎语
     */
    public function sendWelcome($params)
    {
        $result = $this->app->post('externalcontact/send_welcome_msg', $params);
        if ($result['errcode'] !== 0) {
            return false;
        } else {
            return $result;
        }
    }

    /**
     * 处理媒体信息
     */
    public function dealMedia($item)
    {
        if ($item['type'] == 1 || $item['type'] == 3 || $item['type'] == 5 || $item['type'] == 6 || $item['type'] == 7) {
            $info = '';
            switch ($item['type']) {
                case 1:
                    $info = 'image';
                    break;
                case 3:
                    $info = 'image';
                    break;
                case 5:
                    $info = 'video';
                    break;
                case 6:
                    $info = 'image';
                    break;
                case 7:
                    $info = 'file';
                    break;
            }
            if ($item['sptime'] > time()) {
                return $item;
            }
            $result = $this->app->postFile('media/upload', array('type' => $info, 'path' => $item[$info]));
            if ($result['errcode'] !== 0) {
                return false;
            } else {
                Db::name('fastscrm_material_item')
                    ->where('id', $item['id'])
                    ->update(['media_id' => $result['media_id'], 'sptime' => $result['created_at'] + 84400 * 3]);
                $item['media_id'] = $result['media_id'];
                return $item;
            }
        } else {
            return $item;
        }

    }

    /**
     * 处理媒体信息
     */
    public function fj_dealMedia($item)
    {
        if ($item['type'] == 1 || $item['type'] == 3 || $item['type'] == 5 || $item['type'] == 6 || $item['type'] == 7) {
            $info  = '';
            switch ($item['type']) {
                case 1:
                    $info = 'image';
                    break;
                case 3:
                    $info = 'image';
                    break;
                case 5:
                    $info = 'video';
                    break;
                case 6:
                    $info = 'image';
                    break;
                case 7:
                    $info = 'file';
                    break;
            }
            if ($item['fj_sptime'] > time()) {
                return $item;
            }
            $result = $this->app->postFile('media/upload_attachment', array('type' => $info, 'path' => $item[$info]));
            if ($result['errcode'] !== 0) {
                return false;
            } else {
                Db::name('fastscrm_material_item')
                    ->where('id', $item['id'])
                    ->update(['fj_media_id' => $result['media_id'], 'fj_sptime' => $result['created_at'] + 84400 * 3]);
                $item['fj_media_id'] = $result['media_id'];
                return $item;
            }
        } else {
            return $item;
        }

    }

    /**
     * 获取标签列表
     */
    public function tagList($admin_id, $username, $ip)
    {
        $result = $this->app->get('externalcontact/get_corp_tag_list', array());
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $message->getError($result['errcode']);
            $message->addLog('同步标签列表', $message->getError($result['errcode']), 0, $admin_id, $username, $ip);
            return false;
        } else {
            $message = new Message();
            $message->getError($result['errcode']);
            $message->addLog('同步标签列表', $message->getError($result['errcode']), 1, $admin_id, $username, $ip);
            return $result['tag_group'];
        }
    }

    /**
     * 获取标签列表-单一查找
     */
    public function tagListSingle($param)
    {
        $result = $this->app->post('externalcontact/get_corp_tag_list', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $this->error($message->getError($result['errcode']));
        } else {
            return $result['tag_group'];
        }
    }

    /**
     * 创建标签组
     */
    public function createTag($param)
    {
        $group = Db::name('fastscrm_tag_group')
            ->where('id', $param['group_id'])
            ->find();

        if (!empty($group['group_id'])) {
            $data['group_id'] = $group['group_id'];
        }
        $data['group_name'] = $group['group_name'];
        $data['order']      = $group['order'];
        $json['name']       = $param['name'];
        $json['order']      = $param['order'];
        $data['tag'][]      = (object)$json;
        $result             = $this->app->post('externalcontact/add_corp_tag', $data);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $this->error($message->getError($result['errcode']));
        } else {
            if (empty($group['group_id'])) {
                Db::name('fastscrm_tag_group')
                    ->where('id', $param['group_id'])
                    ->update(['group_id' => $result['tag_group']['group_id']]);
            }
            return $result['tag_group']['tag'][0]['id'];
        }
    }

    /**
     * 修改标签
     */
    public function updateTag($param, $id, $type)
    {
        if ($type == 2) {
            $tag_info      = Db::name('fastscrm_tag')
                ->where('id', $id)
                ->find();
            $data['id']    = $tag_info['tag_id'];
            $data['name']  = $param['name'];
            $data['order'] = $param['order'];
        } else {
            $group        = Db::name('fastscrm_tag_group')
                ->where('id', $id)
                ->find();
            $data['id']   = $group['group_id'];
            $data['name'] = $param['group_name'];
        }
        $result = $this->app->post('externalcontact/edit_corp_tag', $data);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $this->error($message->getError($result['errcode']));
        } else {
            return true;
        }
    }

    /**
     * 删除标签
     */
    public function deleteTag($id, $type)
    {
        if ($type == 2) {
            $data['tag_id'][] = $id;
            $data['group_id'] = [];
        } else {
            $data['group_id'][] = $id;
        }
        $result = $this->app->post('externalcontact/del_corp_tag', $data);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $this->error($message->getError($result['errcode']));
        } else {
            return true;
        }
    }

    /**
     * 增加标签成员
     */
    public function addUsersTags($param)
    {
        $result = $this->app->post('externalcontact/mark_tag', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $this->error($message->getError($result['errcode']));
        } else {
            return true;
        }
    }

    /**
     * 删除标签成员
     */
    public function delUsersTags($param)
    {
        $result = $this->app->post('externalcontact/mark_tag', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $this->error($message->getError($result['errcode']));
        } else {
            return true;
        }
    }

    /**
     * 同步客户群列表
     */
    public function getGroupChat($param, $admin_id, $username, $ip)
    {
        $result = $this->app->post('externalcontact/groupchat/list', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $message->addLog('同步客户群列表', $message->getError($result['errcode']), 0, $admin_id, $username, $ip);
            return false;
        } else {
            $message = new Message();
            $message->addLog('同步客户群列表', $message->getError($result['errcode']), 1, $admin_id, $username, $ip);
            return $result;
        }
    }

    /**
     * 同步客户群详情
     */
    public function getGroupChatDetail($param, $admin_id, $username, $ip = '127.0.0.1')
    {
        $result = $this->app->post('externalcontact/groupchat/get', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $message->addLog('同步客户群详情', $message->getError($result['errcode']), 0, $admin_id, $username, $ip);
            return false;
        } else {
            $message = new Message();
            $message->addLog('同步客户群详情', $message->getError($result['errcode']), 1, $admin_id, $username, $ip);
            return $result;
        }
    }

    /**
     * 获取群聊数据统计按群主
     */
    public function getGroupData($param, $admin_id, $username, $ip)
    {
        $result = $this->app->post('externalcontact/groupchat/statistic', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $message->addLog('同步群聊数据统计', $message->getError($result['errcode']), 0, $admin_id, $username, $ip);
//            Cache::rm('groupData');
            return false;
        } else {
            return $result;
        }
    }

    /**
     * 获取群聊数据统计按自然日
     */
    public function getGroupDataDay($param, $admin_id, $username, $ip)
    {
        $result = $this->app->post('externalcontact/groupchat/statistic_group_by_day', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $message->addLog('同步群聊数据统计', $message->getError($result['errcode']), 0, $admin_id, $username, $ip);
//            Cache::rm('groupData');
            return false;
        } else {
            return $result;
        }
    }


    /**
     * 获取联系客户数据统计
     */
    public function getCustomerData($param, $admin_id, $username, $ip)
    {
        $result = $this->app->post('externalcontact/get_user_behavior_data', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $message->addLog('同步联系客户数据统计', $message->getError($result['errcode']), 0, $admin_id, $username, $ip);
//            Cache::rm('customerData');
            return false;
        } else {
            return $result;
        }
    }

    /**
     * 获取企业群发成员执行结果
     */
    public function getSendResult($param, $admin_id, $username, $ip)
    {
        $result = $this->app->post('externalcontact/get_groupmsg_send_result', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $message->addLog('同步企业群发成员执行结果', $message->getError($result['errcode']), 0, $admin_id, $username, $ip);
//            Cache::rm('sendResult');
            return false;
        } else {
            return $result;
        }
    }

    /**
     * 新增客户回调增加标签成员 1.0.4更新
     */
    public function notifyUsersTags($param)
    {
        $result = $this->app->post('externalcontact/mark_tag', $param);
        if ($result['errcode'] !== 0) {
            return false;
        } else {
            return true;
        }
    }


    /**
     * 获取sdkConfig
     * 高级授权
     */
    public function getSdkConfig($url)
    {
        $config = get_fastscrm_config_by_server($_SERVER);
        return $this->app->getAgentConfig(array(
            'url' => $url,
            'corp_id' => $config['corp_id'],
            'agent_id' => $config['H5_agent_id']
        ));
    }

    /**
     * 获取网页授权用户信息
     * 高级授权
     */
    public function getUser($code)
    {
        return $this->app->get('user/getuserinfo', array('code' => $code));
    }

    /**
     * 获取登录url.
     * 高级授权
     * @param string $redirectUri
     * @param string $scope
     * @param string|null $state
     *
     * @return string
     */
    public function getOAuthRedirectUrl(string $redirectUri = '', string $scope = 'snsapi_base', string $state = null)
    {
        $config = get_fastscrm_config_by_server($_SERVER);
        $params = [
            'appid' => $config['corp_id'],
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => $scope,
            'state' => $state,
        ];
        if ($scope == 'snsapi_privateinfo') {
            $params['agentid'] = $config['H5_agent_id'];
        }

        return 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query($params) . '#wechat_redirect';
    }

    /**
     * 获取网页授权用户敏感信息 1.0.4更新
     * 高级授权
     */
    public function getWorkerDetail($param)
    {
        return $this->app->post('auth/getuserdetail', array('user_ticket'=>$param));

    }


    /**
     * 发送应用消息(文本消息) 1.0.4更新
     * 高级授权
     */
    public function sendMessage($param, $admin_id, $username, $ip)
    {
        $config                          = get_fastscrm_config_by_server($_SERVER);
        $data['touser']                  = $param['workers'];
        $data['msgtype']                 = 'textcard';
        $data['agentid']                 = $config['H5_agent_id'];
        $data['textcard']['title']       = $param['title'];
        $data['textcard']['description'] = $param['content'];
        $data['textcard']['url']         = $param['url'];
        $data['textcard']['btntxt']      = $param['btnTxt'];
        $result                          = $this->app->post('message/send', $data);
        $message                         = new Message();
        if ($result['errcode'] !== 0) {
            $message->addLog('分配客户应用通知', $message->getError($result['errcode']), 0, $admin_id, $username, $ip);
            return false;
        } else {
            $message->addLog('分配客户应用通知', $message->getError($result['errcode']), 1, $admin_id, $username, $ip);
            return true;
        }
    }

    /**
     * 新增敏感词规则
     * 高级授权
     */
    public function addInterceptRule($params)
    {
        $result = $this->app->post('externalcontact/add_intercept_rule', $params);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $this->error($message->getError($result['errcode']));
            return false;
        } else {
            return $result;
        }
    }

    /**
     * 修改敏感词规则
     * 高级授权
     */
    public function editInterceptRule($params)
    {
        $result = $this->app->post('externalcontact/update_intercept_rule', $params);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $this->error($message->getError($result['errcode']));
            return false;
        } else {
            return $result;
        }
    }

    /**
     * 删除敏感词规则
     * 高级授权
     */
    public function deleteInterceptRule($params)
    {
        $result = $this->app->post('externalcontact/del_intercept_rule', $params);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $this->error($message->getError($result['errcode']));
        } else {
            return true;
        }
    }

    /**
     * 新增联系「联系我」方式
     * 高级授权
     * 工作台使用
     */
    public function appContactWay($params)
    {
        return $this->app->post('externalcontact/add_contact_way', $params);
    }

    /**
     * 离职客户继承
     */
    public function resignedTransferCustomer($param, $admin_id, $username, $ip)
    {
        $result = $this->app->post('externalcontact/resigned/transfer_customer', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $message->addLog('离职客户继承', $message->getError($result['errcode']), 0, $admin_id, $username, $ip);
            return false;
        } else {
            $message = new Message();
            $message->addLog('离职客户继承', $message->getError($result['errcode']), 1, $admin_id, $username, $ip);
            return $result;
        }
    }

    /**
     * 离职群继承
     */
    public function resignedTransferChat($param, $admin_id, $username, $ip)
    {
        $result = $this->app->post('externalcontact/groupchat/transfer', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $message->addLog('离职群继承', $message->getError($result['errcode']), 0, $admin_id, $username, $ip);
            return false;
        } else {
            $message = new Message();
            $message->addLog('离职群继承', $message->getError($result['errcode']), 1, $admin_id, $username, $ip);
            return $result;
        }
    }

    /**
     * 查询离职客户接替状态
     */
    public function resignedTransferResult($param, $admin_id, $username, $ip)
    {
        $result = $this->app->post('externalcontact/resigned/transfer_result', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $message->addLog('查询离职客户接替状态', $message->getError($result['errcode']), 0, $admin_id, $username, $ip);
            return false;
        } else {
            $message = new Message();
            $message->addLog('查询离职客户接替状态', $message->getError($result['errcode']), 1, $admin_id, $username, $ip);
            return $result;
        }
    }

    /**
     * 在职客户继承
     */
    public function onJobTransferCustomer($param, $admin_id, $username, $ip)
    {
        $result = $this->app->post('externalcontact/transfer_customer', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $message->addLog('在职客户继承', $message->getError($result['errcode']), 0, $admin_id, $username, $ip);
            return false;
        } else {
            $message = new Message();
            $message->addLog('在职客户继承', $message->getError($result['errcode']), 1, $admin_id, $username, $ip);
            return $result;
        }
    }

    /**
     * 在职群继承
     */
    public function onJobTransferChat($param, $admin_id, $username, $ip)
    {
        $result = $this->app->post('externalcontact/groupchat/onjob_transfer', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $message->addLog('在职群继承', $message->getError($result['errcode']), 0, $admin_id, $username, $ip);
            return false;
        } else {
            $message = new Message();
            $message->addLog('在职群继承', $message->getError($result['errcode']), 1, $admin_id, $username, $ip);
            return $result;
        }
    }

    /**
     * 查询在职客户接替状态
     */
    public function onJobTransferResult($param, $admin_id, $username, $ip)
    {
        $result = $this->app->post('externalcontact/transfer_result', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $message->addLog('查询在职客户接替状态', $message->getError($result['errcode']), 0, $admin_id, $username, $ip);
            return false;
        } else {
            $message = new Message();
            $message->addLog('查询在职客户接替状态', $message->getError($result['errcode']), 1, $admin_id, $username, $ip);
            return $result;
        }
    }

    /**
     * 上传临时素材
     */
    public function tempMedia($type,$path)
    {
        $result = $this->app->postFile('media/upload', array('type' => $type, 'path' => $path));
        if ($result['errcode'] !== 0) {
            $message = new Message();
            throw new Exception($message->getError($result['errcode']));
        } else {
            return $result;
        }
    }

    /**
     * 添加客服账号
     */
    public function kfAdd($param)
    {
        $result = $this->app->post('kf/account/add', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            throw new Exception($message->getError($result['errcode']));
        } else {
            return $result;
        }
    }

    /**
     * 修改客服账号
     */
    public function kfUpdate($param)
    {
        $result = $this->app->post('kf/account/update', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            throw new Exception($message->getError($result['errcode']));
        } else {
            return $result;
        }
    }

    /**
     * 删除客服账号
     */
    public function kfDel($param)
    {
        $result = $this->app->post('kf/account/del', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            throw new Exception($message->getError($result['errcode']));
        } else {
            return $result;
        }
    }

    /**
     * 同步客服账号
     */
    public function kfList($param, $admin_id, $username, $ip)
    {
        $result = $this->app->post('kf/account/list', $param);
        $message = new Message();
        if ($result['errcode'] !== 0) {
            $message->addLog('同步客户账号', $message->getError($result['errcode']), 0, $admin_id, $username, $ip);
            return false;
        } else {
            $message->addLog('同步客户账号', $message->getError($result['errcode']), 1, $admin_id, $username, $ip);
            return $result;
        }
    }

    /**
     * 获取客服账号链接
     */
    public function kfUrl($param)
    {
        $result = $this->app->post('kf/add_contact_way', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            throw new Exception($message->getError($result['errcode']));
        } else {
            return $result;
        }
    }

    /**
     * 添加接待人员
     */
    public function servicerAdd($param)
    {
        $result = $this->app->post('kf/servicer/add', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            throw new Exception($message->getError($result['errcode']));
        } else {
            return $result;
        }
    }

    /**
     * 删除接待人员
     */
    public function servicerDel($param)
    {
        $result = $this->app->post('kf/servicer/del', $param);
        if ($result['errcode'] !== 0) {
            $message = new Message();
            $message->addLog('删除接待人员', $message->getError($result['errcode']), 0, '', '', '');
            return false;
        } else {
            return $result;
        }
    }

    /**
     * 同步接待人员
     */
    public function servicerList($param, $admin_id, $username, $ip)
    {
        $result = $this->app->get('kf/servicer/list', $param);
        $message = new Message();
        if ($result['errcode'] !== 0) {
            $message->addLog('同步接待人员', $message->getError($result['errcode']), 0, $admin_id, $username, $ip);
            return false;
        } else {
            $message->addLog('同步接待人员', $message->getError($result['errcode']), 1, $admin_id, $username, $ip);
            return $result;
        }
    }
}