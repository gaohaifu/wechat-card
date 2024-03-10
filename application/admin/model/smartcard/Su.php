<?php

namespace app\admin\model\smartcard;

use think\Model;


class Su extends Model
{

    

    

    // 表名
    protected $name = 'smartcard_su';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text',
        'is_save_text'
    ];
    

    
    public function getStatusList()
    {
        return ['1' => __('Status 1'), '2' => __('Status 2')];
    }

    public function getIsSaveList()
    {
        return ['1' => __('Is_save 1'), '2' => __('Is_save 2')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsSaveTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_save']) ? $data['is_save'] : '');
        $list = $this->getIsSaveList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
