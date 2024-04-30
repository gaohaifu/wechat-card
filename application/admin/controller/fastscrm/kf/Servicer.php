<?php

namespace app\myadmin\controller\fastscrm\kf;

use addons\fastscrm\library\Job\AddJob;
use addons\fastscrm\library\WeWork;
use app\common\controller\Backend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 接待人员
 *
 * @icon fa fa-circle-o
 */
class Servicer extends Backend
{

    /**
     * Servicer模型对象
     * @var \app\admin\model\fastscrm\kf\Servicer
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\kf\Servicer;

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
                ->with(['account', 'worker'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

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
            $params = $this->request->post();
            if ($params) {
                $workers = array_values(array_unique(explode(',', $params['workers'])));
                $workers = array_slice($workers, 0, 100);
                $work = new WeWork('App');
                try {
                    $result = $work->servicerAdd(['open_kfid' => $params['open_kfid'], 'userid_list' => $workers]);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
                $temp['open_kfid'] = $params['open_kfid'];
                $temp['createtime'] = time();
                $temp['updatetime'] = time();
                foreach ($result['result_list'] as $item) {
                    if ($item['errmsg'] == 'success') {
                        $temp['worker_id'] = $item['userid'];
                        $this->model->insert($temp);
                    }
                }
                $this->success();
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
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
            $pk = $this->model->getPk();
            $adminIds = $this->getDataLimitAdminIds();
            if (is_array($adminIds)) {
                $this->model->where($this->dataLimitField, 'in', $adminIds);
            }
            $list = collection($this->model->where($pk, 'in', $ids)->field('open_kfid,worker_id')->select())->toArray();
            if (count($list) > 100) {
                $this->error('单次删除不能超过100条');
            }
            $work = new WeWork('App');
            $temps = [];
            $results = [];
            foreach ($list as $row) {
                $temps[$row['open_kfid']][] = $row['worker_id'];
            }
            foreach ($temps as $key => $item) {
                $results[] = $work->servicerDel(['open_kfid' => $key, 'userid_list' => $item]);
            }
            $del_userids = [];
            foreach ($results as $result) {
                if($result){
                    foreach ($result['result_list'] as $user) {
                        $del_userids[] = $user['userid'];
                    }
                }
            }
            if(!empty($del_userids)){
                $del_userids = implode(',',$del_userids);
                $this->model->where('worker_id','in',$del_userids)->where('id',$ids)->delete();
            }
            $this->success();
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }

    /**
     * 同步客服
     */
    public function sync()
    {
        $job = new AddJob();
        $param['admin_id'] = $this->auth->isLogin() ? $this->auth->id : 0;
        $param['username'] = $this->auth->isLogin() ? $this->auth->username : __('Unknown');
        $param['task'] = 'servicer';
        $param['ip'] = request()->ip();
        $job->add($param);
        $this->success('同步任务已挂起');
    }
}
