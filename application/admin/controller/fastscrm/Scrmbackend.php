<?php

namespace app\myadmin\controller\fastscrm;

use addons\fastscrm\library\Job\AddJob;
use app\common\controller\Backend;
use fast\Date;
use fast\Http;
use think\Cache;

/**
 * 后台控制器基类
 */
class Scrmbackend extends Backend
{

    public function _initialize()
    {
        parent::_initialize();
        $expire = Date::span(time(), strtotime(date('Y-m-d 23:59:59', time())), 'seconds');
        $job =  new AddJob();
        $param['admin_id'] = $this->auth->isLogin() ? $this->auth->id : 0;
        $param['username'] = $this->auth->isLogin() ? $this->auth->username : __('Unknown');
        $param['ip'] = request()->ip();
        if (!Cache::has('groupData')) {
            Cache::set('groupData', true, $expire);
            $param['task'] = 'groupData';
            $job->add($param);
        }
        if (!Cache::has('customerData')) {
            Cache::set('customerData', true, $expire);
            $param['task'] = 'customerData';
            $job->add($param);
        }
        if (!Cache::has('sendResult')) {
            Cache::set('sendResult', true, $expire);
            $param['task'] = 'sendResult';
            $job->add($param);
        }
        if (!Cache::has('momentResult')) {
            Cache::set('momentResult', true, $expire);
            $param['task'] = 'momentResult';
            $job->add($param);
        }

    }
}
