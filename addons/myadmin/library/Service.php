<?php

namespace addons\myadmin\library;

use think\Db;

use addons\myadmin\model\Company;
use addons\myadmin\model\UserBase as User;
use app\common\model\User as IndexUser;
use app\common\model\MoneyLog;
use app\common\model\ScoreLog;
use addons\myadmin\model\UserMoneyLog;
use addons\myadmin\model\CompanyMoneyLog;

class Service
{
    /**
     * 检查企业状态是否正常
     * @param int $company_id 企业ID
     */
    public static function getCompanyStatus($company_id)
    {
        if (!$company = Company::where('id', $company_id)->find()) {
            return false;
        }
        //检测审核中状态
        if (in_array($company['status'], ['created'])) {
            return false;
        }
        //检测禁用状态
        if (in_array($company['status'], ['expired'])) {
            return false;
        }
        //检查有效期状态
        $info = get_addon_config('myadmin');
        if (isset($info['termvalidity']) && $info['termvalidity'] == 'vip') {
            // 检测启用会员插件
            if (!$vip = get_addon_info('vip')) {
                return false;
            }
            if (!$vip['state']) {
                return false;
            }
            $vipInfo = \addons\vip\library\Service::getVipInfo($company['id']);
            if (!$vipInfo) {
                return false;
            }
        }
        return $company;
    }

    /**
     * 获取企业用户信息
     * @param int    $company_id 企业ID
     * @param int    $user_id    平台用户ID
     * @param string $name       返回字段
     */
    public static function getCompanyUser($company_id, $user_id, $name = 'id')
    {
        $name = is_array($name) ? $name : explode(',', $name);
        $info = User::where('company_id', $company_id)->where('user_id', $user_id);
        if (count($name) == 1) {
            $info = $info->value($name);
        } else {
            $info = $info->field(implode(',', $name))->find();
        }
        return $info;
    }

    /**
     * 平台用户与企业支付，可用户充值或退款
     * @param int    $company_id 企业ID
     * @param int    $user_id    平台用户ID
     * @param int    $money      余额
     * @param string $memo       备注
     */
    public static function companyMoney($company_id, $user_id, $money, $memo)
    {
        $minus = -1;
        if ($money == 0) {
            return false;
        }
        $result = false;
        Db::startTrans();
        try {
            $user_money = $money * $minus; // 转换为负数
            $company_money = $money;
            $user = IndexUser::lock(true)->find($user_id);
            $company = Company::lock(true)->find($company_id);
            if ($user && $company) {
                // 操作用户数据
                $user_after = function_exists('bcadd') ? bcadd($user->money, $user_money, 2) : $user->money + $user_money;
                // 操作企业钱包
                $company_after = function_exists('bcadd') ? bcadd($company->money, $company_money, 2) : $company->money + $company_money;
                // 根据金额判断为支出或收
                if ($money < 0) {
                    // 检查用户余额是否充足
                    if ($user_after < 0) {
                        return false;
                    }
                    $user_memo = "企业[{$company['name']}]给您付款：" . $memo;
                    $company_memo = "您给{$user['nickname']}(平台用户)付款：" . $memo;
                } else {
                    // 检查企业余额是否充足
                    if ($company_after < 0) {
                        return false;
                    }
                    $user_memo = "您给企业[{$company['name']}]付款：" . $memo;
                    $company_memo = "{$user['nickname']}(平台用户)给您付款：" . $memo;
                }
                //操作用户钱包
                $user->save(['money' => $user_after]);
                $user_before = $user->money;
                MoneyLog::create(['user_id' => $user_id, 'money' => $user_money, 'before' => $user_before, 'after' => $user_after, 'memo' => $user_memo]);
                $company->save(['money' => $company_after]);
                $before = $company->money;
                CompanyMoneyLog::create(['company_id' => $company_id, 'money' => $company_money, 'before' => $before, 'after' => $company_after, 'memo' => $company_memo]);
                $result = true;
            }
            Db::commit();
        } catch (\Exception $e) {
            $result = false;
            Db::rollback();
        }
        return $result;
    }

    /**
     * 企业支付(自己的用户) 可用户充值或退款
     * @param int    $company_id 企业ID
     * @param int    $user_id    平台用户ID
     * @param int    $money      余额
     * @param string $memo       备注
     */
    public static function companyUserMoney($company_id, $user_id, $money, $memo)
    {
        $company_user_id = User::where('company_id', $company_id)->where('user_id', $user_id)->value('id'); // 获取企业用户表ID
        $minus = -1;
        if ($money == 0 || !$company_user_id) {
            return false;
        }
        $result = false;
        Db::startTrans();
        try {
            $user_money = $money * $minus; // 转换为负数
            $user = User::lock(true)->find($company_user_id);
            $company = Company::find($company_id);
            if ($user && $company) {
                // 操作用户数据
                $user_after = function_exists('bcadd') ? bcadd($user->money, $user_money, 2) : $user->money + $user_money;
                // 根据金额判断为支出或收
                if ($money < 0) {
                    // 检查用户余额是否充足
                    if ($user_after < 0) {
                        return false;
                    }
                    $memo = "企业[{$company['name']}]给您付款：" . $memo;
                } else {
                    $memo = "您给用户[{$user['name']}]付款：" . $memo;
                }
                //操作用户钱包
                $user->save(['money' => $user_after]);
                $user_before = $user->money;
                UserMoneyLog::create(['user_id' => $user_id, 'money' => $user_money, 'before' => $user_before, 'after' => $user_after, 'memo' => $memo, 'company_id' => $company_id]);
                $result = true;
            }
            Db::commit();
        } catch (\Exception $e) {
            $result = false;
            Db::rollback();
        }
        return $result;
    }
}
