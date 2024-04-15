<?php

namespace addons\myadmin\model;

use think\Model;

class Addon extends Model
{

    // 表名
    protected $name = 'myadmin_addon';

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
            //$row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    // 授课方式枚举
    public function getTypeList()
    {
        return ['month' => __('按月'), 'year' => __('按年'), 'forever' => __('永久')];
    }

    public function getStatusAttr($v, $d)
    {
        $status = 'normal';
        if ($d['forever']) {
            return $status;
        }
        if ($d['endtime'] < time()) {
            $status = 'expired';
        }
        return $status;
    }


    public function getAddonAttr($v, $d)
    {
        $info = get_addon_info($d['name']);
        return $info;
    }


    public function info()
    {
        return $this->hasOne(Addons::class, 'name', 'name', [], 'LEFT'); //->bind(['username']);
    }


    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id')->setEagerlyType(1);
    }

    // 获取配置
    static function config($name, $company_id)
    {
        $config = get_addon_fullconfig($name);
        $data = [];
        $company_config = self::where('company_id', $company_id)->where('isuse', 1)->where('name', $name)->value('config');
        $company_config = json_decode($company_config, true);
        foreach ($config as $key => $ad) {
            $value = $ad['value'];
            if (isset($company_config[$ad['name']])) {
                $value = $company_config[$ad['name']];
                if (is_array($value)) {
                    $value = array_merge($config[$key]['value'], $value);
                }
            }
            $data[$ad['name']] = $value;
        }
        return $data;
    }
}
