<?php

namespace app\admin\controller\xccms;

use app\common\controller\Backend;
use think\Db;

/**
 * 配置管理
 *
 * @icon fa fa-circle-o
 */
class Xccmsconfig extends Backend
{
    
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */




    /**
     * Selectpage搜索
     */
    public function selectpage()
    {
        $key = input('key', '');
        $name = input('name', '') ? input('name') : input('searchValue');
        $page = input('pageNumber', 1);
        $pagesize = 10;
        $config = get_addon_config('xccms');
        $list = array();

        foreach($config[$key] as $key=>$item)
        {
            if ($name)
            {
                if (strpos($item, $name) !== false)
                {
                    array_push($list, array('id'=>$item, 'name'=>$item));
                }
            }
            else
            {
                array_push($list, array('id'=>$item, 'name'=>$item));
            }
        }

        $index = ($page - 1) * $pagesize;
        $max_index = $index + $pagesize;
        $max_index = $max_index > count($list) ? count($list) : $max_index;

        $new_list = [];
        for($i=$index; $i<$max_index;$i++)
        {
            array_push($new_list, $list[$i]);
        }
        
        return json(array(
            'list'=>$new_list,
            'total'=>count($list)
        ));
    }


}
