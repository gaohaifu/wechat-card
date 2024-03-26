<?php

namespace addons\posters;

use think\Hook;

/**
 *
 * 测试文件
 *
 * Class Test
 *
 * @package addons\posters
 * Author: zsw iszsw@qq.com
 */
class Test
{

    public static function posters()
    {

        /**
         *
         * 安装插件时已经生成一条测试海报
         * 调用测试
         * \addons\posters\Test::posters();
         *
         * 参数说明
         *
         * id
         * title    id 和 title 都可以绑定海报 任意一项即可
         * params   根据前端的变量提示绑定参数
         *              1.图片：支持本地图片和远程图片
         *              2.二维码、文本：如果传递数组将进行局部变量替换 如果传递的是字符串将完整替换
         * size     可以进行等比放大缩小 默认为1
         * output   1、true 直接输出 | 2、false 返回图片 | 3、__DIR__ . '/poster.png' 保存地址
         */
        $params = [
            'id' => 1,
            'params' => [
                'image_0' => '/assets/addons/posters/img/image.png',
                'qr_1' => [
                    'id' => 5
                ],
                'text_2' => [
                    'name' => '啦啦啦',
                    'title' => '写代码'
                ],
                'text_3' => '替换全部文本消息'
            ],
            'size' => 2.0,
            'output' => true,
        ];

        return Hook::listen('posters', $params, null, true);
    }
}

