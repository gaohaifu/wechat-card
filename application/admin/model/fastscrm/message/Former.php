<?php

namespace app\admin\model\fastscrm\message;

use think\Model;
use traits\model\SoftDelete;

class Former extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'fastscrm_message_template';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'msg_type_text',
        'status_text',
        'expiredtime_text'
    ];
    

    
    public function getMsgTypeList()
    {
        return ['text' => __('Msg_type text'), 'markdown' => __('Msg_type markdown'), 'image' => __('Msg_type image'), 'news' => __('Msg_type news'), 'file' => __('Msg_type file')];
    }

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getMsgTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['msg_type']) ? $data['msg_type'] : '');
        $list = $this->getMsgTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getExpiredtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['expiredtime']) ? $data['expiredtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setExpiredtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}
