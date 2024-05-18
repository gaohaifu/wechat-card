<?php

namespace app\admin\model\fastscrm\crm;

use think\Model;


class Groupuser extends Model
{

    

    

    // 表名
    protected $name = 'fastscrm_group_user';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_text',
        'join_time_text',
        'join_scene_text'
    ];
    

    
    public function getTypeList()
    {
        return ['1' => __('Type 1'), '2' => __('Type 2')];
    }

    public function getJoinSceneList()
    {
        return ['1' => __('Join_scene 1'), '2' => __('Join_scene 2')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getJoinTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['join_time']) ? $data['join_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getJoinSceneTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['join_scene']) ? $data['join_scene'] : '');
        $list = $this->getJoinSceneList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setJoinTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}
