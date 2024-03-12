<?php

namespace addons\myadmin\model;

use think\Exception;
use think\Model;

class AuthPlayer extends Model
{

    // 表名
    protected $name = 'myadmin_auth_player';

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
        // 写入前
        self::beforeInsert(function ($row) {
            if (!$row['label']) {
                throw new Exception("请设置标签");
            }
            $exist = self::where('label', $row['label'])->count();
            if ($exist) {
                throw new Exception("标签已存在，请换一个");
            }
        });
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
}
