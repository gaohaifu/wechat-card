<?php

namespace app\admin\model\fastscrm\message;

use think\Model;


class Sendlog extends Model
{

    

    

    // 表名
    protected $name = 'fastscrm_webhook_send_log';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text'
    ];
    

    
    public function getStatusList()
    {
        return ['-1' => __('Status -1'), '0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function send()
    {
        return $this->belongsTo('app\admin\model\fastscrm\message\Send', 'send_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function webhook()
    {
        return $this->belongsTo('app\admin\model\fastscrm\message\Webhook', 'webhook_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
