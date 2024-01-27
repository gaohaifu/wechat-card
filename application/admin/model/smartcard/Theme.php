<?php

namespace app\admin\model\smartcard;

use think\Model;


class Theme extends Model
{

    

    

    // 表名
    protected $name = 'smartcard_theme';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'statusdata_text'
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
        return ['1' => __('Statusdata 1'), '2' => __('Statusdata 2')];
    }


    public function getStatusdataTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['statusdata']) ? $data['statusdata'] : '');
        $list = $this->getStatusdataList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
