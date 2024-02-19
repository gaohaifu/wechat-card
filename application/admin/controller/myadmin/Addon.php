<?php

namespace app\admin\controller\myadmin;

use app\common\controller\Backend;

use think\Config;
use think\Hook;

/**
 * 高级配置
 *
 * @icon fa fa-circle-o
 */
class Addon extends Backend
{
    protected $model = null;
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
     * Selectpage搜索
     *
     * @internal
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
}
