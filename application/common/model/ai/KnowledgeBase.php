<?php

namespace app\common\model\ai;

use think\Model;


class KnowledgeBase extends Model
{

    

    

    // 表名
    protected $name = 'ai_knowledge_base';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    







}
