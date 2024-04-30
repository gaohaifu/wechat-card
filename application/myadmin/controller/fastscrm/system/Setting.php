<?php

namespace app\myadmin\controller\fastscrm\system;

use app\myadmin\controller\fastscrm\Scrmbackend;
use think\addons\Service;
use think\Exception;
/**
 * 基本设置
 *
 * @icon fa fa-circle-o
 */
class Setting extends Scrmbackend
{

    /**
     * Tasklog模型对象
     * @var \app\admin\model\fastscrm\system\Tasklog
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\system\Tasklog;
        $this->view->assign("statusList", $this->model->getStatusList());
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    public function index($name = 'fastscrm'){
        $name = $name ? $name : $this->request->get("name");
        if (!$name) {
            $this->error(__('Parameter %s can not be empty', 'name'));
        }
        if (!preg_match("/^[a-zA-Z0-9]+$/", $name)) {
            $this->error(__('Addon name incorrect'));
        }
        $info = get_addon_info($name);
        $config = get_addon_fullconfig($name);
        if (!$info) {
            $this->error(__('Addon not exists'));
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a", [], 'trim');
            var_dump(111);exit;
            if ($params) {
                foreach ($config as $k => &$v) {
                    if (isset($params[$v['name']])) {
                        if ($v['type'] == 'array') {
                            $params[$v['name']] = is_array($params[$v['name']]) ? $params[$v['name']] : (array)json_decode($params[$v['name']], true);
                            $value = $params[$v['name']];
                        } else {
                            $value = is_array($params[$v['name']]) ? implode(',', $params[$v['name']]) : $params[$v['name']];
                        }
                        $v['value'] = $value;
                    }
                }
                try {
                    $addon = get_addon_instance($name);
                    //插件自定义配置实现逻辑
                    if (method_exists($addon, 'config')) {
                        $addon->config($name, $config);
                    } else {
                        //更新配置文件
                        set_addon_fullconfig($name, $config);
                        Service::refresh();
                    }
                } catch (Exception $e) {
                    $this->error(__($e->getMessage()));
                }
                $this->success();
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $tips = [];
        $groupList = [];
        $ungroupList = [];
        foreach ($config as $index => &$item) {
            //如果有设置分组
            if (isset($item['group']) && $item['group']) {
                if (!in_array($item['group'], $groupList)) {
                    $groupList["custom" . (count($groupList) + 1)] = $item['group'];
                }
            } elseif ($item['name'] != '__tips__') {
                $ungroupList[] = $item['name'];
            }
            if ($item['name'] == '__tips__') {
                $tips = $item;
                unset($config[$index]);
            }
        }
        if ($ungroupList) {
            $groupList['other'] = '其它';
        }
        $this->view->assign("domain", $_SERVER['HTTP_HOST']);
        $this->view->assign("groupList", $groupList);
        $this->view->assign("addon", ['info' => $info, 'config' => $config, 'tips' => $tips]);
        return $this->view->fetch();
    }
}
