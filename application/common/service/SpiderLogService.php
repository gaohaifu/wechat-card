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
use hisorange\BrowserDetect\Stages\BrowserDetect;
use think\Env;
use think\Exception;
use think\Log;
use GuzzleHttp\Client;
use hisorange\BrowserDetect\Parser as Browser;



/**
 * 访问日志相关
 * @package app\common\service
 */
class SpiderLogService extends Base
{
    public function __construct()
    {
    }
    public static function getvisits($where){
        $spiderLogD = new SpiderLogD($where);
        unset($where['createtime']);
        $allVisits = $spiderLogD->where($where)->count();

        $currentYear = date('Y'); // 获取当前年份
        $yearArray = $visitsArr = array(); // 创建一个空数组来存储年份

        for ($i = 0; $i < 7; $i++) {
            $year = strtotime("-$i year"); // 获取当前年份减去 $i 年的时间戳
            $yearStr = date('Y', $year); // 根据时间戳获取年份
            $yearArray[] = $yearStr; // 将年份添加到数组中
        }

        sort($yearArray); // 对年份数组进行排序
        foreach ($yearArray as $item){
            $start = strtotime($item.'-01-01');
            $end = strtotime(($item+1).'-01-01');
            $where['createtime'] = array('between',[$start,$end]);
            $visits = $spiderLogD->where($where)->count();
            $visitsArr[] = $visits;
        }

        return ['all_visits' => $allVisits,'category' => $yearArray,'visits_arr' => $visitsArr];
    }
    public static function getCountryData($where){
        $spiderLogD = new SpiderLogD($where);
        $list = [];
        $referringSocialMediaArr = $systemArr = $browserArr = $countryArr = $referrerArr = [];
        $list = $spiderLogD->where($where)->field('country,referrer,ua,platform,browser,createtime')->select();
        /*$spiderLogD
            ->where($where)
            ->field('country,referrer,ua,platform,browser,createtime')
        ->chunk(100, function ($items) use (&$list) {
            foreach ($items as $item){
                if (isset($item->platform) && $item->platform){
                    $os = $item->platform;
                    $browser = $item->browser;
                }else{
                    // 解析User Agent字符串，获取浏览器和操作系统信息
                    //$browserInfo = get_browser($item->ua, true);
                    $browserInfo = self::getBrowser($item->ua);
                    // 获取操作系统和浏览器信息
                    $os = $browserInfo['platform'];
                    $browser = $browserInfo['browser'];
                    $re = $item->save([
                        'platform' => $os,
                        'browser' => $browser,
                    ]);
                }
            }
            $list = array_merge($list,$items);
        }, 'id');*/

        foreach ($list as $item){
            if (isset($item->referrer) && $item->referrer){
                $item->referring_social_media = self::getReferringSocialMedia($item->referrer);
            }else{
                $item->referring_social_media = '';
            }

            $os = $item->platform;
            $os = $item->platform;
            $browser = $item->browser;
            $referrer = $item->referrer??'Others';
            if ($referrer == ''){
                $referrer = 'Others';
            }
            $country = self::getCountry($item->country);
            isset($countryArr[$country]) ? $countryArr[$country] += 1 : $countryArr[$country] = 1;
            isset($referrerArr[$referrer]) ? $referrerArr[$referrer] += 1 : $referrerArr[$referrer] = 1;
            isset($systemArr[$os]) ? $systemArr[$os] += 1 : $systemArr[$os] = 1;
            if ($item->referring_social_media){
                isset($referringSocialMediaArr[$item->referring_social_media]) ? $referringSocialMediaArr[$item->referring_social_media] += 1 : $referringSocialMediaArr[$item->referring_social_media] = 1;
            }
            isset($browserArr[$browser]) ? $browserArr[$browser] += 1 : $browserArr[$browser] = 1;

        }
        $countryArr = self::proportionDataFormat($countryArr,count($list),'country');
        $referrerArr = self::proportionDataFormat($referrerArr,count($list));
        $systemArr = self::dataFormat($systemArr);
        $browserArr = self::dataFormat($browserArr);
        $referringSocialMediaArr = self::dataFormat($referringSocialMediaArr);
        return [$countryArr,$systemArr,$browserArr,$referrerArr,$referringSocialMediaArr];
    }
    public static function getReferringSocialMedia($site) {
        $socialMedia = array(
            'Facebook' => array('fb.com', 'facebook.com'),
            'Twitter' => array('twtr.com', 'twitter.com'),
            'Instagram' => array('instagram.com'),
            'Linkedin' => array('linkedin.com'),
            'Youtube' => array('yt.com', 'youtube.com'),
            'Pinterest' => array('pinterest.com')
        );

        foreach ($socialMedia as $media => $domains) {
            foreach ($domains as $domain) {
                if (stripos($site, $domain) !== false) {
                    return $media;
                }
            }
        }

        return '';
    }

