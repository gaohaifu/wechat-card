<?php

namespace app\admin\controller\myadmin\auth;

use addons\myadmin\model\AuthGroup;
use app\common\controller\Backend;

/**
 * 管理员日志
 *
 * @icon   fa fa-users
 * @remark 管理员可以查看自己所拥有的权限的管理员日志
 */
class Adminlog extends Backend
{

    /**
     * @var \addons\myadmin\model\AdminLog
     */
    protected $model = null;
    protected $searchFields = 'title,username,company.name';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \addons\myadmin\model\AdminLog;

        $groupName = AuthGroup::column('id,name');
        $this->view->assign('groupdata', $groupName);
    }

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = $this->model
                ->where($where)
                ->with(['company'])
                ->order($sort, $order)
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 详情
     */
    public function detail($ids)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $this->view->assign("row", $row->toArray());
        return $this->view->fetch();
    }

    /**
     * 添加
     * @internal
     */
    public function add()
    {
        $this->error();
    }

    /**
     * 编辑
     * @internal
     */
    public function edit($ids = null)
    {
        $this->error();
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if (!$this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ? $ids : $this->request->post("ids");
        if ($ids) {
            $adminList = $this->model->where('id', 'in', $ids)->select();
            if ($adminList) {
                $deleteIds = [];
                foreach ($adminList as $k => $v) {
                    $deleteIds[] = $v->id;
                }
                if ($deleteIds) {
                    $this->model->destroy($deleteIds);
                    $this->success();
                }
            }
        }
        $this->error();
    }

    /**
     * 批量更新
     * @internal
     */
    public function multi($ids = "")
    {
        // 管理员禁止批量操作
        $this->error();
    }

    public function selectpage()
    {
        return parent::selectpage();
    }
}
