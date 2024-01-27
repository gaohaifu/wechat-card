<?php

namespace app\admin\model\smartcard;

use think\Model;


class Goods extends Model
{

    

    

    // 表名
    protected $name = 'smartcard_goods';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'recommenddata_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    
    public function getRecommenddataList()
    {
        return ['0' => __('Recommenddata 0'), '1' => __('Recommenddata 1')];
    }


    public function getRecommenddataTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['recommenddata']) ? $data['recommenddata'] : '');
        $list = $this->getRecommenddataList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function smartcardcategory()
    {
        return $this->belongsTo('app\admin\model\smartcard\Category', 'category_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}