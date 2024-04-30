<?php

namespace app\admin\model\fastscrm\crm;

use think\Model;


class Onjobchat extends Model
{

    

    

    // 表名
    protected $name = 'fastscrm_transfer_onjob_groupchat';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text'
    ];
    

    
    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1'), '2' => __('Status 2')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function chat()
    {
        return $this->belongsTo('groupchat', 'chat_id', 'chat_id', [], 'LEFT')->setEagerlyType(0);
    }



}
