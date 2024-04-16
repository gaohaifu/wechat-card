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
 * 网站相关
 * @package app\common\service
 */
class SiteService extends Base
{
    public function __construct()
    {
    }

    public static function getCompanyByServer($server){
        // 设定主题模板目录
        if (!isset($_SERVER['HTTP_HOST'])){
            abort(0,'域名异常');
        }
        $domain = get_first_host($_SERVER['HTTP_HOST']);
        $domains = cache($domain);
        if(!$domains){
            $domains = Domain::where(['name'=>$domain])->cache($domain,60)->find();
        }

        if($domains){
            /*//检测审核中状态
            if (in_array($domains['status'], ['created'])) {
                $this->auth->logout();
                $this->error('[' . $domains['name'] . ']正在审核中，请联系管理员！');
            }*/
            //检测禁用状态
            if (in_array($domains['status'], ['expired'])) {
                abort(0,'[' . $domains['name'] . ']已被禁用，请联系管理员！');
            }

            $company_id = $domains['company_id'];
            $companyInfo = cache('report_company_id:'.$company_id);
            if(!$companyInfo){
                $companyInfo = Company::field('id,theme,logo,name')->cache('report_company_id:'.$company_id,60)->find($domains['company_id']);
            }

            $companyInfo['domain'] = $domain;
            $companyInfo['employ_num'] = $domains['employ_num'];
            return $companyInfo;
        }else{
            abort(0,'域名不存在');
        }
    }

    public static function createSiteMap($domainRow){
        if (!$domainRow){
            return self::error('站点记录不能为空');
        }
        //如果文件存在先做删除再新建
        $fielname = 'uploads/sitemap/'.$domainRow->id.'.xml';
        if (file_exists($fielname)){
            if (unlink($fielname)){

            }else{
                abort(0,'删除失败');
            }
        }
        $domain = trim($domainRow->name);
        $preFix = trim($domainRow->domain_prefix);
        if($domainRow['install'] == 'yes'){
            $sitemap = new Mysitemap("https://".$preFix.'.'.$domain);
        }else{
            $sitemap = new Mysitemap("http://".$preFix.'.'.$domain);
        }
        $sitemap->setXmlFile('uploads/sitemap/'.$domainRow->id);

        if($domainRow && $domainRow['status']=='normal'){
            $company_id = $domainRow['company_id'];
            $list = [];
            array_push($list,'/');
            array_push($list,'/service/');
            array_push($list,'/market/');
            array_push($list,'/download/');
            array_push($list,'/partner/');

            //产品相关url
            array_push($list,'/product/');
            $info = Product::where('company_id','=',$company_id)->where('examine','=',2)->select();
            foreach ($info as $item) {
                $url = '/product_detail/'.$item->id.'.html';
                array_push($list,$url);
            }

            //解决方案相关url
            array_push($list,'/cases/');
            $info = Cases::where('company_id','=',$company_id)->select();
            foreach ($info as $item) {
                $url = '/cases_detail/'.$item->id.'.html';
                array_push($list,$url);
            }

            //新闻资讯相关url
            array_push($list,'/news/');
            $info = News::where('company_id','=',$company_id)->where('examine','=',2)->select();
            foreach ($info as $item) {
                $url = '/news_detail/'.$item->id.'.html';
                array_push($list,$url);
            }

            //标签模块相关url
            array_push($list,'/news/');
            $info = Tag::where('company_id','=',$company_id)->select();
            foreach ($info as $item) {
                if ($item->tag_type == 1){
                    $url = '/news/?tag_id='.$item->id;
                }else{
                    $url = '/product/?tag_id='.$item->id;
                }
                array_push($list,$url);
            }

            foreach ($list as $item) {
                $sitemap->addItem($item, '1.0', 'daily');
            }
        }else{
            return self::error('站点状态异常');
        }
        $sitemap->endSitemap();

        return self::success('站点地图生成成功');
    }

