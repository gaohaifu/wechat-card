<?php

namespace app\admin\model\smartcard;

use think\Model;


class Visitors extends Model
{

    

    

    // 表名
    protected $name = 'smartcard_visitors';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'typedata_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    
    public function getTypedataList()
    {
        return ['1' => __('Typedata 1'), '2' => __('Typedata 2'), '3' => __('Typedata 3'), '4' => __('Typedata 4'), '5' => __('Typedata 5'), '6' => __('Typedata 6'), '7' => __('Typedata 7'), '8' => __('Typedata 8'), '9' => __('Typedata 9')];
    }


    public function getTypedataTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['typedata']) ? $data['typedata'] : '');
        $list = $this->getTypedataList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function smartcardcompany()
    {
        return $this->belongsTo('app\admin\model\smartcard\Company', 'company_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function smartcardstaff()
    {
        return $this->belongsTo('app\admin\model\smartcard\Staff', 'staff_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}