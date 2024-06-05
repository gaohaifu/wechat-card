<?php

namespace addons\editpage;

use think\Addons;

/**
 * 插件
 */
class Editpage extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        return true;
    }

    /**
     * @param $params
     */
    public function configInit(&$params)
    {
        $config['app_debug'] = config('app_debug');
        $config['controller'] = request()->controller();
        $config['action'] = request()->action();
        $config['module'] = request()->module();
        $config['index'] = url('editpage/index');
        $config['command'] = url('editpage/command');
        $params['editpage'] = $config;
    }
}