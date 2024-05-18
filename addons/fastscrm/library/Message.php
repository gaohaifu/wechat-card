<?php

namespace addons\fastscrm\library;

use think\Controller;
use think\Db;
use think\Lang;

/**
 * 消息类
 */
class Message extends Controller
{

    /**
     * 获取报错信息
     */
    public function getError($errcode)
    {
        $message = Lang::load(ROOT_PATH . '/addons/fastscrm/lang/api/zh-cn.php');
        if (isset($message[$errcode])) {
            return $message[$errcode];
        } else {
            return '未知错误';
        }

    }


    /**
     * 记录日志
     */
    public function addLog($title, $message, $status, $admin_id, $username,$ip)
    {
        $data['admin_id'] = $admin_id;
        $data['username'] = $username;
        $data['title'] = $title;
        $data['ip'] = $ip;
        $data['status'] = $status;
        $data['createtime'] = time();
        $data['message'] = $message;
        Db::name('fastscrm_task_log')
            ->insert($data, false, true);
        return true;
    }


}