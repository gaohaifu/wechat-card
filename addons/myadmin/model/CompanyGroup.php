<?php

namespace addons\myadmin\model;

use think\Model;
use addons\myadmin\model\Company;


class CompanyGroup extends Model
{

    // 表名
    protected $name = 'myadmin_company_group';

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
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    // 状态式枚举
    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden'), 'expired' => __('Expired')];
    }
    // 类别枚举
    public function getTypeList()
    {
        return ['article' => __('Article'), 'product' => __('Product')];
    }

    public function getLabelnameAttr($value, $data)
    {
        if ($label = json_decode($data['label'], true)) {
            $arr = [];
            foreach ($label as $v) {
                $arr[] = $v['name'];
            }
            return $arr;
        }
        return [];
    }

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id', [], 'LEFT');
    }
}
