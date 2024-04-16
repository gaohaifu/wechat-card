<?php

namespace app\common\service;

class Base
{

    protected static function success($msg,$data=''){
        return ['code'=>1,'msg'=>$msg,'data'=>$data];
    }

    protected static function error($msg,$data=''){
        return ['code'=>0,'msg'=>$msg,'data'=>$data];
    }
}
