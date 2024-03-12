<?php

namespace app\admin\model;

use think\Model;


class Xccmsconfig extends Model
{


    /**
     * 获得键值
     */    
    public static function get_value($key)
    {
        $re = '';
        if ($key)
        {
            $config = get_addon_config('xccms');
            $re = $config[$key];
        }

        return $re;
    }
    
    /**
     * Selectpage搜索
     */
    public static function selectpage($key)
    {
        $re = array();
        if ($key)
        {
            $config = get_addon_config('xccms');
            $list = array();
            foreach($config[$key] as $key=>$item)
            {
                array_push($list, array('id'=>$item, 'name'=>$key));
            }
            $re = $list;
        }

        return $re;
    }






}
