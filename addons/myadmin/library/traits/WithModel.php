<?php

namespace addons\myadmin\library\traits;

use addons\myadmin\model\CompanyBase;

/**
 * 通用数据关联模型
 */
trait WithModel
{
    // 关联企业
    public function company()
    {
        return $this->belongsTo(CompanyBase::class, 'company_id', 'id', [], 'LEFT')->setEagerlyType(1);
    }
}
