<?php

namespace app\admin\model\fastscrm\message;

use think\Model;
use traits\model\SoftDelete;

class Webhook extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'fastscrm_webhook';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'status_text'
    ];
    

    
    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function group()
    {
        return $this->belongsTo('app\admin\model\fastscrm\message\Webhookgroup', 'group_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }



}
