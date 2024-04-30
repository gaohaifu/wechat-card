<?php

namespace app\admin\model\fastscrm\sale;

use think\Model;


class Chatreport extends Model
{

    

    

    // 表名
    protected $name = 'fastscrm_sale_log';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_text',
        'status_text',
        'send_status_text',
        'send_time_text'
    ];
    

    
    public function getTypeList()
    {
        return ['1' => __('Type 1'), '2' => __('Type 2')];
    }

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }

    public function getSendStatusList()
    {
        return ['0' => __('Send_status 0'), '1' => __('Send_status 1'), '2' => __('Send_status 2'), '3' => __('Send_status 3')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getSendStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['send_status']) ? $data['send_status'] : '');
        $list = $this->getSendStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getSendTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['send_time']) ? $data['send_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setSendTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}
