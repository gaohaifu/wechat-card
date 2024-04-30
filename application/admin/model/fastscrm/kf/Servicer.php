<?php

namespace app\admin\model\fastscrm\kf;

use think\Model;


class Servicer extends Model
{

    

    

    // 表名
    protected $name = 'fastscrm_kf_servicer';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];


    public function account()
    {
        return $this->belongsTo('app\admin\model\fastscrm\kf\Account', 'open_kfid', 'open_kfid', [], 'LEFT')->setEagerlyType(0);
    }

    public function worker()
    {
        return $this->belongsTo('app\admin\model\fastscrm\company\Worker', 'worker_id', 'userid', [], 'LEFT')->setEagerlyType(0);
    }




}
