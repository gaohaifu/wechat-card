<?php

namespace app\admin\model;

use think\Model;


class Xccmsguestbook extends Model
{

    

    

    // 表名
    protected $name = 'xccms_guestbook';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    







}
