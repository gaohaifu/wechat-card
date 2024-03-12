<?php

// 公共助手函数
use think\Config;
use think\Url;


if (!function_exists('company_url')) {
    function company_url($appid = '')
    {
        if (COMPANY_URL) {
            return addon_urls(COMPANY_URL, ['companyappid' => $appid]);
        }
        return urls('index/index/index', ['companyappid' => $appid]);
    }
}

//插件URL自动识别或添加参数
if (!function_exists('addon_urls')) {
    function addon_urls($url, $vars = [], $suffix = true, $domain = false)
    {
        $vars = get_auto_vars($vars);
        return addon_url($url, $vars, $suffix, $domain);
    }
}

//URL自动识别或添加参数
if (!function_exists('urls')) {
    function urls($url = '', $vars = [], $suffix = true, $domain = false)
    {
        $vars = get_auto_vars($vars);
        return Url::build($url, $vars, $suffix, $domain);
    }
}
// 参数自动识别或添加
if (!function_exists('get_auto_vars')) {
    function get_auto_vars($vars = [])
    {
        if (is_string($vars)) {
            parse_str($vars, $vars);
        }
        if (PRIVATE_DOMAIN) {
            if (isset($vars['companyappid'])) {
                if(Config::get('company.identifier') == $vars['companyappid']){
                    unset($vars['companyappid']);
                }
            }else{                
                unset($vars['companyappid']);
            }
        } else {
            if (!isset($vars['companyappid'])) {
                $vars = array_merge($vars, ['companyappid' => Config::get('company.identifier', '')]);
            }
        }
        return $vars;
    }
}
