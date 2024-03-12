<?php

namespace addons\myadmin\model;

use think\Model;

class ConfigValue extends Model
{
    protected $name = 'myadmin_config_value';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;

}
