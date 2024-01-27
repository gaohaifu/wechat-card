<?php

namespace addons\smartcard\controller;



/**
 * 自建表前台页面，展示数据字典
 */
class Index extends \think\addons\Controller {



    public function _initialize() {
        parent::_initialize();
    }

    /**
     *展示数据字典信息
     */
    public function index() {
            $this->error("当前插件暂无前台页面",url());

    }

}
