<?php

namespace app\admin\model\fastscrm\crm;

use think\Model;


class Groupadmin extends Model
{

    

    

    // 表名
    protected $name = 'fastscrm_group_admin';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    







}
