<?php

namespace addons\myadmin\model;

use think\Model;

class UserGroup extends Model
{

    // 表名
    protected $name = 'myadmin_user_group';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
        'status_text'
    ];

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }    

}
