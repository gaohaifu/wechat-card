<?php

namespace app\myadmin\controller\general;

use addons\myadmin\library\Backend;
use fast\Http;
use think\addons\AddonException;
use think\addons\Service;
use think\Cache;
use think\Config;
use think\Db;
use think\Exception;

/**
 * 高级配置
 */
class Addon extends Backend
{
    protected $model = null;
    protected $noNeedRight = ['config'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \addons\myadmin\model\Addons;
        $this->AddonModel = new \addons\myadmin\model\Addon;
        if (!$this->auth->isSuperAdmin() && in_array($this->request->action(), ['install', 'uninstall', 'local', 'upgrade'])) {
            $this->error(__('Access is allowed only to the super management group'));
        }
    }


    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);
            foreach ($list as $ov) {
                if ($has = $this->AddonModel->where('name', $ov['name'])->where('company_id', COMPANY_ID)->find()) {
                    $has->append(['status']);
                    $ov->status = $has['status'];
                } else {
                    $ov->status = '异常';
                }
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 配置
     */
    public function config($name = null)
    {
        $name = $name ? $name : $this->request->get("name");
        if (!$name) {
            $this->error(__('Parameter %s can not be empty', 'name'));
        }
        if (!preg_match("/^[a-zA-Z0-9]+$/", $name)) {
            $this->error(__('Addon name incorrect'));
        }
        if (!is_dir(ADDON_PATH . $name)) {
            $this->error(__('Directory not found'));
        }
        $info = get_addon_info($name);
        $config = get_addon_fullconfig($name);
        $company_config = $this->AddonModel->where('company_id', COMPANY_ID)->where('name', $name)->value('config');

        if ($company_config) {
            $company_config = json_decode($company_config, true);
            foreach ($config as $key => $ad) {
                if (isset($company_config[$ad['name']])) {
                    $company_value = $company_config[$ad['name']];
                    if (is_array($company_value)) {
                        $company_value = array_merge($config[$key]['value'], $company_value);
                    }
                    $config[$key]['value'] = $company_value;
                }
            }
        }
        if (!$info) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a", [], 'trim');
            if ($params) {
                $configList = [];
                foreach ($config as $k => &$v) {
                    if (isset($params[$v['name']])) {
                        if ($v['type'] == 'array') {
                            $params[$v['name']] = is_array($params[$v['name']]) ? $params[$v['name']] : (array)json_decode($params[$v['name']], true);
                            $value = $params[$v['name']];
                        } else {
                            $value = is_array($params[$v['name']]) ? implode(',', $params[$v['name']]) : $params[$v['name']];
                        }
                        $v['value'] = $value;
                        $configList[$v['name']] = $value;
                    }
                }
                $configList = json_encode($configList, JSON_UNESCAPED_UNICODE);
                try {
                    if ($this->AddonModel->where('company_id', COMPANY_ID)->where('name', $name)->value('id')) {
                        $this->AddonModel->where('company_id', COMPANY_ID)->where('name', $name)->update(['config' => $configList]);
                    } else {
                        $data['config'] = $configList;
                        $data['name'] = $name;
                        $data['company_id'] = COMPANY_ID;
                        $data['forever'] = 1;
                        $data['begintime'] = time();
                        $data['endtime'] = 0;
                        $this->AddonModel->insert($data);
                    }
                    $this->success();
                } catch (Exception $e) {
                    $this->error(__($e->getMessage()));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $tips = [];
        foreach ($config as $index => &$item) {
            if ($item['name'] == '__tips__') {
                $tips = $item;
                unset($config[$index]);
            }
        }
        $addon = ['info' => $info, 'config' => $config, 'tips' => $tips];
        $this->view->assign("addon", $addon);
        $configFile = ADDON_PATH . $name . DS . 'config.html';
        $viewFile = is_file($configFile) ? $configFile : '';
        return $this->view->fetch($viewFile);
    }
}
