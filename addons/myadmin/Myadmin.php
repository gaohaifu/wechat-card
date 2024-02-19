<?php

namespace addons\myadmin;

use app\common\library\Menu;
use think\Addons;
use think\Hook;
use addons\myadmin\library\Menu as MyadminMenu;
use think\Request;
use think\Config;

use addons\myadmin\model\Company;
use addons\myadmin\model\Domain;
use think\Route;


use app\common\model\Attachment;
use addons\myadmin\model\Attachment as MyadminAttachment;


/**
 * 插件
 */
class Myadmin extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [];
        $config_file = ADDON_PATH . "myadmin" . DS . 'config' . DS . "menu.php";
        if (is_file($config_file)) {
            $menu = include $config_file;
        }
        if ($menu) {
            Menu::create($menu);
        }
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        $info = get_addon_info('myadmin');
        Menu::delete(isset($info['first_menu']) ? $info['first_menu'] : 'myadmin');
        return true;
    }

    /**
     * 插件启用方法
     */
    public function enable()
    {
        $info = get_addon_info('myadmin');
        Menu::enable(isset($info['first_menu']) ? $info['first_menu'] : 'myadmin');
        // 检测默认配置是否存在
        $config_list = \app\common\model\Config::where('name', 'in', ['name', 'beian', 'fixedpage'])->order('id acs')->select();
        if ($config_list) {
            $config_has = \addons\myadmin\model\Config::where('name', 'in', ['name', 'beian', 'fixedpage'])->column('id,name', 'name');
            $config_data = [];
            foreach ($config_list as $c) {
                // 移除已经存在配置
                if (!isset($config_has[$c['name']])) {
                    unset($c['id']);
                    $config_data[] = $c->toArray();
                }
            }
            // 自动同步配置数据
            if ($config_data) {
                (new \addons\myadmin\model\Config)->allowField(true)->saveAll($config_data);
            }
        }
        // 插件启用时检测自动操作初始菜单
        $all = [];
        $config_file = ADDON_PATH . 'myadmin' . DS . 'config' . DS . "myadmin_menu.php";
        if (is_file($config_file)) {
            $all = include $config_file;
            foreach ($all as $menu) {
                MyadminMenu::myadminrCeate($menu);
            }
        }
        return true;
    }

    /**
     * 插件禁用方法
     */
    public function disable()
    {
        $info = get_addon_info('myadmin');
        Menu::disable(isset($info['first_menu']) ? $info['first_menu'] : 'myadmin');
    }

    /**
     * 初始化
     */
    public function appInit()
    {
        include ADDON_PATH . 'myadmin' . DS . 'common.php'; // 公共助手函数
        $request = Request::instance();
        $host = $request->header('host');
        $config = $this->getConfig();
        $private_domain = false;
        $company_url = isset($config['fixedpage']) && $config['fixedpage'] ? $config['fixedpage'] : '';
        $company = [];
        if ($company_id = Domain::where('name', $host)->where('status', 'normal')->value('company_id')) {
            $private_domain = true;
            if ($company_url) {
                Route::rule('/', $config['fixedpage']); // 路由首页跳转
            }
            $company = Company::get($company_id);
        } else {
            if ($companyappid = $request->header('company-appid', $request->param('companyappid', 0))) {
                if ($company = Company::where('identifier', $companyappid)->find()) {
                    $domain = Domain::where('company_id', $company['id'])->where('status', 'normal')->value('name');
                    // 域名跳转
                    if ($domain) {
                        // 标识私有域名          
                        if (!defined('PRIVATE_DOMAIN')) {
                            define('PRIVATE_DOMAIN', $private_domain);
                        }
                        $url = addon_urls('index', $request->param(), 'html', $domain);
                        redirect($url)->send();
                    }
                }
            }
        }
        if ($company) {
            $company->hidden(['deletetime', 'createtime', 'updatetime', 'admin_limit', 'money', 'score']);
            Config::set('company', $company->toArray());
            $company_id = $company['id'];
        }
        // 标识私有域名          
        if (!defined('PRIVATE_DOMAIN')) {
            define('PRIVATE_DOMAIN', $private_domain);
        }
        // 赋值全局
        if (!defined('COMPANY_ID') && $company_id) {
            define('COMPANY_ID', $company_id);
        }
        if (!defined('COMPANY_URL')) {
            define('COMPANY_URL', $company_url);
        }

        //上传附件
        Attachment::event('after_insert', function ($row) {
            $storage = config('upload.storage');
            $request = Request::instance();
            $company_id = $request->param('company_id', 0);
            if ($company_id) {
                if ($company_id == 'common') {
                    $company_id = 0;
                }
                $myadmin_id = $request->param('myadmin_id', 0);
                $row = $row->toArray();
                $data = array_merge($row, [
                    'category'    => $row['category'],
                    'company_id'  => (int) $company_id,
                    'admin_id'    => $myadmin_id
                ]);
                unset($data['id']);
                if ($data['storage'] == $storage) {
                    MyadminAttachment::create($data, true);
                }
                try {
                } catch (\think\Exception $e) {
                }
            }
        });
    }

    /**
     * 企业中心边栏后
     * @return mixed
     * @throws \Exception
     */
    public function userSidenavAfter()
    {
        $request = Request::instance();
        $controllername = strtolower($request->controller());
        $actionname = strtolower($request->action());
        $data = [
            'controllername' => $controllername,
            'actionname'     => $actionname
        ];
        return $this->fetch('view/hook/user_sidenav_after', $data);
    }


    /**
     * 安装添加myadmin菜单
     */
    public function installMyadminMenu(&$name)
    {
        $menu = [];
        $config_file = ADDON_PATH . $name . DS . 'config' . DS . "myadmin_menu.php";
        if (is_file($config_file)) {
            $menu = include $config_file;
        }
        if ($menu) {
            MyadminMenu::create($menu);
        }
    }

    /**
     * 卸载删除myadmin菜单
     */
    public function uninstallMyadminMenu(&$name)
    {
        MyadminMenu::delete($name);
    }

    /**
     * 启用更新myadmin菜单
     */
    public function enableMyadminMenu(&$name)
    {
        MyadminMenu::enable($name);
    }

    /**
     * 禁用更新myadmin菜单
     */
    public function disableMyadminMenu(&$name)
    {
        MyadminMenu::disable($name);
    }
}
