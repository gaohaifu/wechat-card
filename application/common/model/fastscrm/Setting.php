<?php

namespace app\common\model\fastscrm;

use think\Model;


class Setting extends Model
{

    

    

    // 表名
    protected $name = 'fastscrm_setting';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    







}
