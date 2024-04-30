<?php

namespace addons\fastscrm\library\Job;

use addons\fastscrm\library\Common;
use addons\fastscrm\library\Message;
use addons\fastscrm\library\WeWork;
use app\admin\model\fastscrm\kf\Account;
use app\admin\model\fastscrm\kf\Servicer;
use fast\Date;
use fast\Tree;
use think\Cache;
use think\Controller;
use think\Exception;
use think\Queue;
use think\Queue\Job;
use think\Db;

class DoJob
{
    /**
     * fire方法是消息队列默认调用的方法
     * @param Job $job 当前的任务对象
     * @param $data
     * @return int
     */
    public function fire(Job $job, $data)
    {
        if ($job->attempts() > 3) {
            $job->delete();
            return false;
        }
        if (!isset($data['admin_id']) || !isset($data['username']) || !isset($data['ip'])) {
            $job->delete();
            return false;
        }

        if (is_array($data) && isset($data['task'])) {
            $task = $data['task'];
            switch ($task) {
                case 'depart':
                    $isJobDone = $this->depart($data);
                    break;
                case 'worker':
                    $isJobDone = $this->worker($data);
                    break;
                case 'workerJob':
                    $isJobDone = $this->workerJob($data);
                    break;
                case 'customer':
                    $isJobDone = $this->customer($data);
                    break;
                case 'customer_next':
                    $isJobDone = $this->customerNext($data);
                    break;
                case 'groupChat':
                    $isJobDone = $this->groupChat($data);
                    break;
                case 'groupChat_next':
                    $isJobDone = $this->groupChat_next($data);
                    break;
                case 'tag':
                    $isJobDone = $this->tag($data);
                    break;
                case 'customerSale':
                    $isJobDone = $this->customerSale($data);
                    break;
                case 'chatSale':
                    $isJobDone = $this->chatSale($data);
                    break;
                case 'momentsSale':
                    $isJobDone = $this->momentsSale($data);
                    break;
                case 'groupData':
                    $isJobDone = $this->groupData($data);
                    break;
                case 'customerData':
                    $isJobDone = $this->customerData($data);
                    break;
                case 'sendResult':
                    $isJobDone = $this->sendResult($data);
                    break;
                case 'momentResult':
                    $isJobDone = $this->momentResult();
                    break;
                case 'start':
                    $isJobDone = false;
                    if (!empty($this->depart($data))) {
                        if (!empty($this->workerJob($data))) {
                            if (!empty($this->tag($data))) {
                                if (!empty($this->customer($data))) {
                                    $isJobDone = $this->groupChat($data);
                                }
                            }
                        }
                    }
                    break;
                case 'sendMessage':
                    $isJobDone = $this->sendMessage($data);
                    break;
                case 'resignedCustomer':
                    $isJobDone = $this->resignedCustomer($data);
                    break;
                case 'resignedCustomerNext':
                    $isJobDone = $this->resignedCustomerNext($data);
                    break;
                case 'resignedChat':
                    $isJobDone = $this->resignedChat($data);
                    break;
                case 'resignedCustomerSync':
                    $isJobDone = $this->resignedCustomerSync($data);
                    break;
                case 'resignedCustomerSyncNext':
                    $isJobDone = $this->resignedCustomerSyncNext($data);
                    break;
                case 'onJobCustomer':
                    $isJobDone = $this->onJobCustomer($data);
                    break;
                case 'onJobCustomerNext':
                    $isJobDone = $this->onJobCustomerNext($data);
                    break;
                case 'onJobChat':
                    $isJobDone = $this->onJobChat($data);
                    break;
                case 'onJobCustomerSync':
                    $isJobDone = $this->onJobCustomerSync($data);
                    break;
                case 'onJobCustomerSyncNext':
                    $isJobDone = $this->onJobCustomerSyncNext($data);
                    break;
                case 'kf':
                    $isJobDone = $this->kf($data);
                    break;
                case 'kfNext':
                    $isJobDone = $this->kfNext($data);
                    break;
                case 'servicer':
                    $isJobDone = $this->servicer($data);
                    break;
                case 'servicerNext':
                    $isJobDone = $this->servicerNext($data);
                    break;
                default:
                    $job->delete();
                    return false;
                    break;
            }

        } else {
            $job->delete();
            return false;
        }
        if ($isJobDone) {
            echo $task . ' SUCCESS:';
            $job->delete();
        } else {
            if ($job->attempts() > 3) {
                echo $task . ' error:';
                $job->delete();
                //$job->release(2); //$delay为延迟时间，表示该任务延迟2秒后再执行
            }
        }
    }

    /**
     * 同步企微部门
     */
    private function depart($apidata)
    {
        $work = new WeWork('App');
        $list = $work->getDepart($apidata['admin_id'], $apidata['username'], $apidata['ip']);
        if (empty($list)) {
            return false;
        }
        $common = new Common();
        $list = $common->multi_array_sort($list, 'parentid', SORT_ASC);
        $sortList = [];
        foreach ($list as $item) {
            $data = [
                'depart_id' => $item['id'],
                'name' => $item['name'],
                'department_leader' => implode(',', $item['department_leader']),
                'order' => $item['order'],
                'updatetime' => time()
            ];
            $depart = Db::name('fastscrm_depart')
                ->where('depart_id', $item['id'])
                ->find();
            if ($depart) {
                Db::name('fastscrm_depart')
                    ->where('id', $depart['id'])
                    ->update($data);
            } else {
                $data['createtime'] = time();
                Db::name('fastscrm_depart')
                    ->insert($data, false, true);
            }

            $sortList[$item['id']] = $item;
        }
        foreach ($sortList as $item) {
            if ($item['parentid'] > 0) {
                $depart = Db::name('fastscrm_depart')
                    ->where('depart_id', $item['id'])
                    ->find();
                $parentDepart = Db::name('fastscrm_depart')
                    ->where('depart_id', $item['parentid'])
                    ->field('id')
                    ->find();
                Db::name('fastscrm_depart')
                    ->where('id', $depart['id'])
                    ->update(['parentid' => $parentDepart['id']]);
            }
        }
        return true;
    }

    /**
     * 同步企微员工
     */
    private function workerJob($apidata, $init = true, $parentid = '')
    {
        $model = new \app\admin\model\fastscrm\company\Depart;
        if ($init) {
            $list = collection($model->field('id,name,parentid,depart_id')->where('depart_id',
                1)->order('parentid ASC,order DESC')->select())->toArray();
        } else {
            $list = collection($model->field('id,name,parentid,depart_id')->where('parentid',
                $parentid)->order('parentid ASC,order DESC')->select())->toArray();
        }
        if (!empty($list)) {
            $temp = [];
            $job = new AddJob();
            foreach ($list as $item) {
                $temp['admin_id'] = $apidata['admin_id'];
                $temp['username'] = $apidata['username'];
                $temp['ip'] = $apidata['ip'];
                $temp['task'] = 'worker';
                $temp['department_id'] = $item['depart_id'];
                $job->add($temp);
                $this->workerJob($apidata, false, $item['id']);
            }
        }
        return true;
    }

