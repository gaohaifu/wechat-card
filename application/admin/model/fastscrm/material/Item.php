<?php

namespace app\admin\model\fastscrm\material;

use think\Model;


class Item extends Model
{

    

    

    // 表名
    protected $name = 'fastscrm_material_item';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_text'
    ];

      public function group()
      {
            return $this->belongsTo('group', 'group_id', 'id', [], 'LEFT')->setEagerlyType(0);
      }
    

    
    public function getTypeList()
    {
//        return ['0' => __('Type 0'), '1' => __('Type 1'), '2' => __('Type 2'), '3' => __('Type 3'), '4' => __('Type 4'), '5' => __('Type 5'), '6' => __('Type 6'), '7' => __('Type 7')];
        return [ '1' => __('Type 1'),  '3' => __('Type 3'),  '5' => __('Type 5'), '6' => __('Type 6'), '7' => __('Type 7')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
