<?php

namespace app\myadmin\behavior;

class AdminLog
{
    public function run(&$params)
    {
        //只记录POST请求的日志
        if (request()->isPost() && config('fastadmin.auto_record_log')) {
            \addons\myadmin\model\AdminLog::record();
        }
    }
}
