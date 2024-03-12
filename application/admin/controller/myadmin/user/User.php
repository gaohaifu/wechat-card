<?php

namespace app\admin\controller\myadmin\user;

use app\common\controller\Backend;
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
    protected $noNeedRight = ['selectpage'];
    protected $searchFields = 'id,user_id,name,info.nickname,company.name';
    protected $selectpageFields = 'id,user_id,name';

    /**
     * @var \addons\myadmin\model\User
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \addons\myadmin\model\User;
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
                ->with(['group', 'info', 'company'])
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
        if ($this->request->isPost()) {
            $this->token();
        }
        $this->modelSceneValidate = true;
        $this->modelValidate = true;
        $time = time();
        $ip = $this->request->ip();
        $row = [
            'group_id' => '',
            'username' =>  strtolower(Random::alpha(8)),
            'nickname' => MyRandom::nickname(),
            'mobile' => '',
            'avatar' => '/assets/img/avatar.png',
            'level' => 0,
            'gender' => 1,
            'birthday' => date('Y-m-d'),
            'bio' => '',
            'money' => '0',
            'score' => '0',
            'joinip' => $ip,
            'jointime' => $time,
            'status' => 'normal',
        ];
        $this->assign('row', $row);
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($this->model->where('company_id', $params['company_id'])->where('user_id', $params['user_id'])->find()) {
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
        $this->view->assign('groupList', build_select('row[group_id]', \addons\myadmin\model\UserGroup::where('company_id', $row['company_id'])->column('id,name'), $row['group_id'], ['class' => 'form-control selectpicker']));
        return parent::edit($ids);
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
}
