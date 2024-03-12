<?php

namespace app\admin\model;

use think\Model;


class Xccmsnewsinfo extends Model
{

    

    

    // 表名
    protected $name = 'xccms_news_info';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    public static function get_title($id)
    {
        $m = self::field('id,title')->where('id', $id)->find();
        return $m ? $m['title'] : '';
    }    







}