    public static function getBrowser($ua){
        $browserInfo = cache($ua);
        if (!$browserInfo){
            // 解析User Agent字符串，获取浏览器和操作系统信息
            $browserInfo = get_browser($ua, true);
            cache($ua,$browserInfo,24*3600);
        }

        return $browserInfo;
    }
    public static function proportionDataFormat($arr,$total,$type = ''){
        $data = [];
        foreach ($arr as $key => $item){
            $data[] = [
                'name' => $key,
                'num' => $item,
                'proportion' => number_format($item / $total * 100,1) . '%',
            ];
        }
        // 按照比例降序排列
        $numArr = [];
        foreach ($data as $key => $row){
            $numArr[$key] = $row['num'];
        }
        array_multisort($numArr, SORT_DESC, $data);
        if ($type == 'country'){
            $data = self::swapArray($data);
        }

        // 截取前10个
        $data = array_slice($data, 0, 10);

        return ($data);
    }

    /**
     * 当第一个为中国时  把他换到最后一个
     * @param $array
     * @return mixed
     */
    public static function swapArray($array) {
        $length = count($array);
        if ($length > 1){
            // 获取第一个子数组
            $firstArray = $array[0];
            // 获取最后一个子数组
            $lastArray = $array[$length - 1];
            // 判断第一个子数组的name是否为"chinese"
            if ($firstArray['name'] == 'China') {
                // 交换第一个子数组与最后一个子数组
                $array[0]['name'] = $lastArray['name'];
                $array[$length - 1]['name'] = $firstArray['name'];
            }
        }

        return $array;
    }
    public static function dataFormat($arr){
        $data = [];
        foreach ($arr as $key => $item){
            $data[] = [
                'name' => $key,
                'value' => $item,
            ];
        }
        return json_encode($data);
    }


    public static function getSpiderLogD($where){
        $spiderLogD = new SpiderLogD($where);
        $visits = $spiderLogD->where($where)->count();
        $where['country'] = array('eq',null);
        $ipArr = $spiderLogD->where($where)->distinct('ip');
        //var_dump($ipArr);exit();
        foreach ($ipArr as $item){
            //IP转省区代码
            $csmip = \addons\csmip\library\Csmip::getInstance();
            $region = $csmip->getRegion($item);
            echo $region->country;//打印国家
            echo $region->region;//打印区域
            echo $region->province;//打印省区
            echo $region->city;//打印城市

            //var_dump($region);exit();
        }
        return [$visits];

    }


