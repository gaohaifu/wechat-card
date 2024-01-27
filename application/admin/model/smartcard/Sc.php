<?php

namespace app\admin\model\smartcard;

use think\Model;


class Sc extends Model
{

    

    

    // 表名
    protected $name = 'smartcard_sc';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    
    public function getStatusList()
    {
        return ['1' => __('Status 1'), '2' => __('Status 2'), '3' => __('Status 3'), '4' => __('Status 4')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function smartcardstaff()
    {
        return $this->belongsTo('app\admin\model\smartcard\Staff', 'staff_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function smartcardcompany()
    {
        return $this->belongsTo('app\admin\model\smartcard\Company', 'company_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}