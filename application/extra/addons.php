<?php

return [
    'autoload' => false,
    'hooks' => [
        'sms_send' => [
            'alisms',
        ],
        'sms_notice' => [
            'alisms',
        ],
        'sms_check' => [
            'alisms',
        ],
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
        'posters' => [
            'posters',
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
        '/$' => 'xccms/index/index',
        '/search$' => 'xccms/index/search',
        '/product/[:id]$' => 'xccms/index/product',
        '/info_detail/[:id]$' => 'xccms/index/info_detail',
        '/product_detail/[:id]$' => 'xccms/index/product_detail',
        '/partner/$' => 'xccms/index/partner',
        '/job/$' => 'xccms/index/job',
        '/page/[:id]$' => 'xccms/index/page',
        '/news/$' => 'xccms/index/news',
        '/news/[:id]$' => 'xccms/index/news_detail',
        '/about-us/$' => 'xccms/index/about_us',
        '/contact-us/$' => 'xccms/index/contact_us',
        '/guestbook_add/$' => 'xccms/index/guestbook_add',
        '/job_detail/[:id]$' => 'xccms/index/job_detail',
        '/info/[:id]$' => 'xccms/index/info',
        '/guestbook/[:page_code]/[:page_id]$' => 'xccms/index/guestbook',
        '/closed/$' => 'xccms/index/closed',
        '/faq/$' => 'xccms/index/faq',
    ],
    'priority' => [],
    'domain' => '',
];
