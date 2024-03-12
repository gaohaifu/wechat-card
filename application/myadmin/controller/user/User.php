<?php

namespace app\myadmin\controller\user;

use addons\myadmin\library\Backend;
use app\common\library\Auth;
use fast\Random;
use addons\myadmin\library\helper\MyRandom;

/**
 * 会员管理
 *
 * @icon fa fa-user
 */
class User extends Backend
{

    protected $relationSearch = true;
    protected $searchFields = 'id,name';
    protected $selectpageFields = 'id,user_id,username,name';

    /**
     * @var \addons\myadmin\model\User
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \addons\myadmin\model\User;
        $groupList = \addons\myadmin\model\UserGroup::where('company_id', COMPANY_ID)->column('id,name');
        $this->view->assign('groupList', $groupList ?: []);
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
                //$this->modelWith = 'bindinfo';
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = $this->model
                ->with(['group', 'info'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);
            foreach ($list as $k => $v) {
                $v->info->avatar = $v->info->avatar ? cdnurl($v->info->avatar, true) : letter_avatar($v->info->nickname);
                $v->info->hidden(['password', 'salt']);
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
        $this->modelSceneValidate = true;
        $this->modelValidate = true;
        $time = time();
        $ip = $this->request->ip();
        $row = [
            'group_id' => '',
            'money' => '0',
            'score' => '0',
            'joinip' => $ip,
            'jointime' => $time,
            'status' => 'normal',
        ];
        $this->assign('row', $row);
        $groupList = build_select('row[group_id]', \addons\myadmin\model\UserGroup::where('company_id', COMPANY_ID)->column('id,name'), $row['group_id'], ['class' => 'form-control selectpicker']);
        $this->view->assign('groupList', $groupList);
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($this->model->where('company_id', COMPANY_ID)->where('user_id', $params['user_id'])->find()) {
                $this->error(__('用户已存在'));
            }
        }
        return parent::add();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        if ($this->request->isPost()) {
            $this->token();
        }
        $row = $this->model->get($ids);
        $this->modelValidate = true;
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $groupList = build_select('row[group_id]', \addons\myadmin\model\UserGroup::where('company_id', COMPANY_ID)->column('id,name'), $row['group_id'], ['class' => 'form-control selectpicker']);
        $this->view->assign('groupList',  $groupList);
        return parent::edit($ids);
    }
}
