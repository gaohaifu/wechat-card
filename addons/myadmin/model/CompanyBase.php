<?php

namespace addons\myadmin\model;

use think\Db;
use think\Model;
use addons\myadmin\model\CompanyMoneyLog as MoneyLog;
use addons\myadmin\model\CompanyScoreLog as ScoreLog;

/**
 * 企业基础模型
 */
class CompanyBase extends Model
{
    // 表名
    protected $name = 'myadmin_company';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [];
    
    protected $hidden = ['deletetime','updatetime'];

    /**
     * 获取Logo
     * @param string $value
     * @param array  $data
     * @return string
     */
    public function getLogoAttr($value, $data)
    {
        if ($value) {
            return letter_avatar($data['name']);
        }
        return cdnurl($value, true);
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
     * 变更企业余额
     * @param int    $money   余额
     * @param int    $company_id 企业ID
     * @param string $memo    备注
     */
    public static function money($money, $company_id, $memo)
    {
        $result = false;
        Db::startTrans();
        try {
            $company = self::lock(true)->find($company_id);
            if ($company && $money != 0) {
                $before = $company->money;
                $after = function_exists('bcadd') ? bcadd($company->money, $money, 2) : $company->money + $money;
                if ($after >= 0) {
                    //更新企业信息
                    $company->save(['money' => $after]);
                    //写入日志
                    MoneyLog::create(['company_id' => $company_id, 'money' => $money, 'before' => $before, 'after' => $after, 'memo' => $memo]);
                    $result = true;
                }
            }
            Db::commit();
        } catch (\Exception $e) {
            $result = false;
            Db::rollback();
        }
        return $result;
    }

    /**
     * 变更企业积分
     * @param int    $score   积分
     * @param int    $company_id 企业ID
     * @param string $memo    备注
     */
    public static function score($score, $company_id, $memo)
    {
        Db::startTrans();
        try {
            $company = self::lock(true)->find($company_id);
            if ($company && $score != 0) {
                $before = $company->score;
                $after = $company->score + $score;
                //更新企业信息
                $company->save(['score' => $after]);
                //写入日志
                ScoreLog::create(['company_id' => $company_id, 'score' => $score, 'before' => $before, 'after' => $after, 'memo' => $memo]);
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
        }
    }
}
