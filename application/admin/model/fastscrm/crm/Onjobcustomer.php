<?php

namespace app\admin\model\fastscrm\crm;

use think\Model;


class Onjobcustomer extends Model
{

    

    

    // 表名
    protected $name = 'fastscrm_transfer_onjob_customers';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text',
        'takeover_time_text'
    ];
    

    
    public function getStatusList()
    {
        return ['1' => __('Status 1'), '2' => __('Status 2'), '3' => __('Status 3'), '4' => __('Status 4')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getTakeoverTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['takeover_time']) ? $data['takeover_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setTakeoverTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    public function customer()
    {
        return $this->belongsTo('customer', 'external_userid', 'external_userid', [], 'LEFT')->setEagerlyType(0);
    }
}
