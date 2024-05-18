<?php

namespace app\admin\model\fastscrm\guide;

use think\Model;
use traits\model\SoftDelete;

class Channelcode extends Model
{


    // 表名
    protected $name = 'fastscrm_channel_code';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'type_text',
        'scene_text',
        'style_text',
        'skip_verify_text',
        'is_exclusive_text'
    ];
    

    
    public function getTypeList()
    {
        return ['1' => __('Type 1'), '2' => __('Type 2')];
    }

    public function getSceneList()
    {
        return ['1' => __('Scene 1'), '2' => __('Scene 2')];
    }

    public function getStyleList()
    {
        return ['1' => __('Style 1'), '2' => __('Style 2'), '3' => __('Style 3')];
    }

    public function getSkipVerifyList()
    {
        return ['0' => __('Skip_verify 0'), '1' => __('Skip_verify 1')];
    }

    public function getIsExclusiveList()
    {
        return ['0' => __('Is_exclusive 0'), '1' => __('Is_exclusive 1')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getSceneTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['scene']) ? $data['scene'] : '');
        $list = $this->getSceneList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStyleTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['style']) ? $data['style'] : '');
        $list = $this->getStyleList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getSkipVerifyTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['skip_verify']) ? $data['skip_verify'] : '');
        $list = $this->getSkipVerifyList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsExclusiveTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_exclusive']) ? $data['is_exclusive'] : '');
        $list = $this->getIsExclusiveList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function channelgroup()
    {
        return $this->belongsTo('channelgroup', 'group_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


}
