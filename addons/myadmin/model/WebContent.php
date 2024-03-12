<?php

namespace addons\myadmin\model;

use think\Model;
use think\Db;
use traits\model\SoftDelete;
use fast\Random;
use addons\myadmin\model\Company;

class WebContent extends Model
{
    use SoftDelete;
    // 表名
    protected $name = 'myadmin_web_content';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';
    // 追加属性
    protected $append = [];

    protected static function init()
    {
        self::afterInsert(function ($row) {
            self::afterData($row);
        });
        self::afterUpdate(function ($row) {
            self::afterData($row);
        });
    }

    protected static function afterData($row)
    {
        $pk = $row->getPk();
        $data = [];
        $category = WebCategory::get($row['category_id']);
        $data['mould_id'] = $category['mould_id'];
        // 判断公共类
        if ($category['company_id']) {
            $company = Company::get($category['company_id']);
            if ($company) {
                $data['company_id'] = $company['id'];
            }
        } else {
            if (!isset($row['company_id']) || !$row['company_id']) {
                throw new \think\Exception("请选择关联企业");
            }
            $data['company_id'] = $row['company_id'];
        }
        if ($data) {
            $row->getQuery()->where($pk, $row[$pk])->update($data);
        }
    }
    // 状态式枚举
    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden'), 'expired' => __('Expired')];
    }
    // 类型枚举
    public function getTypeList()
    {
        return ['article' => __('Article'), 'image' => __('Image'), 'image' => __('Image'), 'video' => __('video'), 'audio' => __('audio')];
    }
    // 关联分类
    public function category()
    {
        return $this->hasOne(WebCategory::class, 'id', 'category_id', [], 'LEFT');
    }
    // 关联企业
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id', [], 'LEFT')->setEagerlyType(1);
    }
}
