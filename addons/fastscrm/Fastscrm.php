<?php

namespace addons\fastscrm;

use app\common\library\Menu;
use think\Addons;
use WeWork\App;

/**
 * 插件
 */
class Fastscrm extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = require_once ADDON_PATH . 'fastscrm' . DS . 'data' . DS . 'menu.php';
        Menu::create($menu);
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete('fastscrm');
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable('fastscrm');
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable('fastscrm');
        return true;
    }


    /**
     * 插件升级方法
     */
    public function upgrade()
    {
        $menu = require_once ADDON_PATH . 'fastscrm' . DS . 'data' . DS . 'menu.php';
        Menu::upgrade('fastscrm', $menu);
    }

    /**
     * 应用初始化
     */
    public function appInit()
    {
    }
}