    public static function analytics($companyId){
        //$spiderLog = new SpiderLog(['company_id' => $companyId]);
        $spiderLogD = new SpiderLogD(['company_id' => $companyId]);

        $model = new MyadminWebsiteAnalytics();
        $last_record = $model->where('company_id',$companyId)->order('date desc')->find();
        $last_date = $last_record ? $last_record->date : null;

        $current_date = date('Y-m-d');
        if ($last_date == date('Y-m-d', strtotime('-1 day'))){
            return false;
        }
        if (!$last_date) {
            // 如果最后一条记录不存在，则默认取前天的日期
            $last_date = date('Y-m-d', strtotime('-1 day'));
        }

        $interval = floor((strtotime($current_date) - strtotime($last_date)) / (24 * 60 * 60));

        for ($i = 0; $i < $interval; $i++) {
            $date = date('Y-m-d', strtotime($last_date . " +$i day"));
            $record = $model->where(['company_id' => $companyId,'date' => $date])->find();

            if (!$record) {
                try {
                    $map['company_id'] = $companyId;
                    $map['createtime'] = array('between',[strtotime($date),strtotime($date)+24*3600]);
                    /*$data['company_id'] = $companyId;
                    $data['date'] = $date;
                    $data['ip'] = count($spiderLogD->where($map)->distinct('ip'));
                    $data['pv'] = $spiderLogD->where($map)->count();
                    $data['createtime'] = time();*/
                    $data = [
                        [
                            'company_id' => $companyId,
                            'date' => $date,
                            'type' => 'ip',
                            'num'  => count($spiderLogD->where($map)->distinct('ip')),
                            'createtime' => time(),
                        ],
                        [
                            'company_id' => $companyId,
                            'date' => $date,
                            'type' => 'pv',
                            'num'  => $spiderLogD->where($map)->count(),
                            'createtime' => time(),
                        ],
                    ];
                    $re = $model->insertAll($data);
                }catch (Exception $exception){
                    Log::error($exception->getMessage());
                }
            }
        }
    }

    public static function setEmployNum($domain){

        $domainInfo = Domain::where(['name'=>$domain])->cache($domain,60)->find();

        if ($domainInfo['domain_prefix'] != 'www'){
            $domain = $domainInfo['domain_prefix'] . '.' . $domain;
        }
        $num = self::getGoogleIndex($domain);
        if ($num){
            $re = $domainInfo->save(['employ_num'=>$num]);
        }

        return $num;
    }



    public static function getGoogleIndex($url,$ip = null) {
        $ipArr = config('ip.ip');
        $ip = $ipArr[array_rand($ipArr)];
        $cacheName = 'google_index_'.md5($url);
        $re = cache($cacheName);
        if (!$re){
            $url = 'http://'.$ip.':'.config('ip.port').'/employ?content='.$url;
            //echo $url;
            $re = Http::get($url);
            $re = json_decode($re,true);
            //var_dump($re);
            if ($re){
                cache($cacheName,$re,3*3600);
            }
        }

        preg_match('/About ([0-9,]+) results/', $re['r'], $matches);
        if (isset($matches[1])) {
            return str_replace(',', '', $matches[1]);
        } else {
            return 0;
        }
    }

    public static function getGoogleIndex1($url,$ip = null) {
        $googleUrl = "http://www.google.com/search?q=site:".urlencode($url);
        $client = new Client();//['proxy' => 'http://'.$ip.':39999']
        $response = $client->get($googleUrl);
        $html = (string)$response->getBody();

        return $html;

        $re = Http::get($googleUrl);
        return $re;



        var_dump($data);exit;

        if ($ip){
            $params = [
                'proxy' => 'http://'.$ip.':39999',
                'timeout' => 30,
            ];
        }else{
            $params = [
                'timeout' => 30,
            ];
        }

        $ql = QueryList::get($googleUrl,[],$params);
        echo $ql->getHtml();exit;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $googleUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:54.0) Gecko/20100101 Firefox/54.0");

        //curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC); //代理认证模式
        //curl_setopt($ch, CURLOPT_PROXY, "8.219.57.108"); //代理服务器地址
        //curl_setopt($ch, CURLOPT_PROXYPORT, 39999); //代理服务器端口
        //curl_setopt($ch, CURLOPT_PROXYUSERPWD, ":"); //http代理认证帐号，username:password的格式
        //curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); //使用http代理模式

        $html = curl_exec($ch);
        echo $html;exit;
        curl_close($ch);
        preg_match('/<div id="resultStats">About ([0-9,]+) results/', $html, $matches);
        if (isset($matches[1])) {
            return str_replace(',', '', $matches[1]);
        } else {
            return 0;
        }
    }
    
}