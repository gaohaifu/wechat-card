<?php

namespace app\admin\controller\myadmin;

use app\common\controller\Backend;

use think\Config;
use think\Hook;
use think\Exception;

/**
 * 高级配置
 *
 * @icon fa fa-circle-o
 */
class Addon extends Backend
{
    protected $model = null;
    protected $AddonModel = null;
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \addons\myadmin\model\Addons;
        $this->AddonModel = new \addons\myadmin\model\Addon;

        $typeList = $this->model->getTypeList();
        $this->view->assign("typeList", $typeList);
        $this->assignconfig("typeList", $typeList);
    }


    /**
     * 列表
     */

    public function index()
    {
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
                $ov->append(['addon']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }


    /**
     * 添加
     */
    public function add()
    {
        if ($apply = Config('addon_list')) {
            $haslist = \addons\myadmin\model\Addons::column('name');
            foreach ($apply as $key => $v) {
                // 过滤无效或已经存在的配置健名
                if (!in_array($v['name'], $haslist) && get_addon_config($v['name'])) {
                    $apply_key[] = $v['name'];
                } else {
                    unset($apply[$key]);
                }
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if (!in_array($params['name'], $apply_key)) {
                $this->error(__('配置健名不可用，请检查是否存在'));
            }
            return parent::add();
        }
        $this->view->assign("apply", $apply);
        return $this->view->fetch();
    }

    /**
     * Selectpage搜索
     *
     * @internal
     */
    public function selectpage()
    {
        return parent::selectpage();
    }

    /**
     * 企业配置列表
     */
    public function buy()
    {
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = $this->AddonModel
                ->where($where)
                ->with(['company'])
                ->order($sort, $order)
                ->paginate($limit);
            foreach ($list as $ov) {
                $ov->append(['addon', 'info', 'status']);
            }
            $result = array("total" => $list->total(), "rows" => $list->items());
            return json($result);
        }
        $name = $this->request->param('name', null);
        $company_id = $this->request->param('company_id', null);
        $this->view->assign("name", $name);
        $this->assignconfig("name", $name);
        $this->view->assign("company_id", $company_id);
        $this->assignconfig("company_id", $company_id);
        return $this->view->fetch();
    }

    /**
     * 删除企业配置
     */
    public function buydel($ids = null)
    {
        $this->model = $this->AddonModel;
        return parent::del($ids);
    }

    /**
     * 批量企业配置
     */
    public function buymulti($ids = null)
    {
        $this->model = $this->AddonModel;
        return parent::multi($ids);
    }

    /**
     * 企业配置
     */
    public function config($name = null, $company_id = null)
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
        $hasid = $this->AddonModel->where('company_id', $company_id)->where('name', $name)->value('id');
        if (!$company_id) {
            $this->error(__('No Results were found'));
        }
        $info = get_addon_info($name);
        $config = get_addon_fullconfig($name);
        $company_config = $this->AddonModel->where('company_id', $company_id)->where('name', $name)->value('config');

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
                    if ($hasid && $name) {
                        $this->AddonModel->where('company_id', $company_id)->where('name', $name)->update(['config' => $configList]);
                        $this->success();
                    }
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
