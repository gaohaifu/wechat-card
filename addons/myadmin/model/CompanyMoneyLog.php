<?php

namespace addons\myadmin\model;

use think\Model;

/**
 * 企业余额日志模型
 */
class CompanyMoneyLog extends Model
{

    // 表名
    protected $name = 'myadmin_company_money_log';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = '';
    // 追加属性
    protected $append = [];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id', [], 'LEFT')->setEagerlyType(1);
    }
}
