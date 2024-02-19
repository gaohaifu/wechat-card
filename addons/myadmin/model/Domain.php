<?php

namespace addons\myadmin\model;

use think\Model;

class Domain extends Model
{

    // 表名
    protected $name = 'myadmin_domain';

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

    public function getStatusList()
    {
        return ['created' => __('待审'), 'normal' => __('正常'), 'expired' => __('无效')];
    }

    public function getAddonAttr($v, $d)
    {
        $info = get_addon_info($d['name']);
        return $info;
    }
    
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id')->setEagerlyType(1);
    }
}
