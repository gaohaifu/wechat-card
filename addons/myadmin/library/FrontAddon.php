<?php

namespace addons\myadmin\library;

use think\Config;
use think\Hook;

/**
 * 插件前台控制器基类
 */
class FrontAddon extends \think\addons\Controller
{

    use \addons\myadmin\library\traits\Frontend;
    /**
     * 初始化操作
     * @access protected
     */
    public function __initialize()
    {
        //parent::_initialize();

        // 语言检测
        $lang = strip_tags($this->request->langset());

        $site = $this->site;
        $upload = \app\common\model\Config::upload();

        // 上传信息配置后
        Hook::listen("upload_config_init", $upload);

        // 配置信息
        $config = [
            'site'           => array_intersect_key($site, array_flip(['name', 'cdnurl', 'version', 'timezone', 'languages'])),
            'upload'         => $upload,
            'modulename'     => 'addons',
            'controllername' => $this->controller,
            'actionname'     =>  $this->action,
            'jsname'         => 'addons/' . $this->addon . '/' . str_replace('.', '/', $this->controller),
            'moduleurl'      => '/addons/' . $this->addon,
            'language'       => $lang
        ];
        $config = array_merge($config, Config::get("view_replace_str"));

        // 配置信息后
        Hook::listen("config_init", $config);
        // 加载当前控制器语言包
        $this->view->assign('site', $site);
        $this->view->assign('config', $config);
        $this->view->assign('company', $this->company);
        $this->view->assign('config_json', json_encode(['config' => $config], JSON_UNESCAPED_UNICODE));
    }
}
