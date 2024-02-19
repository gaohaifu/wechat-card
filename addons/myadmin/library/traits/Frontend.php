<?php

namespace addons\myadmin\library\traits;

use think\Lang;
use think\Loader;
use think\Config;
use think\Hook;

use addons\myadmin\model\Company;
use addons\myadmin\model\Addon;
use addons\myadmin\model\Domain;
use addons\myadmin\model\Config as CompanyConfig;

/**
 * 机构前段通过类
 */
trait Frontend
{
    protected $addon = null; // 插件名称，默认自动获取，当插件不存在时，使用myadmin插件
    protected $site = null;  // 当前站点配置
    protected $company_id = null; // 当前企业ID
    protected $company = null; // 当前企业信息
    protected $addon_config = []; // 当前企业插件配置
    protected $companyHiddenFields = ['deletetime', 'createtime', 'updatetime', 'identifier', 'admin_limit', 'money', 'score']; //企业信息安全隐藏敏感数据

    public function _initialize()
    {
        $this->controller = Loader::parseName($this->request->controller());
        parent::_initialize();
        $this->_initCompany(); // 初始化企业        
        $this->_initAddon(); // 初始化插件
        // 初始化
        $this->__initialize();
    }

    //初始化
    public function __initialize()
    {
    }

    // 初始化企业信息
    public function _initCompany()
    {
        $host = $this->request->header('host');
        if (!$company_id = Domain::where('name', $host)->where('status', 'normal')->value('company_id')) {
            $company_id = Company::where('identifier', $this->request->param('companykey', 0))->value('id');
        }
        if (!defined('COMPANY_ID')) {
            define('COMPANY_ID', $company_id);
        }
        if (!$this->company = Company::get(COMPANY_ID)) {
            $this->error('出错啦，找不到企业信息');
        }
        //安全隐藏敏感数据
        $this->company->hidden($this->companyHiddenFields);
        // 获取配置信息
        $site = Config::get("site");
        $company_config = CompanyConfig::Config(COMPANY_ID);
        if ($company_config) {
            $site = array_merge($site, $company_config);
        }
        $upload = \addons\myadmin\model\Config::upload();
        // 上传信息配置后
        Hook::listen("upload_config_init", $upload);
        Config::set('upload', array_merge(Config::get('upload'), $upload));
        $this->site = $site;
    }

    // 初始化企业当前插件信息
    protected function _initAddon()
    {
        // 检查插件权限
        if ($addon = $this->getAddonAccess(COMPANY_ID)) {;
            if ($addon['status'] == 'expired') {
                $this->error('出错啦，应用已过期');
            }
        }
        $this->addon_config = Addon::config($this->addon, COMPANY_ID); // 获取插件配置
    }

    /**
     * 获取当前企业ID
     */
    public function getAddonAccess($company_id)
    {
        // 获取当前应用
        if (!$this->addon) {
            $controller = explode('.', $this->controller);
            $this->addon = $controller[0];
        }
        // 检查插件是否存在
        if (!get_addon_info($this->addon)) {
            $this->addon = 'myadmin'; // 设置默认插件名称  
        }
        // 后续将采用缓存
        if ($addon = Addon::where('name', $this->addon)->where('company_id', $company_id)->find()) {
            $addon->append(['status']);
            return $addon;
        }
    }

    /**
     * 加载插件语言文件
     * @param string $name
     */
    protected function addonLoadlang($name)
    {
        $name = Loader::parseName($name);
        $path = ADDON_PATH . $this->addon  . '/lang/' . $this->request->langset() . '/' . str_replace('.', '/', $name) . '.php';
        Lang::load($path);
    }
}
