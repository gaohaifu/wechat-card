<?php

namespace app\common\service;

use addons\myadmin\model\Company;
use app\admin\model\Swmultilingual;
use fast\Http;
use GeoIp2\Database\Reader;

class Language extends Base
{
    public static function isChinese($str){
        if (preg_match("/[\x7f-\xff]/", $str)) {
            return true;
        }else{
            return false;
        }
    }

    /**
     * 获取客户的语言代码
     * @return bool|int|string
     */
    public static function sourceLanguage(){
        $reLang = '';
        $lang = Domain::preferedLanguage($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        if (!$lang){
            $lang = Domain::ipLanguage('zh_CN');
        }
        $reLang = $lang;
        if ($lang == 'zh'){
            $reLang = '';
        }else{
            $countList = [
                'Japan' => 'ja',
            ];
            if (isset($countList[$lang])){
                $reLang = $countList[$lang];
            }
        }


        return $reLang;
    }


    /**
     * 语言检测
     * @param $str
     * @return mixed|string
     * @throws \Text_LanguageDetect_Exception
     */
    public static function languageDetect($str){
        if (self::isChinese($str)){
            return 'chinese';
        }

        $ld = new \Text_LanguageDetect();
        $results = $ld->detect($str, 3);
        var_dump($results);exit();
        if ($results){
            $keys = array_keys($results);
            return reset($keys);
        }else{
            return '';
        }
    }

    /**
     * 多语言网站列表
     * @return bool|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function languageList($companyId = ''){
        $model = new Swmultilingual();
        $where['status'] = 1;
        if ($companyId){
            $company = Company::with('group')->find($companyId);
            if ($company && $company->group->swmultilingual_ids){
                $where['id'] = array('in',$company->group->swmultilingual_ids);
            }
        }
        $list = $model->where($where)->select();

        $langSign       = (new \addons\swmultilingual\Swmultilingual())->langSign;
        $config         = get_addon_config('swmultilingual');
        $primaryDomains = explode("\r\n", $config['primaryDomains']);

        foreach ($list as &$v) {
            $mode       = isset($v['extends']['mode']) ? $v['extends']['mode'] : 'show';
            switch ($mode) {
                case 'domain':
                    $arr      = explode('.', $primaryDomains[0], 2);
                    $v['url'] = 'http://' . implode('.', array_merge([strtolower($v['route'])], [$arr[1]]));
                    break;
                case 'show':
                    $v['url'] = '/' . $v['route'] . '/';
                    break;
                default :
                    $v['url'] = '/?' . $langSign . '=' . $v['route'];
                    break;
            }

            $v['icon']  = $v['icon'] ? : letter_avatar($v['name']);
        }
        return $list;
    }
}
