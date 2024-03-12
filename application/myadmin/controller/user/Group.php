<?php

namespace app\myadmin\controller\user;

use addons\myadmin\library\Backend;

/**
 * 会员组管理
 *
 * @icon fa fa-users
 */
class Group extends Backend
{

    /**
     * @var \addons\myadmin\model\UserGroup
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \addons\myadmin\model\UserGroup;
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $this->token();
        }
        $nodeList = \addons\myadmin\model\UserRule::getTreeList();
        $this->assign("nodeList", $nodeList);
        return parent::add();
    }

    public function edit($ids = null)
    {
        if ($this->request->isPost()) {
            $this->token();
        }
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $rules = explode(',', $row['rules']);
        $nodeList = \addons\myadmin\model\UserRule::getTreeList($rules);
        $this->assign("nodeList", $nodeList);
        return parent::edit($ids);
    }

}
