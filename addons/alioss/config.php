<?php

return [
    [
        'name' => 'accessKeyId',
        'title' => 'AccessKey ID',
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
        'name' => 'accessKeySecret',
        'title' => 'AccessKey Secret',
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
        'name' => 'bucket',
        'title' => 'Bucket名称',
        'type' => 'string',
        'content' => [],
        'value' => 'bdr-wechat-card',
        'rule' => 'required;bucket',
        'msg' => '',
        'tip' => '阿里云OSS的空间名',
        'ok' => '',
        'extend' => 'data-rule-bucket="[/^[0-9a-z_\\-]{3,63}$/, \'请输入正确的Bucket名称\']"',
    ],
    [
        'name' => 'endpoint',
        'title' => 'Endpoint',
        'type' => 'string',
        'content' => [],
        'value' => 'oss-cn-guangzhou.aliyuncs.com',
        'rule' => 'required;endpoint',
        'msg' => '',
        'tip' => '请填写从阿里云存储获取的Endpoint',
        'ok' => '',
        'extend' => 'data-rule-endpoint="[/^(?!http(s)?:\\/\\/).*$/, \'不能以http(s)://开头\']"',
    ],
    [
        'name' => 'cdnurl',
        'title' => 'CDN地址',
        'type' => 'string',
        'content' => [],
        'value' => 'https://bdr-wechat-card.oss-cn-guangzhou.aliyuncs.com',
        'rule' => 'required;cdnurl',
        'msg' => '',
        'tip' => '请填写CDN地址，必须以http(s)://开头',
        'ok' => '',
        'extend' => 'data-rule-cdnurl="[/^http(s)?:\\/\\/.*$/, \'必需以http(s)://开头\']"',
    ],
    [
        'name' => 'uploadmode',
        'title' => '上传模式',
        'type' => 'select',
        'content' => [
            'client' => '客户端直传(速度快,无备份)',
            'server' => '服务器中转(占用服务器带宽,可备份)',
        ],
        'value' => 'client',
        'rule' => '',
        'msg' => '',
        'tip' => '',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'serverbackup',
        'title' => '服务器中转模式备份',
        'type' => 'radio',
        'content' => [
            1 => '备份(附件管理将产生2条记录)',
            0 => '不备份',
        ],
        'value' => '1',
        'rule' => '',
        'msg' => '',
        'tip' => '服务器中转模式下是否备份文件',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'savekey',
        'title' => '保存文件名',
        'type' => 'string',
        'content' => [],
        'value' => '/uploads/{year}{mon}{day}/{filemd5}{.suffix}',
        'rule' => 'required',
        'msg' => '',
        'tip' => '',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'expire',
        'title' => '上传有效时长',
        'type' => 'string',
        'content' => [],
        'value' => '600',
        'rule' => 'required',
        'msg' => '',
        'tip' => '用户停留页面上传有效时长，单位秒',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'maxsize',
        'title' => '最大可上传',
        'type' => 'string',
        'content' => [],
        'value' => '10M',
        'rule' => 'required',
        'msg' => '',
        'tip' => '',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'mimetype',
        'title' => '可上传后缀格式',
        'type' => 'string',
        'content' => [],
        'value' => 'jpg,png,bmp,jpeg,gif,zip,rar,xls,xlsx',
        'rule' => 'required',
        'msg' => '',
        'tip' => '',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'multiple',
        'title' => '多文件上传',
        'type' => 'bool',
        'content' => [],
        'value' => '1',
        'rule' => 'required',
        'msg' => '',
        'tip' => '',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'thumbstyle',
        'title' => '缩略图样式',
        'type' => 'string',
        'content' => [],
        'value' => '',
        'rule' => '',
        'msg' => '',
        'tip' => '用于后台列表缩略图样式，可使用：?x-oss-process=image/resize,m_lfit,w_120,h_90',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'chunking',
        'title' => '分片上传',
        'type' => 'radio',
        'content' => [
            1 => '开启',
            0 => '关闭',
        ],
        'value' => '1',
        'rule' => 'required',
        'msg' => '',
        'tip' => '',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'chunksize',
        'title' => '分片大小',
        'type' => 'number',
        'content' => [],
        'value' => '4194304',
        'rule' => 'required',
        'msg' => '',
        'tip' => '',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'syncdelete',
        'title' => '附件删除时是否同步删除云存储文件',
        'type' => 'bool',
        'content' => [],
        'value' => '1',
        'rule' => 'required',
        'msg' => '',
        'tip' => '',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'apiupload',
        'title' => 'API接口使用云存储',
        'type' => 'bool',
        'content' => [],
        'value' => '1',
        'rule' => 'required',
        'msg' => '',
        'tip' => '',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'noneedlogin',
        'title' => '免登录上传',
        'type' => 'checkbox',
        'content' => [
            'api' => 'API',
            'index' => '前台',
            'admin' => '后台',
        ],
        'value' => 'api,index,admin',
        'rule' => '',
        'msg' => '',
        'tip' => '',
        'ok' => '',
        'extend' => '',
    ],
];
