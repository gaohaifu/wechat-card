<?php

namespace app\admin\model\smartcard;

use think\Model;


class Staff extends Model
{

    

    

    // 表名
    protected $name = 'smartcard_staff';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }
    
    public function getStatusdataList()
    {
        return ['1' => __('Statusdata 1'), '2' => __('Statusdata 2'),'3' => __('Statusdata 3'),'4' => __('Statusdata 4')];
    }


    public function user()
    {
        return $this->belongsTo('app\admin\model\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
     public function smartcardtheme()
    {
        return $this->belongsTo('app\admin\model\smartcard\Theme', 'theme_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
     public function smartcardcompany()
    {
        return $this->belongsTo('app\admin\model\smartcard\Company', 'company_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}