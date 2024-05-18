<?php

namespace app\admin\model\fastscrm\risk;

use think\Model;


class Intercept extends Model
{

    

    

    // 表名
    protected $name = 'fastscrm_intercept';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'typedata_text',
        'intercept_type_text'
    ];
    

    
    public function getTypedataList()
    {
        return ['1' => __('Typedata 1'), '2' => __('Typedata 2')];
    }

    public function getInterceptTypeList()
    {
        return ['1' => __('Intercept_type 1'), '2' => __('Intercept_type 2')];
    }


    public function getTypedataTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['typedata']) ? $data['typedata'] : '');
        $list = $this->getTypedataList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getInterceptTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['intercept_type']) ? $data['intercept_type'] : '');
        $list = $this->getInterceptTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function group()
    {
        return $this->belongsTo('app\admin\model\fastscrm\risk\Interceptgroup', 'group_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


}
