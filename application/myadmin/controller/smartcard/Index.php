<?php

namespace app\myadmin\controller\smartcard;

use addons\myadmin\library\Backend;


/**
 *
 */
class Index extends Backend
{

    protected $noNeedRight = ['index'];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 首页
     */
    public function index()
    {
        return "恭喜你创建插件成功";
    }



}
