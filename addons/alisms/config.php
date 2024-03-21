<?php

return [
    [
        'name' => 'key',
        'title' => '应用key',
        'type' => 'string',
        'content' => [],
        'value' => 'LTAI5tPHXD9azyAccB6GYXUM',
        'rule' => 'required',
        'msg' => '',
        'tip' => '',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'secret',
        'title' => '密钥secret',
        'type' => 'string',
        'content' => [],
        'value' => 'pj0BOZroYn5hSM7mkXnnETNomNzmCa',
        'rule' => 'required',
        'msg' => '',
        'tip' => '',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'sign',
        'title' => '签名',
        'type' => 'string',
        'content' => [],
        'value' => '乎考AI',
        'rule' => 'required',
        'msg' => '',
        'tip' => '',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'template',
        'title' => '短信模板',
        'type' => 'array',
        'content' => [],
        'value' => [
            'register' => 'SMS_286880048',
            'resetpwd' => 'SMS_286880048',
            'changepwd' => 'SMS_286880048',
            'changemobile' => 'SMS_286880048',
            'profile' => 'SMS_286880048',
            'notice' => 'SMS_286880048',
            'mobilelogin' => 'SMS_286880048',
            'bind' => 'SMS_286880048',
        ],
        'rule' => 'required',
        'msg' => '',
        'tip' => '',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => '__tips__',
        'title' => '温馨提示',
        'type' => 'string',
        'content' => [],
        'value' => '应用key和密钥你可以通过 https://ak-console.aliyun.com/?spm=a2c4g.11186623.2.13.fd315777PX3tjy#/accesskey 获取',
        'rule' => 'required',
        'msg' => '',
        'tip' => '',
        'ok' => '',
        'extend' => '',
    ],
];
