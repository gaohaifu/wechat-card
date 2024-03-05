<?php

namespace app\admin\model;

use think\Model;


class Xccmsjobinfo extends Model
{

    

    

    // 表名
    protected $name = 'xccms_job_info';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    
    public static function get_name($id)
    {
        $m = self::field('id,name')->where('id', $id)->find();
        return $m ? $m['name'] : '';
    }    






}
