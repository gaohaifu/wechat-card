<?php

namespace addons\xccms;

use app\common\library\Menu;
use think\Addons;

/**
 * 插件
 */
class Xccms extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name'    => 'xccms',
                'title'   => 'XC企业建站',
                'icon'    => 'fa fa-sitemap',
                'weigh'   => 100,
                'sublist' => [
                    [
                        'name'    => 'xccms/xccmssiteconfig',
                        'title'   => '站点配置',
                        'icon'    => 'fa fa-cog',
                        'weigh'   => 99,
                        'sublist' => [
                            ["name"  => "xccms/xccmssiteconfig/index","title" => "查看"],
                            ["name"  => "xccms/xccmssiteconfig/edit_theme","title" => "设置站点主题"],
                            ["name"  => "xccms/xccmssiteconfig/set_theme1_ext","title" => "扩展配置Theme1"],
                            ["name"  => "xccms/xccmssiteconfig/set_theme2_ext","title" => "扩展配置Theme2"],
                            ["name"  => "xccms/xccmssiteconfig/set_theme3_ext","title" => "扩展配置Theme3"],
                        ]
                    ],
                    [
                        'name'    => 'xccms/xccmsmenuinfo',
                        'title'   => '菜单管理',
                        'icon'    => 'fa fa-navicon',
                        'weigh'   => 98,
                        'sublist' => [
                            ["name"  => "xccms/xccmsmenuinfo/index","title" => "查看"],
                            ["name"  => "xccms/xccmsmenuinfo/add","title" => "添加"],
                            ["name"  => "xccms/xccmsmenuinfo/edit","title" => "编辑"],
                            ["name"  => "xccms/xccmsmenuinfo/del","title" => "删除"],
                            ["name"  => "xccms/xccmsmenuinfo/selectpage","title" => "Selectpage搜索"]
                        ]
                    ],
                    [
                        'name'    => 'xccms/xccmspageinfo',
                        'title'   => '单页管理',
                        'icon'    => 'fa fa-bookmark',
                        'weigh'   => 97,
                        'sublist' => [
                            ["name"  => "xccms/xccmspageinfo/index","title" => "查看"],
                            ["name"  => "xccms/xccmspageinfo/add","title" => "添加"],
                            ["name"  => "xccms/xccmspageinfo/edit","title" => "编辑"],
                            ["name"  => "xccms/xccmspageinfo/del","title" => "删除"],
                            ["name"  => "xccms/xccmspageinfo/selectpage","title" => "Selectpage搜索"]
                            
                        ]
                    ],
                    [
                        'name'    => 'xccms/xccmsnewsinfo',
                        'title'   => '新闻管理',
                        'icon'    => 'fa fa-newspaper-o',
                        'weigh'   => 96,
                        'sublist' => [
                            ["name"  => "xccms/xccmsnewsinfo/index","title" => "查看"],
                            ["name"  => "xccms/xccmsnewsinfo/add","title" => "添加"],
                            ["name"  => "xccms/xccmsnewsinfo/edit","title" => "编辑"],
                            ["name"  => "xccms/xccmsnewsinfo/del","title" => "删除"]
                            
                        ]
                    ],
                    [
                        'name'    => 'xccms/xccmscontentcategory',
                        'title'   => '内容分类管理',
                        'icon'    => 'fa fa-folder-open',
                        'weigh'   => 95,
                        'sublist' => [
                            ["name"  => "xccms/xccmscontentcategory/index","title" => "查看"],
                            ["name"  => "xccms/xccmscontentcategory/add","title" => "添加"],
                            ["name"  => "xccms/xccmscontentcategory/edit","title" => "编辑"],
                            ["name"  => "xccms/xccmscontentcategory/del","title" => "删除"],
                            ["name"  => "xccms/xccmscontentcategory/selectpage","title" => "Selectpage搜索"]
                            
                        ]
                    ],
                    [
                        'name'    => 'xccms/xccmscontentinfo',
                        'title'   => '内容管理',
                        'icon'    => 'fa fa-file',
                        'weigh'   => 94,
                        'sublist' => [
                            ["name"  => "xccms/xccmscontentinfo/index","title" => "查看"],
                            ["name"  => "xccms/xccmscontentinfo/add","title" => "添加"],
                            ["name"  => "xccms/xccmscontentinfo/edit","title" => "编辑"],
                            ["name"  => "xccms/xccmscontentinfo/del","title" => "删除"],
                            ["name"  => "xccms/xccmscontentinfo/getList","title" => "获取列表"],
                            
                        ]
                    ],
                    [
                        'name'    => 'xccms/xccmsproductcategory',
                        'title'   => '产品分类管理',
                        'icon'    => 'fa fa-th',
                        'weigh'   => 93,
                        'sublist' => [
                            ["name"  => "xccms/xccmsproductcategory/index","title" => "查看"],
                            ["name"  => "xccms/xccmsproductcategory/add","title" => "添加"],
                            ["name"  => "xccms/xccmsproductcategory/edit","title" => "编辑"],
                            ["name"  => "xccms/xccmsproductcategory/del","title" => "删除"],
                            ["name"  => "xccms/xccmsproductcategory/selectpage","title" => "Selectpage搜索"]
                            
                        ]
                    ],
                    [
                        'name'    => 'xccms/xccmsproductinfo',
                        'title'   => '产品管理',
                        'icon'    => 'fa fa-cubes',
                        'weigh'   => 92,
                        'sublist' => [
                            ["name"  => "xccms/xccmsproductinfo/index","title" => "查看"],
                            ["name"  => "xccms/xccmsproductinfo/add","title" => "添加"],
                            ["name"  => "xccms/xccmsproductinfo/edit","title" => "编辑"],
                            ["name"  => "xccms/xccmsproductinfo/del","title" => "删除"],
                            ["name"  => "xccms/xccmsproductinfo/getList","title" => "获取列表"],
                            
                        ]
                    ],
                    [
                        'name'    => 'xccms/xccmsfaq',
                        'title'   => 'FAQ管理',
                        'icon'    => 'fa fa-quora',
                        'weigh'   => 91,
                        'sublist' => [
                            ["name"  => "xccms/xccmsfaq/index","title" => "查看"],
                            ["name"  => "xccms/xccmsfaq/add","title" => "添加"],
                            ["name"  => "xccms/xccmsfaq/edit","title" => "编辑"],
                            ["name"  => "xccms/xccmsfaq/del","title" => "删除"]
                            
                        ]
                    ],                    
                    [
                        'name'    => 'xccms/xccmsfriendlink',
                        'title'   => '友情链接管理',
                        'icon'    => 'fa fa-link',
                        'weigh'   => 90,
                        'sublist' => [
                            ["name"  => "xccms/xccmsfriendlink/index","title" => "查看"],
                            ["name"  => "xccms/xccmsfriendlink/add","title" => "添加"],
                            ["name"  => "xccms/xccmsfriendlink/edit","title" => "编辑"],
                            ["name"  => "xccms/xccmsfriendlink/del","title" => "删除"]
                            
                        ]
                    ],
                    [
                        'name'    => 'xccms/xccmswebsitecarousel',
                        'title'   => '轮播管理',
                        'icon'    => 'fa fa-film',
                        'weigh'   => 89,
                        'sublist' => [
                            ["name"  => "xccms/xccmswebsitecarousel/index","title" => "查看"],
                            ["name"  => "xccms/xccmswebsitecarousel/add","title" => "添加"],
                            ["name"  => "xccms/xccmswebsitecarousel/edit","title" => "编辑"],
                            ["name"  => "xccms/xccmswebsitecarousel/del","title" => "删除"]
                            
                        ]
                    ],
                    [
                        'name'    => 'xccms/xccmsguestbook',
                        'title'   => '留言板管理',
                        'icon'    => 'fa fa-book',
                        'weigh'   => 88,
                        'sublist' => [
                            ["name"  => "xccms/xccmsguestbook/index","title" => "查看"],
                            ["name"  => "xccms/xccmsguestbook/add","title" => "添加"],
                            ["name"  => "xccms/xccmsguestbook/edit","title" => "编辑"],
                            ["name"  => "xccms/xccmsguestbook/del","title" => "删除"]
                            
                        ]
                    ],
                    [
                        'name'    => 'xccms/xccmspartnerlink',
                        'title'   => '合作伙伴管理',
                        'icon'    => 'fa fa-handshake-o',
                        'weigh'   => 87,
                        'sublist' => [
                            ["name"  => "xccms/xccmspartnerlink/index","title" => "查看"],
                            ["name"  => "xccms/xccmspartnerlink/add","title" => "添加"],
                            ["name"  => "xccms/xccmspartnerlink/edit","title" => "编辑"],
                            ["name"  => "xccms/xccmspartnerlink/del","title" => "删除"]
                            
                        ]
                    ],
                    [
                        'name'    => 'xccms/xccmsjobinfo',
                        'title'   => '招聘职位管理',
                        'icon'    => 'fa fa-child',
                        'weigh'   => 86,
                        'sublist' => [
                            ["name"  => "xccms/xccmsjobinfo/index","title" => "查看"],
                            ["name"  => "xccms/xccmsjobinfo/add","title" => "添加"],
                            ["name"  => "xccms/xccmsjobinfo/edit","title" => "编辑"],
                            ["name"  => "xccms/xccmsjobinfo/del","title" => "删除"]
                            
                        ]
                    ],




                ]
            ],

        ];

        Menu::create($menu);
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete("xccms");
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable("xccms");
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable("xccms");
        return true;
    }

    public function appInit()
    {

    }


    /**
     * 脚本替换---注释留做参考
     */
    public function viewFilter(& $content)
    {
        // 获取当前的协议和域名
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
        $domain = $_SERVER['HTTP_HOST'];

        // 使用正则表达式替换所有不以 http:// 或 https:// 开头的 src 属性
        $content = preg_replace_callback('/<img\s+[^>]*src\s*=\s*["\']([^"\']+)["\'][^>]*>/i', function ($matches) use ($protocol, $domain) {
            $src = $matches[1];
            // 检查是否以 http:// 或 https:// 开头
            if (!preg_match("~^(?:f|ht)tps?://~i", $src)) {
                // 如果不是以 http:// 或 https:// 开头，则替换为当前域名的完整 URL
                //$src = $protocol . $domain . $src;
                $src = cdnurl($src);
            }
            // 返回替换后的 img 标签
            return '<img src="' . $src . '">';
        }, $content);

    }
}
