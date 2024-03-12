<?php
namespace app\admin\library\xccms;

use think\Cache;
use app\admin\model\Admin;
use app\admin\model\Xccmssiteconfig;

class Service
{
    /**
     * 检查插件初始化
     */
    public static function check_xccms_init()
    {
        if (Xccmssiteconfig::count() == 0)
        {
            echo '请在[站点配置]中初始化后，再访问';
            exit;
        }
    }

    /**
     * 获取管理员表名称
     */
    public static function get_admin_nickname($id)
    {
        if (!$id)
        {
            return '';
        }
        $key = 'G_Admin_List';

        if (Cache::has($key)) {
            $AdminList = Cache::get($key);
        } 
        else 
        {
            $AdminList = array();
            $rows = Admin::field('id,nickname')->select();
            foreach($rows as $item)
            {
                $AdminList['id' . $item['id']] = $item['nickname'];
            }
            
            Cache::set($key, $AdminList, 1);
        }

        $re = isset($AdminList['id'. $id]) ? $AdminList['id'. $id] : '';

        return $re;
    }


}
