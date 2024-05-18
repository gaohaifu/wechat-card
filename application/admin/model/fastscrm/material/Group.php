<?php

namespace app\admin\model\fastscrm\material;

use think\Model;


class Group extends Model
{

    

    

    // 表名
    protected $name = 'fastscrm_material_group';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    







}
