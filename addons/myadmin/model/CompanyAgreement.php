<?php

namespace addons\myadmin\model;

use think\Exception;
use think\Model;
use addons\myadmin\model\Company;
use fast\Random;

class CompanyAgreement extends Model
{
    // 表名
    protected $name = 'myadmin_company_agreement';

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
        // 写入前
        self::beforeInsert(function ($row) {
            if (!$row['player_id']) {
                throw new Exception("请选择角色");
            }
            if (!$row['company_id']) {
                throw new Exception("请选择企业");
            }
            $exist = self::where('player_id', $row['player_id'])->where('status', 'in', ['normal', 'hidden'])->where('company_id', $row['company_id'])->max('expiredtime');
            $expiredtime = (!is_numeric($row['expiredtime']) ? strtotime($row['expiredtime'] . ' 23:59:59') : $row['expiredtime']);
            if ($exist >= $expiredtime) {
                throw new Exception("请设置效期在" . date('Y-m-d', $exist) . "之后");
            }
            // 是否生效
            if ($row['status'] == 'normal') {
                $player_data = [];
                if (!$player = CompanyPlayer::where('player_id', $row['player_id'])->where('company_id', $row['company_id'])->find()) {
                    $player = (new CompanyPlayer);
                    $player_data['player_id'] = $row['player_id'];
                    $player_data['company_id'] = $row['company_id'];
                    $player_data['status'] = 'normal';
                }
                $player_data['expiredtime'] = $expiredtime;
                $player->save($player_data);
            }
            if (!isset($row['starttime']) || !$row['starttime']) {
                $row['starttime'] = strtotime(date('Y-m-d 00:00:00'));
            }
            $row['agreement_no'] = date('ym') . Random::numeric(4) . str_pad($row['company_id'], 6, "0", STR_PAD_LEFT) . str_pad($row['player_id'], 2, "0", STR_PAD_LEFT);
        });
        self::afterWrite(function ($row) {
            $pk = $row->getPk();
            $data = [];
            $player = AuthPlayer::get($row['player_id']);
            $company_name = Company::where('id', $row['company_id'])->value('name');
            if ($player) {
                $data['player'] = $player['label'];
                $data['name'] = $company_name . '《' . $player['name'] . '》';
                $starttime = (!is_numeric($row['starttime']) ? strtotime($row['starttime'] . ' 23:59:59') : $row['starttime']);
                $expiredtime = (!is_numeric($row['expiredtime']) ? strtotime($row['expiredtime'] . ' 23:59:59') : $row['expiredtime']);
                // 生成协议
                $content_value = [
                    'COMPANY' => $company_name,     // 企业名称
                    'PLAYER' => $player['name'],    // 企业玩家
                    'YEAR' => date('Y'),            // 年
                    'MONTH' => date('m'),           // 月
                    'DAY' => date('d'),             // 日
                    'NOWTIME' => date('Y年m月d日'),  // 年月日
                    'STARTTIME' => date('Y年m月d日', $starttime),
                    'EXPIREDTIME' => date('Y年m月d日', $expiredtime)
                ];
                $content = str_replace(array_keys($content_value), $content_value, $player['content']);
                $data['content'] = $content;
            }
            if ($data) {
                $row->getQuery()->where($pk, $row[$pk])->update($data);
            }
        });
        self::afterDelete(function ($row) {
        });
    }

    protected function getStarttimeAttr($value)
    {
        return $value === '' || $value === null ? date('Y-m-d') : ($value && !is_numeric($value) ? $value : date('Y-m-d', $value));
    }

    protected function setStarttimeAttr($value)
    {
        return $value === '' || $value === null ? strtotime(date('Y-m-d 00:00:00')) : ($value && !is_numeric($value) ? strtotime($value . ' 00:00:00') : $value);
    }

    protected function getExpiredtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? $value : date('Y-m-d', $value));
    }

    protected function setExpiredtimeAttr($value)
    {
        return $value === '' || $value === null ? strtotime(date('Y-m-d 23:59:59')) : ($value && !is_numeric($value) ? strtotime($value . ' 23:59:59') : $value);
    }

    // 状态式枚举
    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden'), 'expired' => __('Expired')];
    }
    // 关联企业
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id', [], 'LEFT')->setEagerlyType(1);
    }

    // 关联角色
    public function player()
    {
        return $this->belongsTo(AuthPlayer::class, 'player_id', 'id', [], 'LEFT')->setEagerlyType(1);
    }
}
