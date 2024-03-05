<?php

namespace app\admin\model;

use think\Model;


class Xccmspageinfo extends Model
{

    

    

    // 表名
    protected $name = 'xccms_page_info';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    public static function get_name($id)
    {
        $m = self::field('id,title as name')->where('id', $id)->find();
        return $m ? $m['name'] : '';
    }


    public static function get_title($id)
    {
        $m = self::field('id,title')->where('id', $id)->find();
        return $m ? $m['title'] : '';
    }    




}
