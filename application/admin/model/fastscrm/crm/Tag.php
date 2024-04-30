<?php

namespace app\admin\model\fastscrm\crm;

use think\Model;


class Tag extends Model
{

    

    

    // 表名
    protected $name = 'fastscrm_tag';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    







    public function group()
    {
        return $this->belongsTo('app\admin\model\fastscrm\tag\Group', 'group_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
