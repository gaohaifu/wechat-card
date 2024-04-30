<?php

namespace app\admin\model\fastscrm\kf;

use think\Model;
use traits\model\SoftDelete;

class Account extends Model
{

    

    // 表名
    protected $name = 'fastscrm_kf_account';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [

    ];


    public function getManagePrivilegeAttr($value)
    {

        return $value?'有':'无';
    }






}
