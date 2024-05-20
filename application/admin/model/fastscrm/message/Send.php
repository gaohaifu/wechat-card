<?php

namespace app\admin\model\fastscrm\message;

use think\Model;


class Send extends Model
{

    

    

    // 表名
    protected $name = 'fastscrm_webhook_send';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'mentioned_type_text',
        'type_text',
        'fixedtime_text'
    ];
    

    
    public function getTypeList()
    {
        return ['1' => __('Type 1'), '2' => __('Type 2')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getMentionedTypeList()
    {
        return ['1' => __('Mentioned_type 1'), '2' => __('Mentioned_type 2')];
    }


    public function getMentionedTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['mentioned_type']) ? $data['mentioned_type'] : '');
        $list = $this->getMentionedTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getFixedtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['fixedtime']) ? $data['fixedtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setFixedtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    public function template()
    {
        return $this->belongsTo('app\admin\model\fastscrm\message\Former', 'template_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
