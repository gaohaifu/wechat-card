<?php

namespace addons\myadmin\library;

use think\Config;
use think\Request;
use think\Lang;

/**
 * API控制器基类
 */
class FrontApi extends \app\common\controller\Api
{
    /**
     * 引入控制器的traits
     */
    use \addons\myadmin\library\traits\Frontend;

    /**
     * 构造方法
     * @access public
     * @param Request $request Request 对象
     */
    public function __construct(Request $request = null)
    {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header('Access-Control-Expose-Headers: __token__'); //跨域让客户端获取到
        } 
        parent::__construct($this->request);
    }

    /**
     * 初始化操作
     * @access protected
     */
    public function __initialize()
    {       
        if (!isset($_COOKIE['PHPSESSID'])) {
            Config::set('session.id', $this->request->server("HTTP_SID"));
        }
        // 加载语言包
        Lang::load([
            ADDON_PATH . $this->addon . DS . 'lang' . DS . $this->request->langset() . EXT,
        ]);
        // 加载控制器语言包
        $this->addonLoadlang($this->controller);
    }

    // 自动显示错误
    public function autoError($msg = '出错啦')
    {
        $this->error($msg, [], 401);
    }
}
