<?php

//配置文件
return [
    'url_common_param'       => true,
    'url_html_suffix'        => '',
    'controller_auto_search' => true,
    // 认证设置
    'auth' => [
        'auth_on'           => 1, // 权限开关
        'auth_type'         => 1, // 认证方式，1为实时认证；2为登录认证。
        'auth_group'        => 'myadmin_auth_group', // 用户组数据表名
        'auth_group_access' => 'myadmin_auth_group_access', // 用户-用户组关系表
        'auth_rule'         => 'myadmin_auth_rule', // 权限规则表
        'auth_user'         => 'myadmin_admin', // 用户信息表
        'token_name'         => 'token', // 验证TOKEN标识
    ]
];
