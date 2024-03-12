<?php

namespace app\admin\controller\myadmin\general;


use app\common\controller\Backend;
use think\addons\Service;
use think\Exception;

/**
 * 插件配置
 *
 * @icon fa fa-gears
 */
class Addon extends Backend
{


    /**
     * 插件配置
     */
    public function index()
    {
        $name = 'myadmin';
        $info = get_addon_info($name);
        $config = get_addon_fullconfig($name);
        if (!$info) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a", [], 'trim');
            if ($params) {
                if (isset($params['termvalidity']) && $params['termvalidity'] == 'vip') {
                    if (!get_addon_info('vip')) {
                        $this->error('请先购买并安装FastAdmin官方VIP插件！');
                    }
                }
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
                    //更新配置文件
                    set_addon_fullconfig($name, $config);
                    Service::refresh();
                    $this->success();
                } catch (Exception $e) {
                    $this->error(__($e->getMessage()));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $tips = [];
        $groupList = [];
        foreach ($config as $index => &$item) {
            //如果有设置分组
            if (isset($item['group']) && $item['group']) {
                if (!in_array($item['group'], $groupList)) {
                    $groupList["custom" . (count($groupList) + 1)] = $item['group'];
                }
            }
            if ($item['name'] == '__tips__') {
                $tips = $item;
                unset($config[$index]);
            }
        }
        $groupList['other'] = '其它';
        $this->view->assign("groupList", $groupList);
        $this->view->assign("addon", ['info' => $info, 'config' => $config, 'tips' => $tips]);
        $configFile = ADDON_PATH . $name . DS . 'config.html';
        $viewFile = is_file($configFile) ? $configFile : '/addon/config';
        return $this->view->fetch($viewFile);
    }
}
