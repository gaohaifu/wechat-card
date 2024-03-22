<?php

namespace app\admin\model;

use think\Model;


class PostersRecord extends Model
{

    // 表名
    protected $name = 'posters_record';

    public function getParamsAttr($config)
    {
        return json_decode($config, true);
    }

    public function setParamsAttr($config)
    {
        return ! is_array($config) ? $config : json_encode($config, JSON_UNESCAPED_UNICODE);
    }

    public function posters(){
        return $this->hasOne(Posters::class, 'id', 'posters_id');
    }

}
