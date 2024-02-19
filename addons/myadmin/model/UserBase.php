<?php

namespace addons\myadmin\model;

use think\Db;
use think\Model;
use addons\myadmin\model\UserMoneyLog as MoneyLog;
use addons\myadmin\model\UserScoreLog as ScoreLog;

/**
 * 会员基础模型
 */
class UserBase extends Model
{
    // 表名
    protected $name = 'myadmin_user';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [];

    /**
     * 获取个人URL
     * @param string $value
     * @param array  $data
     * @return string
     */
    public function getUrlAttr($value, $data)
    {
        return "/u/" . $data['id'];
    }

    /**
     * 获取头像
     * @param string $value
     * @param array  $data
     * @return string
     */
    public function getAvatarAttr($value, $data)
    {
        if (!$value) {
            //如果不需要启用首字母头像，请使用
            //$value = '/assets/img/avatar.png';
            $value = letter_avatar($data['nickname']);
        }
        return $value;
    }

    /**
     * 获取会员的组别
     */
    public function getGroupAttr($value, $data)
    {
        return UserGroup::get($data['group_id']);
    }

    /**
     * 获取验证字段数组值
     * @param string $value
     * @param array  $data
     * @return  object
     */
    public function getVerificationAttr($value, $data)
    {
        $value = array_filter((array)json_decode($value, true));
        $value = array_merge(['email' => 0, 'mobile' => 0], $value);
        return (object)$value;
    }

    /**
     * 设置验证字段
     * @param mixed $value
     * @return string
     */
    public function setVerificationAttr($value)
    {
        $value = is_object($value) || is_array($value) ? json_encode($value) : $value;
        return $value;
    }

    /**
     * 变更会员余额
     * @param int    $user_id    会员ID
     * @param int    $money      余额
     * @param string $memo       备注
     */
    public static function money($user_id, $money, $memo)
    {
        Db::startTrans();
        try {
            $user = self::lock(true)->find($user_id);
            if ($user && $money != 0) {
                $before = $user->money;
                //$after = $user->money + $money;
                $after = function_exists('bcadd') ? bcadd($user->money, $money, 2) : $user->money + $money;
                //更新会员信息
                $user->save(['money' => $after]);
                //写入日志
                MoneyLog::create(['user_id' => $user['user_id'], 'money' => $money, 'before' => $before, 'after' => $after, 'memo' => $memo, 'company_id' => $user['company_id']]);
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
        }
    }

    /**
     * 变更会员积分
     * @param int    $company_id 企业ID
     * @param int    $user_id    会员ID
     * @param int    $score      积分
     * @param string $memo       备注
     */
    public static function score($user_id, $score, $memo)
    {
        Db::startTrans();
        try {
            $user = self::lock(true)->find($user_id);
            if ($user && $score != 0) {
                $before = $user->score;
                $after = $user->score + $score;
                //更新会员信息
                $user->save(['score' => $after]);
                //写入日志
                ScoreLog::create(['user_id' => $user['user_id'], 'score' => $score, 'before' => $before, 'after' => $after, 'memo' => $memo, 'company_id' => $user['company_id']]);
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
        }
    }

    /**
     * 申请开通会员
     * @param int    $company_id 企业ID
     * @param int    $user_id    会员ID
     * @param string $name       名称
     */
    public static function apply($company_id, $user_id, $name = '')
    {
        $user = self::where('company_id', $company_id)->where('user_id', $user_id)->find();
        if ($user) {
            return '已是该企业会员';
        }
        $use_data = [];
        $use_data['company_id'] = $company_id;
        $use_data['user_id'] = $user_id;
        $use_data['name'] = $name;
        $use_data['jointime'] = time();
        $use_data['money'] = 0;
        $use_data['score'] = 0;
        $use_data['status'] = 'hidden';        
        Db::startTrans();
        try {
            $rs = (new UserBase)->save($use_data);
            Db::commit();
        } catch (\Exception $e) {
            $rs = false;
            Db::rollback();
        }
        if ($rs) {
            return true;
        }
        return '开通失败';
    }
}
