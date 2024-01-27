<?php

namespace app\admin\model\smartcard;

use think\Model;
use app\admin\model\User;
use think\Db;


class Company extends Model
{


    // 表名
    protected $name = 'smartcard_company';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    
    public function ckdadministrators()
    {
        return $this->belongsTo('app\admin\model\User', 'administrators_ids', 'id', [], 'LEFT')->setEagerlyType(0);
    }


}