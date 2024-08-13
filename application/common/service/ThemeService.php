<?php

namespace app\common\service;

use app\common\model\xccms\Xccmssitetheme;

/**
 * 文件
 */
class ThemeService extends Base
{

    public static function getThemeText($company_id,$themeName){
        $themeRow = Xccmssitetheme::where('company_id',$company_id)->where('name',$themeName)->find();
        if (!$themeRow){
            $config = get_addon_config('xccms');
            $config_theme_ext_theme = $config['theme_ext'][$themeName];
            if (!$config_theme_ext_theme){
                return [];
            }
            $re = Xccmssitetheme::insert([
                'company_id'=>$company_id,
                'name'=>$themeName,
                'content' => $config_theme_ext_theme,
                'createtime' => time()
            ]);
            if ($re){
                return self::getThemeText($company_id,$themeName);
            }else{
                return [];
            }
        }else{
            return json_decode($themeRow->content,true);
        }


    }
    public static function setThemeText($company_id,$themeName,$content){
        $themeRow = Xccmssitetheme::where('company_id',$company_id)->where('name',$themeName)->find();
        if($themeRow){
            $re = $themeRow->save(['content'=>$content]);
        }else{
            $re = false;
        }
        return $re;
    }
}
