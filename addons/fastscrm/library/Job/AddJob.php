<?php

namespace addons\fastscrm\library\Job;

use think\Config;
use think\Exception;
use think\Controller;
use think\helper\Str;
use think\Queue;
use think\Db;
use think\Lang;

class AddJob extends Controller
{
    public function add($jobData)
    {
        $options = Config::get('queue');
        if (isset($options['connector'])) {
            if (Str::studly($options['connector']) != 'Redis') {
                $this->error('请先根据文档配置队列启动为redis');
            }
            if (!extension_loaded('redis')) {
                $this->error('php-redis扩展未安装');
            }

        } else {
            $this->error('请先安装think-queue依赖');
        }

        $jobHandlerClassName = 'addons\fastscrm\library\Job\DoJob';
        $jobQueueName = "ScrmQueue";
        $isPushed = Queue::push($jobHandlerClassName, $jobData, $jobQueueName);
        if ($isPushed !== false) {
            return true;
        } else {
            return false;
        }


    }
}