    /**
     * 同步企微员工
     */
    private function worker($apidata)
    {
        $work = new WeWork('App');
        $list = $work->getUserList($apidata['department_id'], $apidata['admin_id'], $apidata['username'],
            $apidata['ip']);
        foreach ($list as $item) {
            $worker = Db::name('fastscrm_worker')
                ->where('userid', $item['userid'])
                ->field('id')
                ->find();
            $data = [
                'userid' => $item['userid'],
                'name' => isset($item['name']) ? $item['name'] : '',
                'department' => implode(',', $item['department']),
                'mobile' => isset($item['mobile']) ? $item['mobile'] : '',
                'order' => isset($item['order']) ? implode(',', $item['order']) : '',
                'position' => isset($item['position']) ? $item['position'] : '',
                'gender' => isset($item['gender']) ? $item['gender'] : '0',
                'email' => isset($item['email']) ? $item['email'] : '',
                'biz_mail' => isset($item['biz_mail']) ? $item['biz_mail'] : '',
                'is_leader_in_dept' => isset($item['is_leader_in_dept']) ? implode(',',
                    $item['is_leader_in_dept']) : '',
                'direct_leader' => isset($item['direct_leader']) ? implode(',', $item['direct_leader']) : '',
                'avatar' => isset($item['avatar']) ? $item['avatar'] : '',
                'thumb_avatar' => isset($item['thumb_avatar']) ? $item['thumb_avatar'] : '',
                'telephone' => isset($item['telephone']) ? $item['telephone'] : '',
                'alias' => isset($item['alias']) ? $item['alias'] : '',
                'extattr' => isset($item['extattr']) ? json_encode($item['extattr']) : '',
                'status' => $item['status'],
                'qr_code' => isset($item['qr_code']) ? $item['qr_code'] : '',
                'main_department' => isset($item['main_department']) ? $item['main_department'] : '',
                'updatetime' => time()
            ];
            if (isset($item['address'])) {
                $data['address'] = json_encode($item['address']);
            }
            if (isset($item['external_profile'])) {
                $data['external_profile'] = json_encode($item['external_profile']);
            }
            if (isset($item['external_position'])) {
                $data['external_position'] = json_encode($item['external_position']);
            }
            if (isset($item['open_userid'])) {
                $data['open_userid'] = $item['open_userid'];
            }

            if (!empty($worker)) {
                unset($data['mobile']);
                unset($data['gender']);
                unset($data['email']);
                unset($data['avatar']);
                unset($data['thumb_avatar']);
                unset($data['qr_code']);
                unset($data['address']);
                Db::name('fastscrm_worker')
                    ->where('id', $worker['id'])
                    ->update($data);
            } else {
                $data['createtime'] = time();
                Db::name('fastscrm_worker')
                    ->insert($data, false, true);
            }
        }
        return true;
    }

    /**
     * 同步客户列表
     */
    private function customer($apidata, $cursor = '', $page = 1)
    {
        $work = new WeWork('App');
        $workers = Db::name('fastscrm_worker')->where('status', 1)->page($page, 100)->column('userid');
        if (!empty($workers)) {
            $params = array(
                'userid_list' => $workers,
                'cursor' => $cursor,
                'limit' => 100
            );
            $list = $work->customerList($params, $apidata['admin_id'], $apidata['username'], $apidata['ip']);

            if (empty($list)) {
                return false;
            }
            $common = new Common();
            foreach ($list['external_contact_list'] as $item) {
                $customer = Db::name('fastscrm_customer')
                    ->where('external_userid', $item['external_contact']['external_userid'])
                    ->where('fl_userid', $item['follow_info']['userid'])
                    ->field('id')
                    ->find();
                $data = [
                    'external_userid' => $item['external_contact']['external_userid'],
                    'name' => $common->removeEmoji($item['external_contact']['name']),
                    'avatar' => $item['external_contact']['avatar'],
                    'type' => $item['external_contact']['type'],
                    'gender' => $item['external_contact']['gender'],
                    'fl_userid' => $item['follow_info']['userid'],
                    'fl_remark' => $common->removeEmoji($item['follow_info']['remark']),
                    'fl_description' => $item['follow_info']['description'],
                    'fl_createtime' => $item['follow_info']['createtime'],
                    'fl_add_way' => isset($item['follow_info']['add_way']) ? $item['follow_info']['add_way'] : '',
                    'updatetime' => time()
                ];
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
                if (isset($item['follow_info']['tag_id'])) {
                    $data['fl_tags'] = json_encode($item['follow_info']['tag_id']);
                }
                if (isset($item['follow_info']['remark_mobiles'])) {
                    $data['fl_remark_mobiles'] = json_encode($item['follow_info']['remark_mobiles']);
                }
                if (isset($item['follow_info']['state'])) {
                    $data['state'] = $item['follow_info']['state'];
                }
                if (!empty($customer)) {
                    Db::name('fastscrm_customer')
                        ->where('id', $customer['id'])
                        ->update($data);
                } else {
                    Db::name('fastscrm_customer')
                        ->insert($data, false, true);
                }
            }
            if (!empty($list['next_cursor'])) {
                $job = new AddJob();
                $param['admin_id'] = $apidata['admin_id'];
                $param['username'] = $apidata['username'];
                $param['task'] = 'customer_next';
                $param['ip'] = $apidata['ip'];
                $param['workers'] = $workers;
                $param['cursor'] = $list['next_cursor'];
                $job->add($param);
//                $this->customer($apidata,$list['next_cursor'], $page);
            }
            $this->customer($apidata, '', $page + 1);

        }
        return true;
    }

    /**
     * 同步客户列表
     */
    private function customerNext($apidata, $cursor = '', $page = 1)
    {
        $work = new WeWork('App');
        $workers = $apidata['workers'];
        if (!empty($workers)) {
            $params = array(
                'userid_list' => $workers,
                'cursor' => $apidata['cursor'],
                'limit' => 100
            );
            $list = $work->customerList($params, $apidata['admin_id'], $apidata['username'], $apidata['ip']);
            if (empty($list)) {
                return false;
            }
            $common = new Common();
            foreach ($list['external_contact_list'] as $item) {
                $customer = Db::name('fastscrm_customer')
                    ->where('external_userid', $item['external_contact']['external_userid'])
                    ->where('fl_userid', $item['follow_info']['userid'])
                    ->field('id')
                    ->find();
                $data = [
                    'external_userid' => $item['external_contact']['external_userid'],
                    'name' => $common->removeEmoji($item['external_contact']['name']),
                    'avatar' => $item['external_contact']['avatar'],
                    'type' => $item['external_contact']['type'],
                    'gender' => $item['external_contact']['gender'],
                    'fl_userid' => $item['follow_info']['userid'],
                    'fl_remark' => $common->removeEmoji($item['follow_info']['remark']),
                    'fl_description' => $item['follow_info']['description'],
                    'fl_createtime' => $item['follow_info']['createtime'],
                    'fl_add_way' => isset($item['follow_info']['add_way']) ? $item['follow_info']['add_way'] : '',
                    'updatetime' => time()
                ];
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
                if (isset($item['follow_info']['tag_id'])) {
                    $data['fl_tags'] = json_encode($item['follow_info']['tag_id']);
                }
                if (isset($item['follow_info']['remark_mobiles'])) {
                    $data['fl_remark_mobiles'] = json_encode($item['follow_info']['remark_mobiles']);
                }
                if (isset($item['follow_info']['state'])) {
                    $data['state'] = $item['follow_info']['state'];
                }
                if (!empty($customer)) {
                    Db::name('fastscrm_customer')
                        ->where('id', $customer['id'])
                        ->update($data);
                } else {
                    Db::name('fastscrm_customer')
                        ->insert($data, false, true);
                }
            }
            if (!empty($list['next_cursor'])) {
                $job = new AddJob();
                $param['admin_id'] = $apidata['admin_id'];
                $param['username'] = $apidata['username'];
                $param['task'] = 'customer_next';
                $param['ip'] = $apidata['ip'];
                $param['workers'] = $workers;
                $param['cursor'] = $list['next_cursor'];
                $job->add($param);
            }
        }
        return true;
    }

