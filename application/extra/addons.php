<?php

return [
    'autoload' => false,
    'hooks' => [
        'app_init' => [
            'myadmin',
            'xccms',
        ],
        'user_sidenav_after' => [
            'myadmin',
        ],
        'install_myadmin_menu' => [
            'myadmin',
        ],
        'uninstall_myadmin_menu' => [
            'myadmin',
        ],
        'enable_myadmin_menu' => [
            'myadmin',
        ],
        'disable_myadmin_menu' => [
            'myadmin',
        ],
        'view_filter' => [
            'mylogin',
            'third',
        ],
        'user_delete_successed' => [
            'third',
        ],
        'user_logout_successed' => [
            'third',
        ],
        'module_init' => [
            'third',
        ],
        'action_begin' => [
            'third',
        ],
        'config_init' => [
            'third',
        ],
    ],
    'route' => [
        '/web$' => 'myadmin/web/index',
        '/web/content$' => 'myadmin/web/content',
        '/web/product$' => 'myadmin/web/product',
        '/web/single$' => 'myadmin/web/single',
        '/web/product/detail$' => 'myadmin/web/product_detail',
        '/web/content/detail$' => 'myadmin/web/content_detail',
        '/third$' => 'third/index/index',
        '/third/connect/[:platform]' => 'third/index/connect',
        '/third/callback/[:platform]' => 'third/index/callback',
        '/third/bind/[:platform]' => 'third/index/bind',
        '/third/unbind/[:platform]' => 'third/index/unbind',
        '/xccms/$' => 'xccms/index/index',
        '/xccms/search$' => 'xccms/index/search',
        '/xccms/product/[:id]$' => 'xccms/index/product',
        '/xccms/info_detail/[:id]$' => 'xccms/index/info_detail',
        '/xccms/product_detail/[:id]$' => 'xccms/index/product_detail',
        '/xccms/partner/$' => 'xccms/index/partner',
        '/xccms/job/$' => 'xccms/index/job',
        '/xccms/page/[:id]$' => 'xccms/index/page',
        '/xccms/news/$' => 'xccms/index/news',
        '/xccms/news/[:id]$' => 'xccms/index/news_detail',
        '/xccms/about-us/$' => 'xccms/index/about_us',
        '/xccms/contact-us/$' => 'xccms/index/contact_us',
        '/xccms/guestbook_add/$' => 'xccms/index/guestbook_add',
        '/xccms/job_detail/[:id]$' => 'xccms/index/job_detail',
        '/xccms/info/[:id]$' => 'xccms/index/info',
        '/xccms/guestbook/[:page_code]/[:page_id]$' => 'xccms/index/guestbook',
        '/xccms/closed/$' => 'xccms/index/closed',
        '/xccms/faq/$' => 'xccms/index/faq',
    ],
    'priority' => [],
    'domain' => '',
];
