<?php

namespace addons\myadmin\model;

use think\Exception;
use think\Model;
use addons\myadmin\model\Company;

class CompanyPlayer extends Model
{
    // 表名
    protected $name = 'myadmin_company_player';

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
            $exist = self::where('player_id', $row['player_id'])->where('company_id', $row['company_id'])->count();
            if ($exist) {
                throw new Exception("角色已经设置，请务重复设置");
            }
        });
        self::afterWrite(function ($row) {
            $pk = $row->getPk();
            $data = [];
            $player = AuthPlayer::get($row['player_id']);
            if ($player) {
                $data['player'] = $player['label'];
                $data['name'] = $player['name'];                
            }
            if ($data) {
                $row->getQuery()->where($pk, $row[$pk])->update($data);
            }
            self::synCompany($row['company_id']);
        });
        self::afterDelete(function ($row) {
            self::synCompany($row['company_id']);
        });
    }
    public static function synCompany($company_id)
    {
        $player = '';
        if ($player_id = self::where('company_id', $company_id)->where('expiredtime', '>', time())->column('player_id')) {
            $playerList = AuthPlayer::where('id', 'in', $player_id)->column('label', 'label');
            $player = implode(',', $playerList);
        }
        $company = Company::get($company_id);
        $company->save(['player' => $player]);
    }

    protected function getExpiredtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? $value : date('Y-m-d', $value));
    }

    protected function setExpiredtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value . ' 23:59:59') : $value);
    }

    // 状态式枚举
    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
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
