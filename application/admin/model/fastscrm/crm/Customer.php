<?php

namespace app\admin\model\fastscrm\crm;

use think\Model;


class Customer extends Model
{

    

    

    // 表名
    protected $name = 'fastscrm_customer';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_text',
        'gender_text',
        'fl_createtime_text',
        'fl_add_way_text'
    ];
    

    
    public function getTypeList()
    {
        return ['1' => __('Type 1'), '2' => __('Type 2')];
    }

    public function getGenderList()
    {
        return ['0' => __('Gender 0'), '1' => __('Gender 1'), '2' => __('Gender 2')];
    }

    public function getFlAddWayList()
    {
        return ['0' => __('Fl_add_way 0'), '1' => __('Fl_add_way 1'), '2' => __('Fl_add_way 2'), '3' => __('Fl_add_way 3'), '4' => __('Fl_add_way 4'), '5' => __('Fl_add_way 5'), '6' => __('Fl_add_way 6'), '7' => __('Fl_add_way 7'), '8' => __('Fl_add_way 8'), '9' => __('Fl_add_way 9'), '10' => __('Fl_add_way 10'), '11' => __('Fl_add_way 11'), '12' => __('Fl_add_way 12'), '13' => __('Fl_add_way 13'), '14' => __('Fl_add_way 14'), '15' => __('Fl_add_way 15'),'201' => __('Fl_add_way 201'), '202' => __('Fl_add_way 202')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getGenderTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['gender']) ? $data['gender'] : '');
        $list = $this->getGenderList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getFlCreatetimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['fl_createtime']) ? $data['fl_createtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getFlAddWayTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['fl_add_way']) ? $data['fl_add_way'] : '');
        $list = $this->getFlAddWayList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setFlCreatetimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}
