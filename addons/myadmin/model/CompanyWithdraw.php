<?php

namespace addons\myadmin\model;

use addons\epay\library\Service;
use addons\myadmin\model\CompanyBase;
use think\Exception;
use think\Model;
use Yansongda\Pay\Pay;

class CompanyWithdraw extends Model
{


    // 表名
    protected $name = 'myadmin_company_withdraw';

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
        return max(0, sprintf("%.2f", $data['money'] - $data['handfee'] - $data['taxefee']));
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

    /**
     * 企业提现申请
     */
    public static function apply($company_id, $money, $type, $name, $account, $memo)
    {
        $where_company['company_id'] = $company_id;
        $company = Company::get($company_id);
        if ($money <= 0) {
            return '请输入提现金额';
        }
        if ($money > $company['money']) {
            return '可提现金额不足，最多可提：￥' . $company['money'];
        }
        $handrate = $company['handrate'];
        $taxerate = $company['taxerate'];
        $handfee = $handrate / 100 * $money;
        $taxefee = $taxerate / 100 * $money;
        $settledmoney = max(0, sprintf("%.2f", $money - $handfee - $taxefee));
        $data['company_id'] = $company['id'];
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
        (new CompanyWithdraw)->save($data);
        $rs = CompanyBase::money($money * -1, $company_id, '申请提现');
        if ($rs) {
            return true;
        }
        return false;
    }
}
