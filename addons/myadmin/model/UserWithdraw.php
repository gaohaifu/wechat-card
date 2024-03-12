<?php

namespace addons\myadmin\model;


use app\common\model\User as IndexUser;
use addons\myadmin\model\CompanyBase;
use addons\myadmin\model\User;
use addons\myadmin\model\UserBase;
use think\Exception;
use think\Model;
use Yansongda\Pay\Pay;
use app\common\model\User as CommonUser;

use addons\myadmin\library\Service;


class UserWithdraw extends Model
{


    // 表名
    protected $name = 'myadmin_user_withdraw';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text',
        'settledmoney'
    ];

    public static function init()
    {
        self::afterInsert(function ($row) {
            $changedData = $row->getChangedData();
            if (isset($changedData['status']) && $changedData['status'] == 'agreed') {
                CompanyBase::money(-$row['money'], $row['company_id'], '提现');
            }
        });
        self::beforeWrite(function ($row) {
            if (!isset($row['orderid']) || !$row['orderid']) {
                $row['orderid'] = date("YmdHis") . sprintf("%08d", $row['company_id']) . mt_rand(1000, 9999);
            }
        });
        self::beforeDelete(function ($row) {
            if ($row['status'] == 'created') {
                throw new Exception("该记录无法删除");
            }
        });
    }

    public function getSettledmoneysAttr($value, $data)
    {
        return max(0, sprintf("%.2f", $data['money'] - $data['handrate'] - $data['taxerate']));
    }

    public function getStatusList()
    {
        return ['created' => __('Status created'), 'successed' => __('Status successed'), 'rejected' => __('Status rejected')];
    }

    public function getTypeList()
    {
        return ['bank' => __('Bank'), 'wechat' => __('Wechat'), 'alipay' => __('Alipay')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function company()
    {
        return $this->belongsTo(Company::class, "company_id", "id")->setEagerlyType(0);
    }

    public function user()
    {
        return $this->belongsTo(CommonUser::class, "user_id", "id")->setEagerlyType(0);
    }

    public static function apply($company_id, $user_id, $money, $type, $name, $account, $memo)
    {
        $where_user['company_id'] = $company_id;
        $where_user['user_id'] = $user_id;
        $user = User::where($where_user)->find();
        if ($money <= 0) {
            return '请输入提现金额';
        }
        if ($money > $user['money']) {
            return '可提现金额不足，最多可提：￥' . $user['money'];
        }
        $handrate = $user['handrate'];
        $taxerate = $user['taxerate'];
        $handfee = $handrate / 100 * $money;
        $taxefee = $taxerate / 100 * $money;
        $settledmoney = max(0, sprintf("%.2f", $money - $handfee - $taxefee));
        $data['user_id'] = $user['user_id'];
        $data['company_user_id'] = $user['id'];
        $data['company_id'] = $user['company_id'];
        $data['money'] = $money;
        $data['handrate'] = $handrate;
        $data['taxerate'] = $taxerate;
        $data['handfee'] = $handfee;
        $data['taxefee'] = $taxefee;
        $data['settledmoney'] = $settledmoney;
        $data['type'] = $type;
        $data['name'] = $name;
        $data['account'] = $account;
        $data['memo'] = $memo;
        $data['status'] = 'created';
        (new UserWithdraw)->save($data);
        UserBase::money($user['id'],  $money * -1,  '申请提现');
        return true;
    }
}
