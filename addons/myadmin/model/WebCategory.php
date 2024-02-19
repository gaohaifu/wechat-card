<?php

namespace addons\myadmin\model;

use think\Model;
use traits\model\SoftDelete;
use addons\myadmin\model\Company;


class WebCategory extends Model
{

    use SoftDelete;
    // 表名
    protected $name = 'myadmin_web_category';

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
            if(!isset($row['mould_id'])){
                throw new \think\Exception("请选择模型");
            }
            if(!WebMould::get($row['mould_id'])){
                throw new \think\Exception("该模型无效或已删除");
            }
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
        return ['article' => __('Article'), 'product' => __('Product'), 'single' => __('Single')];
    }

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id', [], 'LEFT');
    }
}
