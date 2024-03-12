<?php

namespace addons\myadmin\model;

use think\Model;
use app\common\model\User;
use addons\myadmin\model\Company;
/**
 * 会员积分日志模型
 */
class UserScoreLog Extends Model
{

    // 表名
    protected $name = 'myadmin_user_score_log';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = '';
    // 追加属性
    protected $append = [
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
