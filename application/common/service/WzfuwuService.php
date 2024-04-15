<?php


namespace app\common\service;

use addons\myadmin\model\Company;
use addons\myadmin\model\Domain;
use app\admin\model\wwh\Cases;
use app\admin\model\wwh\News;
use app\admin\model\wwh\Product;
use app\admin\model\wwh\Tag;
use app\common\model\MyadminWebsiteAnalytics;
use app\common\model\SpiderLog;
use app\common\model\SpiderLogD;
use fast\Http;
use fast\Mysitemap;
use think\Env;
use think\Exception;
use think\Log;
use GuzzleHttp\Client;


/**
 * 网站服务相关类
 * @package app\common\service
 */
class WzfuwuService extends Base
{
    public function __construct()
    {
    }

    public static function analytics($companyName,$domain,$isHttps){
        $cacheName = md5($companyName.$domain.$isHttps);
        $result = cache($cacheName);
        if ($result){
            return $result;
        }


        $url = 'http://keyword.wzfuwu.cn/api/domain/analytics';
        $data = [
            'name' => $companyName,
            'domain' => $domain,
            'is_https' => $isHttps,
        ];

        $re = Http::get($url,$data);
        $re = json_decode($re,true);
        if (isset($re['code']) && $re['code'] == 1){
            cache($cacheName,$re['data'],3600);
            return $re['data'];
        }else{
            return '';
        }
    }


    
}