<?php

namespace app\admin\model\fastscrm\convert;

use think\Model;


class Replylog extends Model
{

    

    

    // 表名
    protected $name = 'fastscrm_reply_log';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'entry_text'
    ];
    

    
    public function getEntryList()
    {
        return ['single_chat_tools' => __('Entry single_chat_tools'), 'group_chat_tools' => __('Entry group_chat_tools'), 'chat_attachment' => __('Entry chat_attachment')];
    }


    public function getEntryTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['entry']) ? $data['entry'] : '');
        $list = $this->getEntryList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
