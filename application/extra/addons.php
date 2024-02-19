<?php

return [
    'autoload' => false,
    'hooks' => [
        'app_init' => [
            'myadmin',
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
        'view_filter' => [
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
    ],
    'priority' => [],
    'domain' => '',
];
