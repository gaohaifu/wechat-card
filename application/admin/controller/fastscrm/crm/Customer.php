<?php

namespace app\myadmin\controller\fastscrm\crm;

use addons\fastscrm\library\Job\AddJob;
use addons\fastscrm\library\WeWork;
use app\myadmin\controller\fastscrm\Scrmbackend;
use fast\Http;
use think\Db;

/**
 * 员工管理
 *
 * @icon fa fa-circle-o
 */
class Customer extends Scrmbackend
{

    /**
     * Customer模型对象
     * @var \app\admin\model\fastscrm\crm\Customer
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\crm\Customer;
        $this->view->assign("typeList", $this->model->getTypeList());
        $this->view->assign("genderList", $this->model->getGenderList());
        $this->view->assign("flAddWayList", $this->model->getFlAddWayList());
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

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
            foreach ($list as $row) {
                $fl_tags = json_decode($row->fl_tags);
                $fl_mobile = json_decode($row->fl_remark_mobiles);
                if (!empty($fl_mobile)) {
                    $row->fl_remark_mobiles = implode(',', json_decode($row->fl_remark_mobiles));
                } else {
                    $row->fl_remark_mobiles = '';
                }

                if (!empty($fl_tags)) {
                    $fl_tags = implode(',', $fl_tags);
                    $tags = Db::name('fastscrm_tag')->where('tag_id', 'in', $fl_tags)->select();
                    $row->tags = $tags;
                } else {
                    $row->tags = [];
                }
                $fl_user_name = Db::name('fastscrm_worker')->where('userid', $row->fl_userid)->value('name');
                $row->fl_user_name = $fl_user_name;
            }
            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 添加客户标签
     */
    public function addtags($ids = "")
    {
        if ($this->request->isPost()) {
            $customers = $this->request->post("customers");
            $tags = $this->request->post("tags");
            $tags = explode(',', $tags);
            $customers = explode(',', $customers);
            $work = new WeWork('App');
            if (!empty($tags)) {
                foreach ($customers as $key => $customer) {
                    $user = \app\admin\model\fastscrm\crm\Customer::where('id',
                        $customer)->field('external_userid,fl_userid,fl_tags')->find();
                    if (empty($user->fl_tags)) {
                        $addtags = $tags;
                        $updatetags = $addtags;
                    } else {
                        $addtags = array_values(array_diff($tags, json_decode($user->fl_tags)));
                        $updatetags = array_merge($addtags, json_decode($user->fl_tags));
                    }
                    if (!empty($addtags)) {
                        $param['userid'] = $user->fl_userid;
                        $param['external_userid'] = $user->external_userid;
                        $param['add_tag'] = $addtags;
                        $work->addUsersTags($param);
                        \app\admin\model\fastscrm\crm\Customer::where('id',
                            $customer)->update(['fl_tags' => json_encode($updatetags)]);
                    }

                }
            }
            $this->success('操作成功');
        }
        $ids = $ids ? $ids : $this->request->get("ids");
        $users = \app\admin\model\fastscrm\crm\Customer::where('id', 'in',
            $ids)->where('fl_tags is not null')->field('fl_tags')->select();
        $acitve = [];
        foreach ($users as $user) {
            if (empty($acitve)) {
                $acitve = json_decode($user->fl_tags);
            } else {
                $acitve = array_intersect($acitve, json_decode($user->fl_tags));
            }
        }
        $groups = Db::name('fastscrm_tag_group')->field('id,group_name')->select();
        foreach ($groups as &$group) {
            $tags = Db::name('fastscrm_tag')->where('group_id',
                $group['id'])->field('id,group_id,tag_id,name')->select();
            foreach ($tags as &$tag) {
                if (in_array($tag['tag_id'], $acitve)) {
                    $tag['active'] = true;
                } else {
                    $tag['active'] = false;
                }
            }
            unset($tag);
            $group['tags'] = $tags;
        }
        unset($group);
        $this->view->assign("groupList", $groups);
        $this->view->assign("customers", $ids);
        return $this->view->fetch();
    }

    /**
     * 删除客户标签
     */
    public function deltags($ids = "")
    {
        if ($this->request->isPost()) {
            $customers = $this->request->post("customers");
            $tags = $this->request->post("tags");
            $tags = explode(',', $tags);
            $customers = explode(',', $customers);
            $work = new WeWork('App');
            if (!empty($tags)) {
                foreach ($customers as $key => $customer) {
                    $user = \app\admin\model\fastscrm\crm\Customer::where('id',
                        $customer)->field('external_userid,fl_userid,fl_tags')->find();
                    $deltags = array_intersect($tags, json_decode($user->fl_tags));
                    $updatetags = array_values(array_diff(json_decode($user->fl_tags), $deltags));
                    if (!empty($deltags)) {
                        $param['userid'] = $user->fl_userid;
                        $param['external_userid'] = $user->external_userid;
                        $param['remove_tag'] = $deltags;
                        $work->delUsersTags($param);
                        \app\admin\model\fastscrm\crm\Customer::where('id',
                            $customer)->update(['fl_tags' => json_encode($updatetags)]);
                    }
                }
            }
            $this->success('操作成功');
        }
        $ids = $ids ? $ids : $this->request->get("ids");
        $users = \app\admin\model\fastscrm\crm\Customer::where('id', 'in', $ids)->column('fl_tags');
        $acitve = [];
        foreach ($users as $user) {
            $acitve = array_merge($acitve, json_decode($user));
        }
        $acitve = array_unique($acitve);
        $groups = Db::name('fastscrm_tag_group')->field('id,group_name')->select();
        foreach ($groups as &$group) {
            $tags = Db::name('fastscrm_tag')->where('group_id',
                $group['id'])->field('id,group_id,tag_id,name')->select();
            foreach ($tags as &$tag) {
                if (in_array($tag['tag_id'], $acitve)) {
                    $tag['active'] = true;
                } else {
                    $tag['active'] = false;
                }
            }
            unset($tag);
            $group['tags'] = $tags;
        }
        unset($group);
        $this->view->assign("groupList", $groups);
        $this->view->assign("customers", $ids);
        return $this->view->fetch();
    }

    /**
     * 同步企微客户
     */
    public function sync()
    {
        $job = new AddJob();
        $param['admin_id'] = $this->auth->isLogin() ? $this->auth->id : 0;
        $param['username'] = $this->auth->isLogin() ? $this->auth->username : __('Unknown');
        $param['task'] = 'customer';
        $param['ip'] = request()->ip();
        $job->add($param);
        $this->success('同步任务已挂起');
    }


}