    /**
     * 同步客户群列表
     */
    private function groupChat($apidata, $cursor = '', $page = 1)
    {
        $work = new WeWork('App');
        $workers = Db::name('fastscrm_worker')->where('status', 1)->page($page, 100)->column('userid');
        if (!empty($workers)) {
            $params = array(
                'status_filter' => 0,
                'owner_filter' => array('userid_list' => $workers),
                'cursor' => $cursor,
                'limit' => 10
            );
            $list = $work->getGroupChat($params, $apidata['admin_id'], $apidata['username'], $apidata['ip']);
            if (empty($list)) {
                return false;
            }

            foreach ($list['group_chat_list'] as $item) {
                $group = Db::name('fastscrm_group_chat')
                    ->where('chat_id', $item['chat_id'])
                    ->field('id')
                    ->find();
                $data = [
                    'chat_id' => $item['chat_id'],
                    'status' => $item['status'],
                ];
                $params = array(
                    'chat_id' => $data['chat_id'],
                    'need_name' => 1
                );
                $detail = $work->getGroupChatDetail($params, $apidata['admin_id'], $apidata['username'],
                    $apidata['ip']);
                $common = new Common();
                $data['name'] = $common->removeEmoji($detail['group_chat']['name']);
                $data['owner'] = $detail['group_chat']['owner'];
                $data['createtime'] = $detail['group_chat']['create_time'];
                if (isset($detail['group_chat']['notice'])) {
                    $data['notice'] = $detail['group_chat']['notice'];
                }
                $data['updatetime'] = time();

                if (!empty($group)) {
                    Db::name('fastscrm_group_chat')
                        ->where('id', $group['id'])
                        ->update($data);
                    $group_id = $group['id'];
                } else {
                    $group_id = Db::name('fastscrm_group_chat')
                        ->insert($data, false, true);
                }
                if (isset($detail['group_chat']['admin_list'])) {
                    foreach ($detail['group_chat']['admin_list'] as $admin) {
                        $find_admin = Db::name('fastscrm_group_admin')
                            ->where('group_id', $group_id)
                            ->where('userid', $admin['userid'])
                            ->field('id')->find();
                        $admin_data['group_id'] = $group_id;
                        $admin_data['userid'] = $admin['userid'];
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
                if (isset($detail['group_chat']['member_list'])) {
                    foreach ($detail['group_chat']['member_list'] as $member) {
                        $find_member = Db::name('fastscrm_group_user')
                            ->where('group_id', $group_id)
                            ->where('userid', $member['userid'])
                            ->field('id')->find();
                        $member_data['group_id'] = $group_id;
                        $member_data['userid'] = $member['userid'];
                        $member_data['type'] = $member['type'];
                        if (isset($member['unionid'])) {
                            $member_data['unionid'] = $member['unionid'];
                        }
                        $member_data['join_time'] = $member['join_time'];
                        $member_data['join_scene'] = $member['join_scene'];
                        if (isset($member['invitor'])) {
                            $member_data['invitor'] = json_encode($member['invitor']);
                        }
                        $member_data['group_nickname'] = $member['group_nickname'];
                        $member_data['name'] = $member['name'];
                        $member_data['updatetime'] = time();
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

            if (!empty($list['next_cursor'])) {
                $job = new AddJob();
                $param['admin_id'] = $apidata['admin_id'];
                $param['username'] = $apidata['username'];
                $param['task'] = 'groupChat_next';
                $param['ip'] = $apidata['ip'];
                $param['workers'] = $workers;
                $param['cursor'] = $list['next_cursor'];
                $job->add($param);
            }
            $this->groupChat($apidata, '', $page + 1);
        }
        return true;
    }

    /**
     * 同步客户群列表
     */
    private function groupChat_next($apidata, $cursor = '', $page = 1)
    {
        $work = new WeWork('App');
        $workers = $apidata['workers'];
        if (!empty($workers)) {
            $params = array(
                'status_filter' => 0,
                'owner_filter' => array('userid_list' => $workers),
                'cursor' => $apidata['cursor'],
                'limit' => 10
            );
            $list = $work->getGroupChat($params, $apidata['admin_id'], $apidata['username'], $apidata['ip']);
            if (empty($list)) {
                return false;
            }

            foreach ($list['group_chat_list'] as $item) {
                $group = Db::name('fastscrm_group_chat')
                    ->where('chat_id', $item['chat_id'])
                    ->field('id')
                    ->find();
                $data = [
                    'chat_id' => $item['chat_id'],
                    'status' => $item['status'],
                ];
                $params = array(
                    'chat_id' => $data['chat_id'],
                    'need_name' => 1
                );
                $detail = $work->getGroupChatDetail($params, $apidata['admin_id'], $apidata['username'],
                    $apidata['ip']);
                $common = new Common();
                $data['name'] = $common->removeEmoji($detail['group_chat']['name']);
                $data['owner'] = $detail['group_chat']['owner'];
                $data['createtime'] = $detail['group_chat']['create_time'];
                if (isset($detail['group_chat']['notice'])) {
                    $data['notice'] = $detail['group_chat']['notice'];
                }
                $data['updatetime'] = time();

                if (!empty($group)) {
                    Db::name('fastscrm_group_chat')
                        ->where('id', $group['id'])
                        ->update($data);
                    $group_id = $group['id'];
                } else {
                    $group_id = Db::name('fastscrm_group_chat')
                        ->insert($data, false, true);
                }
                if (isset($detail['group_chat']['admin_list'])) {
                    foreach ($detail['group_chat']['admin_list'] as $admin) {
                        $find_admin = Db::name('fastscrm_group_admin')
                            ->where('group_id', $group_id)
                            ->where('userid', $admin['userid'])
                            ->field('id')->find();
                        $admin_data['group_id'] = $group_id;
                        $admin_data['userid'] = $admin['userid'];
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
                if (isset($detail['group_chat']['member_list'])) {
                    foreach ($detail['group_chat']['member_list'] as $member) {
                        $find_member = Db::name('fastscrm_group_user')
                            ->where('group_id', $group_id)
                            ->where('userid', $member['userid'])
                            ->field('id')->find();
                        $member_data['group_id'] = $group_id;
                        $member_data['userid'] = $member['userid'];
                        $member_data['type'] = $member['type'];
                        if (isset($member['unionid'])) {
                            $member_data['unionid'] = $member['unionid'];
                        }
                        $member_data['join_time'] = $member['join_time'];
                        $member_data['join_scene'] = $member['join_scene'];
                        if (isset($member['invitor'])) {
                            $member_data['invitor'] = json_encode($member['invitor']);
                        }
                        $member_data['group_nickname'] = $member['group_nickname'];
                        $member_data['name'] = $member['name'];
                        $member_data['updatetime'] = time();
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

            if (!empty($list['next_cursor'])) {
                $job = new AddJob();
                $param['admin_id'] = $apidata['admin_id'];
                $param['username'] = $apidata['username'];
                $param['task'] = 'groupChat_next';
                $param['ip'] = $apidata['ip'];
                $param['workers'] = $workers;
                $param['cursor'] = $list['next_cursor'];
                $job->add($param);
            }

        }
        return true;
    }

    /**
     * 同步标签
     */
    private function tag($apidata)
    {
        $work = new WeWork('App');
        $list = $work->tagList($apidata['admin_id'], $apidata['username'], $apidata['ip']);
        if (empty($list)) {
            return false;
        }

        foreach ($list as $item) {
            $group = Db::name('fastscrm_tag_group')
                ->where('group_id', $item['group_id'])
                ->field('id')
                ->find();
            $data = [
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
                $data['group_id'] = $item['group_id'];
                $data['createtime'] = $item['create_time'];
                $last_group_id = Db::name('fastscrm_tag_group')
                    ->insert($data, false, true);
            }
            foreach ($item['tag'] as $v) {
                $tag = Db::name('fastscrm_tag')
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
                    $tag_data['group_id'] = $last_group_id;
                    $tag_data['tag_id'] = $v['id'];
                    $tag_data['createtime'] = $item['create_time'];
                    Db::name('fastscrm_tag')
                        ->insert($tag_data, false, true);
                }
            }
            $common = new Common();
            $difdata = array();
            foreach ($item['tag'] as $itemtag) {
                $itemtag['tag_id'] = $itemtag['id'];
                $difdata[] = $itemtag;
            }
            $common->difCpr('fastscrm_tag', array('group_id' => $last_group_id), 'tag_id', $difdata);


        }
        return true;
    }

    /**
     * 客户消息群发
     */
    private function customerSale($apidata)
    {
        $task = Db::name('fastscrm_customer_sale')->where('id', $apidata['id'])->find();
        $item = Db::name('fastscrm_material_item')->where('id', $task['item_id'])->find();
        $work = new WeWork('App');

        //处理媒体信息
        if (!empty($item)) {
            $item['domain'] = $apidata['domain'];
            $item = $work->dealMedia($item);
            if (empty($item)) {
                return false;
            }
        }

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
                $stores = explode(',', $task['store_id']);
                foreach ($stores as $store) {
                    $workers = Db::name('fastscrm_worker')->where('status',
                        '1')->where('find_in_set(:store_id,store_id)', ['store_id' => $store])->column('userid');
                    foreach ($workers as $worker) {
                        $owners[] = $worker;
                    }
                }
                break;
        }
        $params['attachments'] = array();
        if (empty($item)) {
            if (!empty($task['content'])) {
                $params['text'] = array('content' => $task['content']);
            } else {
                return false;
            }
        } else {
            switch ($item['type']) {
                case 0:
                    if (!empty($task['content'])) {
                        $params['text'] = array('content' => $task['content']);
                    }
                    break;
                case 1:
                    if (!empty($task['content'])) {
                        $params['text'] = array('content' => $task['content']);
                    }
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
                    if (!empty($task['content'])) {
                        $params['text'] = array('content' => $task['content']);
                    }
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
                    if (!empty($task['content'])) {
                        $params['text'] = array('content' => $task['content']);
                    }
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
                    if (!empty($task['content'])) {
                        $params['text'] = array('content' => $task['content']);
                    }
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
                    if (!empty($task['content'])) {
                        $params['text'] = array('content' => $task['content']);
                    }
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
        }

        $params['chat_type'] = 'single';
        $message = new Message();
        $owners = array_values(array_unique($owners));
        foreach ($owners as $owner) {
            $params['sender'] = $owner;
            $result = $work->messageSend($params, $apidata['admin_id'], $apidata['username'],
                $apidata['ip']);
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
     * 客户群消息群发
     */
    private function chatSale($apidata)
    {
        $task = Db::name('fastscrm_group_chat_sale')->where('id', $apidata['id'])->find();
        $item = Db::name('fastscrm_material_item')->where('id', $task['item_id'])->find();
        $work = new WeWork('App');

        //处理媒体信息
        if (!empty($item)) {
            $item['domain'] = $apidata['domain'];
            $item = $work->dealMedia($item);
            if (empty($item)) {
                return false;
            }
        }

        //消息推送
        switch ($task['typedata']) {
            case 1:
                $owners = explode(',', $task['worker_id']);
                break;
            case 2:
                $groups = Db::name('fastscrm_group_chat')->field('owner')->column('owner');
                $common = new Common();
                $departids = $common->dealDepart($task['depart_id']);
                foreach ($departids as $departid) {
                    $workers = Db::name('fastscrm_worker')->where('status',
                        '1')->where('find_in_set(:depart_id,department)', ['depart_id' => $departid])->column('userid');
                    foreach ($workers as $worker) {
                        if (in_array($worker, $groups)) {
                            $owners[] = $worker;
                        }
                    }
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
        if (empty($item)) {
            if (!empty($task['content'])) {
                $params['text'] = array('content' => $task['content']);
            } else {
                return false;
            }
        } else {
            switch ($item['type']) {
                case 0:
                    if (!empty($task['content'])) {
                        $params['text'] = array('content' => $task['content']);
                    }
                    break;
                case 1:
                    if (!empty($task['content'])) {
                        $params['text'] = array('content' => $task['content']);
                    }
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
                    if (!empty($task['content'])) {
                        $params['text'] = array('content' => $task['content']);
                    }
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
                    if (!empty($task['content'])) {
                        $params['text'] = array('content' => $task['content']);
                    }
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
                    if (!empty($task['content'])) {
                        $params['text'] = array('content' => $task['content']);
                    }
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
                    if (!empty($task['content'])) {
                        $params['text'] = array('content' => $task['content']);
                    }
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
        }

        $params['chat_type'] = 'group';
        $message = new Message();
        $owners = array_values(array_unique($owners));
        foreach ($owners as $owner) {
            $params['sender'] = $owner;
            $result = $work->messageSend($params, $apidata['admin_id'], $apidata['username'],
                $apidata['ip']);
            $data['sale_id'] = $task['id'];
            $data['userid'] = $owner;
            $data['type'] = 1;
            $data['createtime'] = time();
            if ($result['errcode'] !== 0) {
                $data['status'] = 0;
                $data['message'] = $message->getError($result['errcode']);
            } else {
                $data['msgid'] = $result['msgid'];
                $data['status'] = 1;
                $data['message'] = '执行成功';
            }
            $mygroups = Db::name('fastscrm_group_chat')->where('owner', $owner)->field('chat_id')->select();
            foreach ($mygroups as $mygroup) {
                $data['chat_id'] = $mygroup['chat_id'];
                Db::name('fastscrm_sale_log')->insert($data);
            }
        }

        return true;
    }

    /**
     * 朋友圈消息
     */
    private function momentsSale($apidata)
    {
        $task = Db::name('fastscrm_moments_sale')->where('id', $apidata['id'])->find();
        $items = Db::name('fastscrm_material_item')->where('id', 'in', $task['item_id'])->select();
        $work = new WeWork('App');
        if (empty($items)) {
            return false;
        }
        //处理媒体信息
        foreach ($items as &$item) {
            $item['domain'] = $apidata['domain'];
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
                $common = new Common();
                $departids = $common->dealDepart($task['depart_id']);
                $owners = array();
                foreach ($departids as $departid) {
                    $workers = Db::name('fastscrm_worker')->where('status',
                        '1')->where('find_in_set(:depart_id,department)', ['depart_id' => $departid])->column('userid');
                    $owners = array_merge($owners, $workers);
                }
                $owners = array_values(array_unique($owners));
                break;
            case 3:
                $stores = explode(',', $task['store_id']);
                foreach ($stores as $store) {
                    $workers = Db::name('fastscrm_worker')->where('status',
                        '1')->where('find_in_set(:store_id,store_id)', ['store_id' => $store])->column('userid');
                    foreach ($workers as $worker) {
                        $owners[] = $worker;
                    }
                }
                $owners = array_values(array_unique($owners));
                $params['visible_range']['sender_list']['user_list'] = $owners;
                break;
        }
        $params['attachments'] = array();
        foreach ($items as $item) {
            switch ($item['type']) {
                case 0:
                    if (!empty($task['content'])) {
                        $params['text'] = array('content' => $task['content']);
                    }
                    break;
                case 1:
                    if (!empty($task['content'])) {
                        $params['text'] = array('content' => $task['content']);
                    }
                    $params['attachments'][] = array(
                        'msgtype' => "image",
                        'image' => array(
                            "media_id" => $item['fj_media_id'],
                        )
                    );
                    break;
                case 3:
                    if (!empty($task['content'])) {
                        $params['text'] = array('content' => $task['content']);
                    }
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
                    if (!empty($task['content'])) {
                        $params['text'] = array('content' => $task['content']);
                    }
                    $params['attachments'][] = array(
                        'msgtype' => "video",
                        'video' => array(
                            "media_id" => $item['fj_media_id'],
                        )
                    );
                    break;
                case 6:
                    if (!empty($task['content'])) {
                        $params['text'] = array('content' => $task['content']);
                    }
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
                    if (!empty($task['content'])) {
                        $params['text'] = array('content' => $task['content']);
                    }
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
        $result = $work->momentSend($params, $apidata['admin_id'], $apidata['username'], $apidata['ip']);
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


    /**
     * 同步群统计数据
     */
    private function groupData($apidata, $cursor = 0, $page = 1)
    {
        $owners = Db::name('fastscrm_group_chat')->page($page, 100)->column('owner');
        if (!empty($owners)) {
            $work = new WeWork('App');
            for ($i = 1; $i <= 30; $i++) {
                $param['day_begin_time'] = strtotime(date('Y-m-d ', strtotime('-' . $i . ' day', time())));
                $param['owner_filter']['userid_list'] = array_values(array_unique($owners));
                $param['order_by'] = 2;
                $param['order_asc'] = 0;
                $param['offset'] = $cursor;
                $param['limit'] = 1000;
                $list = $work->getGroupData($param, $apidata['admin_id'],
                    $apidata['username'], $apidata['ip']);
                if (empty($list)) {
                    return false;
                }
                foreach ($list['items'] as $item) {
                    $data['owner'] = $item['owner'];
                    $data['stat_time'] = $param['day_begin_time'];
                    $data['updatetime'] = time();
                    $data['new_chat_cnt'] = $item['data']['new_chat_cnt'];
                    $data['chat_total'] = $item['data']['chat_total'];
                    $data['chat_has_msg'] = $item['data']['chat_has_msg'];
                    $data['new_member_cnt'] = $item['data']['new_member_cnt'];
                    $data['member_total'] = $item['data']['member_total'];
                    $data['member_has_msg'] = $item['data']['member_has_msg'];
                    $data['msg_total'] = $item['data']['msg_total'];
                    $log = Db::name('fastscrm_group_data')->where('owner',
                        $item['owner'])->where('stat_time', $data['stat_time'])->find();
                    if (!empty($log)) {
                        Db::name('fastscrm_group_data')->where('id', $log['id'])->update($data);
                    } else {
                        Db::name('fastscrm_group_data')->insert($data);
                    }

                }
            }
            $this->groupData($apidata, 0, $page + 1);
        }
        return true;
    }

    /**
     * 同步联系客户统计数据
     */
    private function customerData($apidata, $cursor = 0, $page = 1)
    {
        $worker = Db::name('fastscrm_worker')->where('status', '1')->page($page, 1)->value('userid');
        if (!empty($worker)) {
            $work = new WeWork('App');
            $param['start_time'] = strtotime(date('Y-m-d ', strtotime('-30 day', time())));
            $param['end_time'] = strtotime(date('Y-m-d ', strtotime('-1 day', time())));
            $param['userid'] = $worker;
            $list = $work->getCustomerData($param, $apidata['admin_id'], $apidata['username'],
                $apidata['ip']);
            if (empty($list)) {
                return false;
            }
            foreach ($list['behavior_data'] as $item) {
                $data['userid'] = $worker;
                $data['stat_time'] = $item['stat_time'];
                $data['updatetime'] = time();
                $data['chat_cnt'] = $item['chat_cnt'];
                $data['message_cnt'] = $item['message_cnt'];
                if (isset($item['reply_percentage'])) {
                    $data['reply_percentage'] = $item['reply_percentage'];
                }
                if (isset($item['avg_reply_time'])) {
                    $data['avg_reply_time'] = $item['avg_reply_time'];
                }
                $data['negative_feedback_cnt'] = $item['negative_feedback_cnt'];
                $data['new_apply_cnt'] = $item['new_apply_cnt'];
                $data['new_contact_cnt'] = $item['new_contact_cnt'];
                $log = Db::name('fastscrm_customer_data')->where('userid',
                    $worker)->where('stat_time', $data['stat_time'])->find();
                if (!empty($log)) {
                    Db::name('fastscrm_customer_data')->where('id', $log['id'])->update($data);
                } else {
                    Db::name('fastscrm_customer_data')->insert($data);
                }
            }
            $this->customerData($apidata, 0, $page + 1);
        }
        return true;
    }


    /**
     * 群发成员执行结果
     */
    private function sendResult($apidata)
    {
        $work = new WeWork('App');
        $logs = Db::name('fastscrm_sale_log')
            ->where('status', '1')
            ->where('send_status', '0')
            ->field('msgid,userid,type,max(id) as id')
            ->group('msgid,userid,type')
            ->select();
        foreach ($logs as $log) {
            $params['msgid'] = $log['msgid'];
            $params['userid'] = $log['userid'];
            $params['limit'] = 1000;
            $params['cursor'] = '';
            $list = $work->getSendResult($params, $apidata['admin_id'], $apidata['username'],
                $apidata['ip']);
            if (empty($list)) {
                return false;
            }
            foreach ($list['send_list'] as $item) {
                $update['send_status'] = $item['status'];
                if ($item['status'] === 1) {
                    $update['send_time'] = $item['send_time'];
                    $update['message'] = '发送成功';
                }
                if ($log['type'] == 2) {
                    Db::name('fastscrm_sale_log')
                        ->where('msgid', $log['msgid'])
                        ->where('userid', $log['userid'])
                        ->where('external_userid', $item['external_userid'])
                        ->update($update);
                } else {
                    Db::name('fastscrm_sale_log')
                        ->where('msgid', $log['msgid'])
                        ->where('userid', $log['userid'])
                        ->where('chat_id', $item['chat_id'])
                        ->update($update);
                }

            }
            while (!empty($list['next_cursor'])) {
                $params['cursor'] = $list['next_cursor'];
                $list = $work->getSendResult($params, $apidata['admin_id'], $apidata['username'],
                    $apidata['ip']);
                foreach ($list['send_list'] as $item) {
                    $update['send_status'] = $item['status'];
                    if ($item['status'] === 1) {
                        $update['send_time'] = $item['send_time'];
                        $update['message'] = '发送成功';
                    }
                    if ($log['type'] == 2) {
                        Db::name('fastscrm_sale_log')
                            ->where('msgid', $log['msgid'])
                            ->where('userid', $log['userid'])
                            ->where('external_userid', $item['external_userid'])
                            ->update($update);
                    } else {
                        Db::name('fastscrm_sale_log')
                            ->where('msgid', $log['msgid'])
                            ->where('userid', $log['userid'])
                            ->where('chat_id', $item['chat_id'])
                            ->update($update);
                    }
                }
            }
        }
        return true;
    }

    /**
     * 朋友圈执行结果
     */
    private function momentResult()
    {
        $work = new WeWork('App');
        $logs = Db::name('fastscrm_moments_sale_log')
            ->where('status', '>', '0')
            ->where('publish_status', '0')
            ->field('jobid,moment_id,max(id) as id')
            ->group('jobid,moment_id')
            ->select();
        foreach ($logs as $k => $log) {
            if (empty($log['moment_id'])) {
                $result = $work->getMomentTask(array('jobid' => $log['jobid']));
                if ($result['errcode'] == 0) {
                    switch ($result['status']) {
                        case '1':
                            Db::name('fastscrm_moments_sale_log')
                                ->where('id', $log['id'])
                                ->update(['status' => 1]);
                            break;
                        case '2':
                            Db::name('fastscrm_moments_sale_log')
                                ->where('id', $log['id'])
                                ->update(['status' => 2]);
                            break;
                        case '3':
                            Db::name('fastscrm_moments_sale_log')
                                ->where('id', $log['id'])
                                ->update(['status' => 3, 'moment_id' => $result['result']['moment_id']]);
                            if (isset($result['result']['invalid_sender_list'])) {
                                foreach ($result['result']['invalid_sender_list']['user_list'] as $item) {
                                    Db::name('fastscrm_moments_sale_log')
                                        ->where('jobid', $log['jobid'])
                                        ->where('userid', $item)
                                        ->update(['status' => 0, 'message' => '不合法的执行员工']);
                                }
                            }
                            $log['moment_id'] = $result['result']['moment_id'];
                            $this->getMomentStatus($log);
                            break;
                    }
                }
            } else {
                $this->getMomentStatus($log);
            }
        }
        return true;
    }

    /**
     * 朋友圈成员发布状态
     */
    private function getMomentStatus($log)
    {
        $param['moment_id'] = $log['moment_id'];
        $param['cursor'] = '';
        $param['limit'] = 1000;
        $work = new WeWork('App');
        $list = $work->getMomentStatus($param);
        if (empty($list)) {
            return false;
        }
        foreach ($list['task_list'] as $item) {
            $data['status'] = 3;
            $data['publish_status'] = $item['publish_status'];
            if ($item['publish_status'] === 1) {
                $data['send_time'] = time();
            }
            Db::name('fastscrm_moments_sale_log')
                ->where('jobid', $log['jobid'])
                ->where('moment_id', $log['moment_id'])
                ->where('userid', $item['userid'])
                ->update($data);
            unset($data);
        }

        while (!empty($list['next_cursor'])) {
            $params['cursor'] = $list['next_cursor'];
            $list = $work->getMomentStatus($param);
            foreach ($list['task_list'] as $item) {
                $data['status'] = 3;
                $data['publish_status'] = $item['publish_status'];
                if ($item['publish_status'] === 1) {
                    $data['send_time'] = time();
                }
                Db::name('fastscrm_moments_sale_log')
                    ->where('jobid', $log['jobid'])
                    ->where('moment_id', $log['moment_id'])
                    ->where('userid', $item['userid'])
                    ->update($data);
                unset($data);
            }
        }
        return true;
    }

    /**
     * 发送应用文本消息
     */
    private function sendMessage($apidata)
    {
        $work = new WeWork('H5');
        $work->sendMessage($apidata['data'], $apidata['admin_id'], $apidata['username'], $apidata['ip']);
        return true;
    }

    /**
     * 离职客户继承
     */
    private function resignedCustomer($apidata)
    {
        $work = new WeWork('H5');
        $resigned = Db::name('fastscrm_transfer_resigned')->where('id', $apidata['id'])->find();
        $customers = Db::name('fastscrm_transfer_resigned_customers')->where('resigned_id',
            $resigned['id'])->page(1,
            100)->column('external_userid');
        if (!empty($customers)) {
            $params = array(
                'handover_userid' => $resigned['handover_userid'],
                'takeover_userid' => $resigned['takeover_userid'],
                'external_userid' => $customers
            );
            $list = $work->resignedTransferCustomer($params, $apidata['admin_id'], $apidata['username'],
                $apidata['ip']);
            if (empty($list)) {
                return false;
            }
            $message = new Message();
            foreach ($list['customer'] as $item) {
                if ($item['errcode'] > 0) {
                    $temp['errmsg'] = $message->getError($item['errcode']);
                } else {
                    $temp['errmsg'] = '待24小时后自动接替';
                }
                Db::name('fastscrm_transfer_resigned_customers')->where('external_userid',
                    $item['external_userid'])->where('resigned_id',
                    $resigned['id'])->update(['errmsg' => $temp['errmsg']]);
            }
            $job = new AddJob();
            $param['admin_id'] = $apidata['admin_id'];
            $param['username'] = $apidata['username'];
            $param['task'] = 'resignedCustomerNext';
            $param['ip'] = $apidata['ip'];
            $param['id'] = $apidata['id'];
            $param['page'] = 2;
            $job->add($param);
        }
        return true;
    }

    /**
     * 离职客户继承
     */
    private function resignedCustomerNext($apidata)
    {
        $work = new WeWork('H5');
        $resigned = Db::name('fastscrm_transfer_resigned')->where('id', $apidata['id'])->find();
        $customers = Db::name('fastscrm_transfer_resigned_customers')->where('resigned_id',
            $resigned['id'])->page($apidata['page'],
            100)->column('external_userid');
        if (!empty($customers)) {
            $params = array(
                'handover_userid' => $resigned['handover_userid'],
                'takeover_userid' => $resigned['takeover_userid'],
                'external_userid' => $customers
            );
            $list = $work->resignedTransferCustomer($params, $apidata['admin_id'], $apidata['username'],
                $apidata['ip']);
            if (empty($list)) {
                return false;
            }
            $message = new Message();
            foreach ($list['customer'] as $item) {
                if ($item['errcode'] > 0) {
                    $temp['errmsg'] = $message->getError($item['errcode']);
                } else {
                    $temp['errmsg'] = '待24小时后自动接替';
                }
                Db::name('fastscrm_transfer_resigned_customers')->where('external_userid',
                    $item['external_userid'])->where('resigned_id',
                    $resigned['id'])->update(['errmsg' => $temp['errmsg']]);
            }
            $job = new AddJob();
            $param['admin_id'] = $apidata['admin_id'];
            $param['username'] = $apidata['username'];
            $param['task'] = 'resignedCustomerNext';
            $param['ip'] = $apidata['ip'];
            $param['id'] = $apidata['id'];
            $param['page'] = $apidata['page'] + 1;
            $job->add($param);
        }
        return true;
    }

    /**
     * 离职群聊继承
     */
    private function resignedChat($apidata, $page = 1)
    {
        $work = new WeWork('H5');
        $resigned = Db::name('fastscrm_transfer_resigned')->where('id', $apidata['id'])->find();
        $chats = Db::name('fastscrm_transfer_resigned_groupchat')->where('resigned_id', $resigned['id'])->page($page,
            100)->column('chat_id');
        if (!empty($chats)) {
            $params = array(
                'chat_id_list' => $chats,
                'new_owner' => $resigned['takeover_userid']
            );
            $list = $work->resignedTransferChat($params, $apidata['admin_id'], $apidata['username'], $apidata['ip']);
            if (empty($list)) {
                return false;
            }
            if (isset($list['failed_chat_list']) && !empty($list['failed_chat_list'])) {
                $message = new Message();
                foreach ($list['failed_chat_list'] as $item) {
                    $temp['status'] = '0';
                    $temp['errmsg'] = $message->getError($item['errcode']);
                    Db::name('fastscrm_transfer_resigned_groupchat')->where('resigned_id',
                        $resigned['id'])->where('chat_id', $item['chat_id'])->update($temp);
                    unset($chats[$item['chat_id']]);
                }
                if (!empty($chats)) {
                    $chats = implode(',', $chats);
                    $temp['status'] = '1';
                    $temp['errmsg'] = '接替成功';
                    Db::name('fastscrm_transfer_resigned_groupchat')->where('resigned_id',
                        $resigned['id'])->where('chat_id', 'in', $chats)->update($temp);
                }
            } else {
                $chats = implode(',', $chats);
                $temp['status'] = '1';
                $temp['errmsg'] = '接替成功';
                Db::name('fastscrm_transfer_resigned_groupchat')->where('resigned_id',
                    $resigned['id'])->where('chat_id', 'in', $chats)->update($temp);
            }

            $this->resignedChat($apidata, $page + 1);
        }
        return true;
    }

    /**
     * 同步离职客户接替状态
     */
    private function resignedCustomerSync($apidata)
    {
        $work = new WeWork('H5');
        $resigned = Db::name('fastscrm_transfer_resigned')->where('id', $apidata['id'])->find();
        if (!empty($resigned)) {
            $params = array(
                'handover_userid' => $resigned['handover_userid'],
                'takeover_userid' => $resigned['takeover_userid'],
            );
            $list = $work->resignedTransferResult($params, $apidata['admin_id'], $apidata['username'], $apidata['ip']);
            if (empty($list)) {
                return false;
            }
            foreach ($list['customer'] as $item) {
                $temp['status'] = $item['status'];
                $temp['takeover_time'] = $item['takeover_time'] > 0 ? $item['takeover_time'] : null;
                if ($item['status'] == 1) {
                    $temp['errmsg'] = '接替成功';
                } elseif ($item['status'] == 2) {
                    $temp['errmsg'] = '待24小时后自动接替';
                } else {
                    $temp['errmsg'] = '接替失败';
                }
                Db::name('fastscrm_transfer_resigned_customers')->where('external_userid',
                    $item['external_userid'])->where('resigned_id',
                    $resigned['id'])->update(['status' => $item['status'], 'errmsg' => $temp['errmsg']]);
            }
            if (!empty($list['next_cursor'])) {
                $job = new AddJob();
                $param['admin_id'] = $apidata['admin_id'];
                $param['username'] = $apidata['username'];
                $param['task'] = 'resignedCustomerSyncNext';
                $param['ip'] = $apidata['ip'];
                $param['id'] = $apidata['id'];
                $param['cursor'] = $list['next_cursor'];
                $job->add($param);
            }
        }
        return true;
    }

    /**
     * 同步离职客户接替状态
     */
    private function resignedCustomerSyncNext($apidata)
    {
        $work = new WeWork('H5');
        $resigned = Db::name('fastscrm_transfer_resigned')->where('id', $apidata['id'])->find();
        if (!empty($resigned)) {
            $params = array(
                'handover_userid' => $resigned['handover_userid'],
                'takeover_userid' => $resigned['takeover_userid'],
                'cursor' => $apidata['cursor']
            );
            $list = $work->resignedTransferResult($params, $apidata['admin_id'], $apidata['username'], $apidata['ip']);
            if (empty($list)) {
                return false;
            }
            foreach ($list['customer'] as $item) {
                $temp['status'] = $item['status'];
                $temp['takeover_time'] = $item['takeover_time'] > 0 ? $item['takeover_time'] : null;
                if ($item['status'] == 1) {
                    $temp['errmsg'] = '接替成功';
                } elseif ($item['status'] == 2) {
                    $temp['errmsg'] = '待24小时后自动接替';
                } else {
                    $temp['errmsg'] = '接替失败';
                }
                Db::name('fastscrm_transfer_resigned_customers')->where('external_userid',
                    $item['external_userid'])->where('resigned_id',
                    $resigned['id'])->update(['status' => $item['status'], 'errmsg' => $temp['errmsg']]);
            }
            if (!empty($list['next_cursor'])) {
                $job = new AddJob();
                $param['admin_id'] = $apidata['admin_id'];
                $param['username'] = $apidata['username'];
                $param['task'] = 'resignedCustomerSyncNext';
                $param['ip'] = $apidata['ip'];
                $param['id'] = $apidata['id'];
                $param['cursor'] = $list['next_cursor'];
                $job->add($param);
            }
        }
        return true;
    }

    /**
     * 在职客户继承
     */
    private function onJobCustomer($apidata)
    {
        $work = new WeWork('H5');
        $onjob = Db::name('fastscrm_transfer_onjob')->where('id', $apidata['id'])->find();
        $customers = Db::name('fastscrm_transfer_onjob_customers')->where('onjob_id',
            $onjob['id'])->page(1,
            100)->column('external_userid');
        if (!empty($customers)) {
            $params = array(
                'handover_userid' => $onjob['handover_userid'],
                'takeover_userid' => $onjob['takeover_userid'],
                'external_userid' => $customers
            );
            $list = $work->onJobTransferCustomer($params, $apidata['admin_id'], $apidata['username'],
                $apidata['ip']);
            if (empty($list)) {
                return false;
            }
            $message = new Message();
            foreach ($list['customer'] as $item) {
                if ($item['errcode'] > 0) {
                    $temp['errmsg'] = $message->getError($item['errcode']);
                } else {
                    $temp['errmsg'] = '待24小时后自动接替';
                }
                Db::name('fastscrm_transfer_onjob_customers')->where('external_userid',
                    $item['external_userid'])->where('onjob_id', $onjob['id'])->update(['errmsg' => $temp['errmsg']]);
            }
            $job = new AddJob();
            $param['admin_id'] = $apidata['admin_id'];
            $param['username'] = $apidata['username'];
            $param['task'] = 'onJobCustomerNext';
            $param['ip'] = $apidata['ip'];
            $param['id'] = $apidata['id'];
            $param['page'] = 2;
            $job->add($param);
        }
        return true;
    }

    /**
     * 在职客户继承
     */
    private function onJobCustomerNext($apidata)
    {
        $work = new WeWork('H5');
        $onjob = Db::name('fastscrm_transfer_onjob')->where('id', $apidata['id'])->find();
        $customers = Db::name('fastscrm_transfer_onjob_customers')->where('onjob_id',
            $onjob['id'])->page($apidata['page'],
            100)->column('external_userid');
        if (!empty($customers)) {
            $params = array(
                'handover_userid' => $onjob['handover_userid'],
                'takeover_userid' => $onjob['takeover_userid'],
                'external_userid' => $customers
            );
            $list = $work->onJobTransferCustomer($params, $apidata['admin_id'], $apidata['username'],
                $apidata['ip']);
            if (empty($list)) {
                return false;
            }
            $message = new Message();
            foreach ($list['customer'] as $item) {
                if ($item['errcode'] > 0) {
                    $temp['errmsg'] = $message->getError($item['errcode']);
                } else {
                    $temp['errmsg'] = '待24小时后自动接替';
                }
                Db::name('fastscrm_transfer_onjob_customers')->where('external_userid',
                    $item['external_userid'])->where('onjob_id', $onjob['id'])->update(['errmsg' => $temp['errmsg']]);
            }
            $job = new AddJob();
            $param['admin_id'] = $apidata['admin_id'];
            $param['username'] = $apidata['username'];
            $param['task'] = 'onJobCustomerNext';
            $param['ip'] = $apidata['ip'];
            $param['id'] = $apidata['id'];
            $param['page'] = $apidata['page'] + 1;
            $job->add($param);
        }
        return true;
    }

    /**
     * 在职群聊继承
     */
    private function onJobChat($apidata, $page = 1)
    {
        $work = new WeWork('H5');
        $onjob = Db::name('fastscrm_transfer_onjob')->where('id', $apidata['id'])->find();
        $chats = Db::name('fastscrm_transfer_onjob_groupchat')->where('onjob_id', $onjob['id'])->page($page,
            100)->column('chat_id');
        if (!empty($chats)) {
            $params = array(
                'chat_id_list' => $chats,
                'new_owner' => $onjob['takeover_userid']
            );
            $list = $work->onJobTransferChat($params, $apidata['admin_id'], $apidata['username'], $apidata['ip']);
            if (empty($list)) {
                return false;
            }
            if (isset($list['failed_chat_list']) && !empty($list['failed_chat_list'])) {
                $message = new Message();
                foreach ($list['failed_chat_list'] as $item) {
                    $temp['status'] = '0';
                    $temp['errmsg'] = $message->getError($item['errcode']);
                    Db::name('fastscrm_transfer_onjob_groupchat')->where('onjob_id', $onjob['id'])->where('chat_id',
                        $item['chat_id'])->update($temp);
                    unset($chats[$item['chat_id']]);
                }
                if (!empty($chats)) {
                    $chats = implode(',', $chats);
                    $temp['status'] = '1';
                    $temp['errmsg'] = '接替成功';
                    Db::name('fastscrm_transfer_onjob_groupchat')->where('onjob_id', $onjob['id'])->where('chat_id',
                        'in', $chats)->update($temp);
                }
            } else {
                $chats = implode(',', $chats);
                $temp['status'] = '1';
                $temp['errmsg'] = '接替成功';
                Db::name('fastscrm_transfer_onjob_groupchat')->where('onjob_id', $onjob['id'])->where('chat_id', 'in',
                    $chats)->update($temp);
            }
            $this->onJobChat($apidata, $page + 1);
        }
        return true;
    }

    /**
     * 同步在职客户接替状态
     */
    private function onJobCustomerSync($apidata)
    {
        $work = new WeWork('H5');
        $onjob = Db::name('fastscrm_transfer_onjob')->where('id', $apidata['id'])->find();
        if (!empty($onjob)) {
            $params = array(
                'handover_userid' => $onjob['handover_userid'],
                'takeover_userid' => $onjob['takeover_userid'],
            );
            $list = $work->onJobTransferResult($params, $apidata['admin_id'], $apidata['username'], $apidata['ip']);
            if (empty($list)) {
                return false;
            }
            foreach ($list['customer'] as $item) {
                $temp['status'] = $item['status'];
                $temp['takeover_time'] = $item['takeover_time'] > 0 ? $item['takeover_time'] : null;
                if ($item['status'] == 1) {
                    $temp['errmsg'] = '接替成功';
                } elseif ($item['status'] == 2) {
                    $temp['errmsg'] = '待24小时后自动接替';
                } else {
                    $temp['errmsg'] = '接替失败';
                }
                Db::name('fastscrm_transfer_onjob_customers')->where('external_userid',
                    $item['external_userid'])->where('onjob_id', $onjob['id'])->update([
                    'status' => $item['status'],
                    'errmsg' => $temp['errmsg']
                ]);
            }
            if (!empty($list['next_cursor'])) {
                $job = new AddJob();
                $param['admin_id'] = $apidata['admin_id'];
                $param['username'] = $apidata['username'];
                $param['task'] = 'onJobCustomerSyncNext';
                $param['ip'] = $apidata['ip'];
                $param['id'] = $apidata['id'];
                $param['cursor'] = $list['next_cursor'];
                $job->add($param);
            }
        }
        return true;
    }

    /**
     * 同步在职客户接替状态
     */
    private function onJobCustomerSyncNext($apidata)
    {
        $work = new WeWork('H5');
        $onjob = Db::name('fastscrm_transfer_onjob')->where('id', $apidata['id'])->find();
        if (!empty($onjob)) {
            $params = array(
                'handover_userid' => $onjob['handover_userid'],
                'takeover_userid' => $onjob['takeover_userid'],
                'cursor' => $apidata['cursor']
            );
            $list = $work->onJobTransferResult($params, $apidata['admin_id'], $apidata['username'], $apidata['ip']);
            if (empty($list)) {
                return false;
            }
            foreach ($list['customer'] as $item) {
                $temp['status'] = $item['status'];
                $temp['takeover_time'] = $item['takeover_time'] > 0 ? $item['takeover_time'] : null;
                if ($item['status'] == 1) {
                    $temp['errmsg'] = '接替成功';
                } elseif ($item['status'] == 2) {
                    $temp['errmsg'] = '待24小时后自动接替';
                } else {
                    $temp['errmsg'] = '接替失败';
                }
                Db::name('fastscrm_transfer_onjob_customers')->where('external_userid',
                    $item['external_userid'])->where('onjob_id', $onjob['id'])->update([
                    'status' => $item['status'],
                    'errmsg' => $temp['errmsg']
                ]);
            }
            if (!empty($list['next_cursor'])) {
                $job = new AddJob();
                $param['admin_id'] = $apidata['admin_id'];
                $param['username'] = $apidata['username'];
                $param['task'] = 'onJobCustomerSyncNext';
                $param['ip'] = $apidata['ip'];
                $param['id'] = $apidata['id'];
                $param['cursor'] = $list['next_cursor'];
                $job->add($param);
            }
        }
        return true;
    }

    /**
     * 同步客服
     */
    private function kf($apidata)
    {
        $work = new WeWork('App');
        $params = ['offset' => 0, 'limit' => 100];
        $list = $work->kfList($params, $apidata['admin_id'], $apidata['username'], $apidata['ip']);
        if (empty($list)) {
            return false;
        }
        $kfModel = new Account();
        foreach ($list['account_list'] as $item) {
            $log = $kfModel->where('open_kfid', $item['open_kfid'])->find();
            if (!empty($log)) {
                $kfModel->where('id', $log->id)->update($item);
            }else{
                $item['createtime'] = time();
                $item['updatetime'] = time();
                $kfModel->insert($item);
            }
        }
        if (count($list['account_list']) >= 100) {
            $job = new AddJob();
            $param['admin_id'] = $apidata['admin_id'];
            $param['username'] = $apidata['username'];
            $param['task'] = 'kfNext';
            $param['ip'] = $apidata['ip'];
            $param['offset'] = 1;
            $job->add($param);
        }
        return true;
    }

    /**
     * 同步客服
     */
    private function kfNext($apidata)
    {
        $work = new WeWork('App');
        $params = ['offset' => $apidata['offset'], 'limit' => 100];
        $list = $work->kfList($params, $apidata['admin_id'], $apidata['username'], $apidata['ip']);
        if (empty($list)) {
            return false;
        }
        $kfModel = new Account();
        foreach ($list['account_list'] as $item) {
            $log = $kfModel->where('open_kfid', $item['open_kfid'])->find();
            if (!empty($log)) {
                $kfModel->where('id', $log->id)->update($item);
            }else{
                $item['createtime'] = time();
                $item['updatetime'] = time();
                $kfModel->insert($item);
            }
        }
        if (count($list['account_list']) >= 100) {
            $job = new AddJob();
            $param['admin_id'] = $apidata['admin_id'];
            $param['username'] = $apidata['username'];
            $param['task'] = 'kfNext';
            $param['ip'] = $apidata['ip'];
            $param['offset'] = 1;
            $job->add($param);
        }
        return true;
    }

    /**
     * 同步接待人员
     */
    private function servicer($apidata)
    {
        $kfModel = new Account();
        $servicerModel = new Servicer();
        $accounts = collection($kfModel->field('id,open_kfid')->page(1,1)->select())->toArray();
        if(!empty($accounts)){
            $work = new WeWork('App');
            foreach ($accounts as $account) {
                $params = ['open_kfid' => $account['open_kfid']];
                $result = $work->servicerList($params, $apidata['admin_id'], $apidata['username'], $apidata['ip']);
                if (isset($result['servicer_list']) && !empty($result['servicer_list'])) {
                    foreach ($result['servicer_list'] as $item) {
                        if($item['status']==0){
                            $temp['open_kfid'] = $account['open_kfid'];
                            $temp['worker_id'] = $item['userid'];
                            $log = $servicerModel->where('open_kfid',$account['open_kfid'])->where('worker_id',$item['userid'])->find();
                            $temp['updatetime'] = time();
                            if(!empty($log)){
                                $servicerModel->where('id',$log->id)->update(['updatetime'=>time()]);
                            }else{
                                $temp['createtime'] = time();
                                $servicerModel->insert($temp);
                            }
                        }
                    }
                }
            }
            $job = new AddJob();
            $param['admin_id'] = $apidata['admin_id'];
            $param['username'] = $apidata['username'];
            $param['task'] = 'servicerNext';
            $param['ip'] = $apidata['ip'];
            $param['page'] = 2;
            $job->add($param);

        }
        return true;
    }

    /**
     * 同步接待人员
     */
    private function servicerNext($apidata)
    {
        $kfModel = new Account();
        $servicerModel = new Servicer();
        $accounts = collection($kfModel->field('id,open_kfid')->page($apidata['page'],1)->select())->toArray();
        if(!empty($accounts)){
            $work = new WeWork('App');
            foreach ($accounts as $account) {
                $params = ['open_kfid' => $account['open_kfid']];
                $result = $work->servicerList($params, $apidata['admin_id'], $apidata['username'], $apidata['ip']);
                if (isset($result['servicer_list']) && !empty($result['servicer_list'])) {
                    foreach ($result['servicer_list'] as $item) {
                        if($item['status']==0){
                            $temp['open_kfid'] = $account['open_kfid'];
                            $temp['worker_id'] = $item['userid'];
                            $log = $servicerModel->where('open_kfid',$account['open_kfid'])->where('worker_id',$item['userid'])->find();
                            $temp['updatetime'] = time();
                            if(!empty($log)){
                                $servicerModel->where('id',$log->id)->update(['updatetime'=>time()]);
                            }else{
                                $temp['createtime'] = time();
                                $servicerModel->insert($temp);
                            }
                        }
                    }
                }
            }
            $job = new AddJob();
            $param['admin_id'] = $apidata['admin_id'];
            $param['username'] = $apidata['username'];
            $param['task'] = 'servicerNext';
            $param['ip'] = $apidata['ip'];
            $param['page'] = $apidata['page'] + 1;
            $job->add($param);
        }
        return true;
    }
}