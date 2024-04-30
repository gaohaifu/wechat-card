<?php

namespace app\admin\model\fastscrm\company;

use think\Model;


class Worker extends Model
{

    

    

    // 表名
    protected $name = 'fastscrm_worker';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'gender_text',
        'status_text'
    ];

    /*  public function depart()
      {
            return $this->belongsTo('fastscrm_depart', 'department')->setEagerlyType(0);
      }*/
      public function depart()
      {
            return $this->belongsTo('depart', 'department', 'depart_id', [], 'LEFT')->setEagerlyType(0);
      }

      public function storebind()
      {
            return $this->belongsTo('storebind', 'id', 'worker_id', [], 'left')->setEagerlyType(0);
      }

    
    public function getGenderList()
    {
        return ['0' => __('Gender 0'), '1' => __('Gender 1'), '2' => __('Gender 2')];
    }

    public function getStatusList()
    {
        return ['1' => __('Status 1'), '2' => __('Status 2'), '4' => __('Status 4'), '5' => __('Status 5')];
    }


    public function getGenderTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['gender']) ? $data['gender'] : '');
        $list = $this->getGenderList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
