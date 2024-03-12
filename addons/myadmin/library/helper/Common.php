<?php

namespace addons\myadmin\library\helper;

use think\Config;

/**
 * myadmin 通用助手
 */
class Common
{
    /**
     * 启用myadmin扩展插件
     * @param object|string $that
     * @return array
     */
    public static function EnableAddon($that)
    {
        $info = is_object($that) ? $that->getInfo() : get_addon_info($that);
        if ($info) {
            Config::set('addon_list.' . $info['name'], $info);
        }
        return $info;
    }

    /**
     * 下载文件到指定目录
     * @param string $url  文件路径或URL
     * @param string $save_dir 保存的位置
     * @param string $filename 保存的文件名
     * @param int $type 是否为远程
     * @return array
     */
    public static function getFile($url, $save_dir = '', $filename = '', $type = 0)
    {
        if (trim($url) == '') {
            return false;
        }
        if (trim($save_dir) == '') {
            $save_dir = './';
        }
        if (0 !== strrpos($save_dir, '/')) {
            $save_dir .= '/';
        }
        //创建保存目录
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return false;
        }
        //获取远程文件所采用的方法
        if ($type) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $content = curl_exec($ch);
            curl_close($ch);
        } else {
            ob_start();
            readfile($url);
            $content = ob_get_contents();
            ob_end_clean();
        }
        if(is_file($save_dir . $filename)){
            unlink($save_dir . $filename);
        }
        $size = strlen($content);
        //文件大小
        $fp2 = @fopen($save_dir . $filename, 'a');
        fwrite($fp2, '');
        fwrite($fp2, $content);
        fclose($fp2);
        unset($content, $url);
        return array(
            'file_name' => $filename,
            'save_path' => $save_dir . $filename
        );
    }
}