    public static function getCountry($key){
        $countries = array(
            '阿富汗' => 'Afghanistan',
            '阿尔巴尼亚' => 'Albania',
            '阿尔及利亚' => 'Algeria',
            '安道尔' => 'Andorra',
            '安哥拉' => 'Angola',
            '安提瓜和巴布达' => 'Antigua-and-Barbuda',
            '阿根廷' => 'Argentina',
            '亚美尼亚' => 'Armenia',
            '澳大利亚' => 'Australia',
            '奥地利' => 'Austria',
            '阿塞拜疆' => 'Azerbaijan',
            '巴哈马' => 'Bahamas',
            '巴林' => 'Bahrain',
            '孟加拉' => 'Bangladesh',
            '巴巴多斯' => 'Barbados',
            '白俄罗斯' => 'Belarus',
            '比利时' => 'Belgium',
            '伯利兹' => 'Belize',
            '贝宁' => 'Benin',
            '不丹' => 'Bhutan',
            '玻利维亚' => 'Bolivia',
            '波黑' => 'Bosnia-and-Herzegovina',
            '博茨瓦纳' => 'Botswana',
            '巴西' => 'Brazil',
            '文莱' => 'Brunei',
            '保加利亚' => 'Bulgaria',
            '布基纳法索' => 'Burkina-Faso',
            '布隆迪' => 'Burundi',
            '柬埔寨' => 'Cambodia',
            '喀麦隆' => 'Cameroon',
            '加拿大' => 'Canada',
            '佛得角' => 'Cape-Verde',
            '中非共和国' => 'Central-African-Republic',
            '乍得' => 'Chad',
            '智利' => 'Chile',
            '中国' => 'China',
            '香港' => 'China',
            '澳门' => 'China',
            '哥伦比亚' => 'Colombia',
            '科摩罗' => 'Comoros',
            '刚果金' => 'Congo-democratic-republic',
            '刚果民主共和国' => 'Congo-democratic-republic',
            '刚果布' => 'Congo-Republic',
            '刚果共和国' => 'Congo-Republic',
            '哥斯达黎加' => 'Costa-Rica',
            '科特迪瓦' => 'Cote-d-ivoire',
            '克罗地亚' => 'Croatia',
            '古巴' => 'Cuba',
            '塞浦路斯' => 'Cyprus',
            '捷克' => 'Czech-Republic',
            '捷克共和国' => 'Czech-Republic',
            '丹麦' => 'Denmark',
            '格陵兰' => 'Denmark',
            '法罗群岛' => 'Denmark',
            '吉布提' => 'Djibouti',
            '多米尼加' => 'Dominica',
            '多明尼加共和国' => 'Dominican-Republic',
            '东帝汶' => 'East-Timor',
            '厄瓜多尔' => 'Ecuador',
            '埃及' => 'Egypt',
            '萨尔瓦多' => 'El-Salvador',
            '赤道几内亚' => 'Equatorial-Guinea',
            '厄立特里亚' => 'Eritrea',
            '爱沙尼亚' => 'Estonia',
            '埃塞俄比亚' => 'Ethiopia',
            '斐济' => 'Fiji',
            '芬兰' => 'Finland',
            '法国' => 'France',
            '法属圭亚那' => 'France',
            '法属波利尼西亚' => 'France',
            '瓜德罗普' => 'France',
            '留尼旺' => 'France',
            '马提尼克' => 'France',
            '新喀里多尼亚' => 'France',
            '加蓬' => 'Gabon',
            '冈比亚' => 'Gambia',
            '格鲁吉亚' => 'Georgia',
            '德国' => 'Germany',
            '加纳' => 'Ghana',
            '希腊' => 'Greece',
            '格林纳达' => 'Grenada',
            '危地马拉' => 'Guatemala',
            '几内亚比绍' => 'Guinea-Bissau',
            '几内亚' => 'Guinea',
            '圭亚那' => 'Guyana',
            '海地' => 'Haiti',
            '洪都拉斯' => 'Honduras',
            '匈牙利' => 'Hungary',
            '冰岛' => 'Iceland',
            '印度' => 'India',
            '印度尼西亚' => 'Indonesia',
            '伊朗' => 'Iran',
            '伊拉克' => 'Iraq',
            '爱尔兰' => 'Ireland',
            '以色列' => 'Israel',
            '意大利' => 'Italy',
            '牙买加' => 'Jamaica',
            '日本' => 'Japan',
            '约旦' => 'Jordan',
            '哈萨克斯坦' => 'Kazakhstan',
            '肯尼亚' => 'Kenya',
            '基里巴斯' => 'Kiribati',
            '科索沃' => 'Kosovo',
            '科威特' => 'Kuwait',
            '吉尔吉斯斯坦' => 'Kyrgyzstan',
            '老挝' => 'Laos',
            '拉脱维亚' => 'Latvia',
            '黎巴嫩' => 'Lebanon',
            '莱索托' => 'Lesotho',
            '利比里亚' => 'Liberia',
            '利比亚' => 'Libya',
            '列支敦士登' => 'Liechtenstein',
            '立陶宛' => 'Lithuania',
            '卢森堡' => 'Luxembourg',
            '马其顿' => 'Macedonia',
            '马达加斯加' => 'Madagascar',
            '马拉维' => 'Malawi',
            '马来西亚' => 'Malaysia',
            '马尔代夫' => 'Maldives',
            '马里' => 'Mali',
            '马耳他' => 'Malta',
            '马绍尔群岛' => 'Marshall-Islands',
            '毛里塔尼亚' => 'Mauritania',
            '毛里求斯' => 'Mauritius',
            '墨西哥' => 'Mexico',
            '密克罗尼西亚' => 'Micronesia',
            '摩尔多瓦' => 'Moldova',
            '摩纳哥' => 'Monaco',
            '蒙古' => 'Mongolia',
            '黑山' => 'Montenegro',
            '摩洛哥' => 'Morocco',
            '莫桑比克' => 'Mozambique',
            '缅甸' => 'Myanmar',
            '纳米比亚' => 'Namibia',
            '瑙鲁' => 'Nauru',
            '尼泊尔' => 'Nepal',
            '荷兰' => 'Netherlands',
            '库拉索' => 'Netherlands',
            '阿鲁巴' => 'Netherlands',
            '荷属圣马丁' => 'Netherlands',
            '新西兰' => 'New-Zealand',
            '尼加拉瓜' => 'Nicaragua',
            '尼日尔' => 'Niger',
            '尼日利亚' => 'Nigeria',
            '纽埃' => 'Niue',
            '北朝鲜' => 'North-Korea',
            '挪威' => 'Norway',
            '阿曼' => 'Oman',
            '巴基斯坦' => 'Pakistan',
            '帕劳' => 'Palau',
            '巴拿马' => 'Panama',
            '巴布亚新几内亚' => 'Papua-New-Guinea',
            '巴拉圭' => 'Paraguay',
            '秘鲁' => 'Peru',
            '菲律宾' => 'Philippines',
            '波兰' => 'Poland',
            '葡萄牙' => 'Portugal',
            '卡塔尔' => 'Qatar',
            '罗马尼亚' => 'Romania',
            '俄罗斯' => 'Russia',
            '卢旺达' => 'Rwanda',
            '圣基茨和尼维斯' => 'Saint-Kitts-and-Nevis',
            '圣卢西亚' => 'Saint-Lucia',
            '圣文森特和格林纳丁斯' => 'Saint-Vincent-and-the-Grenadines',
            '萨摩亚' => 'Samoa',
            '圣马力诺' => 'San-Marino',
            '圣多美和普林西比' => 'Sao-tome-and-principe',
            '沙特阿拉伯' => 'Saudi-Arabia',
            '塞内加尔' => 'Senegal',
            '塞尔维亚' => 'Serbia',
            '塞舌尔' => 'Seychelles',
            '塞拉利昂' => 'Sierra-Leone',
            '新加坡' => 'Singapore',
            '斯洛伐克' => 'Slovakia',
            '斯洛文尼亚' => 'Slovenia',
            '所罗门群岛' => 'Solomon-Islands',
            '索马里' => 'Somalia',
            '南非' => 'South-Africa',
            '韩国' => 'South-Korea',
            '南苏丹' => 'South-Sudan',
            '西班牙' => 'Spain',
            '斯里兰卡' => 'Sri-Lanka',
            '苏丹' => 'Sudan',
            '苏里南' => 'Suriname',
            '斯威士兰' => 'Swaziland',
            '瑞典' => 'Sweden',
            '瑞士' => 'Switzerland',
            '叙利亚' => 'Syria',
            '台湾' => 'Taiwan',
            '塔吉克斯坦' => 'Tajikistan',
            '坦桑尼亚' => 'Tanzania',
            '泰国' => 'Thailand',
            '多哥' => 'Togo',
            '汤加' => 'Tonga',
            '特立尼达和多巴哥' => 'Trinidad-and-Tobago',
            '突尼斯' => 'Tunisia',
            '土耳其' => 'Turkey',
            '土库曼斯坦' => 'Turkmenistan',
            '图瓦卢' => 'Tuvalu',
            '乌干达' => 'Uganda',
            '乌克兰' => 'Ukraine',
            '阿联酋' => 'United-Arab-Emirates',
            '英国' => 'United-Kingdom',
            '安圭拉' => 'United-Kingdom',
            '百慕大' => 'United-Kingdom',
            '蒙塞拉特岛' => 'United-Kingdom',
            '根西岛' => 'United-Kingdom',
            '特克斯和凯科斯群岛' => 'United-Kingdom',
            '开曼群岛' => 'United-Kingdom',
            '泽西岛' => 'United-Kingdom',
            '英属维尔京群岛' => 'United-Kingdom',
            '多米尼克' => 'United-Kingdom',
            '美国' => 'United-States',
            '美属维尔京群岛' => 'United-States',
            '波多黎各' => 'United-States',
            '北马里亚纳群岛' => 'United-States',
            '关岛' => 'United-States',
            '美属萨摩亚' => 'United-States',
            '乌拉圭' => 'Uruguay',
            '乌兹别克斯坦' => 'Uzbekistan',
            '梵蒂冈城' => 'Vatican-City',
            '委内瑞拉' => 'Venezuela',
            '越南' => 'Vietnam',
            '也门' => 'Yemen',
            '赞比亚' => 'Zambia',
            '津巴布韦' => 'Zimbabwe',
            '欧洲' => 'Europe',
            '亚太地区' => 'Asia Pacific region',
        );
        return $countries[$key]??$key;
    }
    
}