<?php

namespace addons\myadmin\library;

use think\Lang;
/**
 * 前台控制器基类
 */
class Frontend extends \app\common\controller\Frontend
{
    
    /**
     * 引入控制器的traits
     */
    use \addons\myadmin\library\traits\Frontend;

    // 自动显示错误
    public function autoError($msg = '出错啦')
    {
        $this->error($msg, null, '', 100000);
    }

    /**
     * 初始化操作
     * @access protected
     */
    public function __initialize()
    {
        // 加载语言包
        Lang::load([
            ADDON_PATH . $this->addon . DS . 'lang' . DS . $this->request->langset() . EXT,
        ]);
        // 加载控制器语言包
        $this->addonLoadlang($this->controller);
        $this->assign('site', $this->site);
    }
}
