<?php

namespace addons\myadmin\model;

use think\Model;

class Addons extends Model
{

    // 表名
    protected $name = 'myadmin_addons';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [];

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
        });
    }

    // 授课方式枚举
    public function getTypeList()
    {
        return ['month' => __('按月'), 'year' => __('按年'), 'forever' => __('永久')];
    }
    
    public function getStatussAttr($v, $d)
    {
        $status = 'normal';
        return $status;
    }
    public function getAddonAttr($v, $d)
    {
        $info = get_addon_info($d['name']);
        return $info;
    }
}
