<?php

namespace addons\myadmin\model;

use think\Model;
use think\Db;
use traits\model\SoftDelete;
use fast\Random;
use app\common\model\User;
use  addons\myadmin\model\User as CompanyUser;


class Company extends Model
{

    use SoftDelete;
    // 表名
    protected $name = 'myadmin_company';

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
        self::beforeDelete(function ($row) {
            // 判断真实删除时同时删除关联数据
            if ($row['deletetime']) {
                $where['company_id'] = $row['id'];
                CompanyMoneyLog::where($where)->delete(true);
                CompanyScoreLog::where($where)->delete(true);
                CompanyWithdraw::where($where)->delete(true);
                ConfigValue::where($where)->delete(true);
                Admin::where($where)->delete(true);
                AdminLog::where($where)->delete(true);
                AuthGroup::where($where)->delete(true);
                AuthGroupAccess::where($where)->delete(true);
                Attachment::where($where)->delete(true);
                Domain::where($where)->delete(true);
                CompanyUser::where($where)->delete(true);
                UserGroup::where($where)->delete(true);
                UserMoneyLog::where($where)->delete(true);
                UserScoreLog::where($where)->delete(true);
            }
        });
    }

    protected static function afterData($row)
    {
        $pk = $row->getPk();
        $data = [];
        if (!isset($row['identifier']) || !$row['identifier']) {
            $data['identifier'] = strtoupper(Random::alpha(2) . Random::numeric(6)); // 生成唯一识别码
        }
        if ($data) {
            $row->getQuery()->where($pk, $row[$pk])->update($data);
        }
    }

    public function getStatusList()
    {
        return ['created' => __('Created'), 'normal' => __('Normal'), 'hidden' => __('Hidden'), 'expired' => __('Expired')];
    }

    public function getAuditList()
    {
        return ['0' => __('未认证'), '1' => __('待审核'), '2' => __('通过'), '3' => __('驳回')];
    }

    protected function setBegintimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value . ' 00:00:00') : $value);
    }

    protected function setEndtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value . ' 23:59:59') : $value);
    }

    public function group()
    {
        return $this->belongsTo(CompanyGroup::class, 'group_id')->setEagerlyType(1);
    }

    public function founder()
    {
        return $this->hasOne(Admin::class, 'company_id', 'id', [], 'LEFT')->where('is_founder', 1)->field('id,username,nickname,company_id'); //->bind(['username']);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'id', [], 'LEFT'); //->bind(['username']);
    }


    /**
     * 获取企业身份
     */
    public static function getPlayer($company_id = 0)
    {
        $data = [];
        $log = CompanyPlayer::where('company_id', $company_id)->where('status', 'normal')->column('expiredtime', 'player_id');
        $player_id = [];
        foreach ($log as $k => $ov) {
            $player_id[] = $k;
        }
        $player = AuthPlayer::where('id', 'in', $player_id)->where('status', 'in', ['normal', 'hidden'])->column('id,name,label', 'label');
        foreach ($player as $ks => $os) {
            $expiredtime = $log[$os['id']];
            $status = $expiredtime < time() ? 0 : 1;
            $rs = ['id' => $os['id'], 'name' => $os['name'], 'status' => $status, 'expired' => $expiredtime, 'date' => date('Y-m-d', $expiredtime)];
            $data[$os['label']] = $rs;
        }
        return $data;
    }

    /**
     * 获取企业权限
     */
    public static function getRules($company_id = 0)
    {
        $player_id = CompanyPlayer::where('company_id', $company_id)->where('status', 'normal')->column('player_id');
        $player = AuthPlayer::where('id', 'in', $player_id)->where('status', 'in', ['normal', 'hidden'])->column('rules');
        $rules = [];
        foreach ($player as $ov) {
            if ($ov) {
                $rules = array_unique(array_merge($rules, explode(',', $ov)));
            }
        }
        $rules = array_values($rules);
        return $rules;
    }
}
