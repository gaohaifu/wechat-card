<?php

namespace addons\fastscrm\controller\api;

use app\common\controller\Api;
use think\Db;
use think\Lang;

class Base extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();

        // 这里手动载入语言包
        Lang::load(ROOT_PATH . '/addons/fastscrm/lang/api/zh-cn.php');
    }

    public function checkauth($userid)
    {
        $user = Db::name('fastscrm_worker')
            ->where('userid', $userid)
            ->where('fauser_id', $this->auth->id)
            ->find();
        if (!empty($user)) {
            return true;
        } else {
            return false;
        }
    }
}