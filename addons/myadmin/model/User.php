<?php

namespace addons\myadmin\model;

use addons\myadmin\model\UserMoneyLog as MoneyLog;
use addons\myadmin\model\UserScoreLog as ScoreLog;
use think\Model;
use app\common\model\User as IndexUser;

class User extends Model
{

    // 表名
    protected $name = 'myadmin_user';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [];

    public function getOriginData()
    {
        return $this->origin;
    }

    protected static function init()
    {
        self::beforeInsert(function ($row) {
            $user = IndexUser::get($row['user_id']);
            if ($user) {
                $row['name'] = $user['username'];
            }
            $row['jointime'] = time();
        });
        self::afterInsert(function ($row) {
            if ($row['money']) {
                MoneyLog::create(['user_id' => $row['user_id'], 'money' => $row['money'] - 0, 'before' => 0, 'after' => $row['money'], 'memo' => '管理员变更金额', 'company_id' => $row['company_id']]);
            }
            if ($row['score']) {
                ScoreLog::create(['user_id' => $row['user_id'], 'score' => $row['score'] - 0, 'before' => 0, 'after' => $row['score'], 'memo' => '管理员变更积分', 'company_id' => $row['company_id']]);
            }
        });

        self::beforeUpdate(function ($row) {
            $user = IndexUser::get($row['user_id']);
            if ($user && !$row['name']) {
                $row['name'] = $user['username'];
            }
            $changedata = $row->getChangedData();
            $origin = $row->getOriginData();
            if (isset($changedata['money']) && (function_exists('bccomp') ? bccomp($changedata['money'], $origin['money'], 2) !== 0 : (float) $changedata['money'] !== (float) $origin['money'])) {
                MoneyLog::create(['user_id' => $row['user_id'], 'money' => $changedata['money'] - $origin['money'], 'before' => $origin['money'], 'after' => $changedata['money'], 'memo' => '管理员变更金额', 'company_id' => $row['company_id']]);
            }
            if (isset($changedata['score']) && (int) $changedata['score'] !== (int) $origin['score']) {
                ScoreLog::create(['user_id' => $row['user_id'], 'score' => $changedata['score'] - $origin['score'], 'before' => $origin['score'], 'after' => $changedata['score'], 'memo' => '管理员变更积分', 'company_id' => $row['company_id']]);
            }
        });
    }

    public function getGenderList()
    {
        return ['1' => __('Male'), '0' => __('Female')];
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }

    public function getPrevtimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['prevtime'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    public function getLogintimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['logintime'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    public function getJointimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['jointime'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setPrevtimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setLogintimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setJointimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setBirthdayAttr($value)
    {
        return $value ? $value : null;
    }


    public function bindinfo()
    {
        return $this->belongsTo(IndexUser::class, 'user_id', 'id', [], 'LEFT')->setEagerlyType(1)->bind('username,nickname');
        return $this->hasOne(IndexUser::class, 'id', 'user_id')->bind('username,nickname');
    }


    public function info()
    {
        return $this->belongsTo(IndexUser::class, 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function group()
    {
        return $this->belongsTo('UserGroup', 'group_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
