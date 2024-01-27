<?php

namespace app\admin\model\smartcard;

use think\Model;


class Favor extends Model
{

    

    

    // 表名
    protected $name = 'smartcard_favor';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    







    public function user()
    {
        return $this->belongsTo('app\admin\model\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function smartcardtags()
    {
        return $this->belongsTo('app\admin\model\smartcard\Tags', 'tags_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function smartcardstaff()
    {
        return $this->belongsTo('app\admin\model\smartcard\Staff', 'staff_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}