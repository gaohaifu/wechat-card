<?php

namespace app\admin\controller\fastscrm\report;

use app\admin\controller\fastscrm\Scrmbackend;
use think\Db;

/**
 * 客户分析
 */
class Customer extends Scrmbackend
{
    protected $model = null;

    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $day = $this->request->post("day");
            $userid = $this->request->post("userid");
            $where=[];
            if(!empty($userid)){
                $where['owner']=$userid;
            }
            $k   = 0;
            for ($i = $day; $i > 0; $i--) {
                $days[]         = date('Y-m-d', strtotime('-' . ($i) . ' day'));
                $searchtime     = strtotime(date('Y-m-d 23:59:59', strtotime('-' . ($i) . ' day')));
                $starttime     = strtotime(date('Y-m-d', strtotime('-' . ($i) . ' day')));
                $user_total[$k] = Db::name('fastscrm_customer')->where('fl_createtime','<=',$searchtime)->count();
                $new_contact_cnt[$k] = Db::name('fastscrm_customer_data')->where('stat_time',$starttime)->sum('new_contact_cnt');
                $worker_del[$k] = Db::name('fastscrm_customer_lose')->where('createtime','>=',$starttime)->where('createtime','<=',$searchtime)->where('del_type','0')->count();
                $user_del[$k] = Db::name('fastscrm_customer_lose')->where('createtime','>=',$starttime)->where('createtime','<=',$searchtime)->where('del_type','1')->count();
                $k++;
            }
            $this->success('', '', array(
                'days' => $days,
                'user_total' => $user_total,
                'new_contact_cnt' => $new_contact_cnt,
                'worker_del' => $worker_del,
                'user_del' => $user_del,
            ));
        } else {
            $day = 7;
            $k   = 0;
            for ($i = $day; $i > 0; $i--) {
                $days[]         = date('Y-m-d', strtotime('-' . ($i) . ' day'));
                $searchtime     = strtotime(date('Y-m-d 23:59:59', strtotime('-' . ($i) . ' day')));
                $starttime     = strtotime(date('Y-m-d', strtotime('-' . ($i) . ' day')));
                $user_total[$k] = Db::name('fastscrm_customer')->where('fl_createtime','<=',$searchtime)->count();
                $new_contact_cnt[$k] = Db::name('fastscrm_customer_data')->where('stat_time',$starttime)->sum('new_contact_cnt');
                $worker_del[$k] = Db::name('fastscrm_customer_lose')->where('createtime','>=',$starttime)->where('createtime','<=',$searchtime)->where('del_type','0')->count();
                $user_del[$k] = Db::name('fastscrm_customer_lose')->where('createtime','>=',$starttime)->where('createtime','<=',$searchtime)->where('del_type','1')->count();
                $k++;
            }
            $searchtime           = strtotime(date('Y-m-d 23:59:59', strtotime('-1 day')));
            $starttime            = strtotime(date('Y-m-d', strtotime('-1 day')));
            $yester['user_total'] = Db::name('fastscrm_customer')->where('fl_createtime','<=',$searchtime)->count();
            $yester['new_contact_cnt'] = Db::name('fastscrm_customer_data')->where('stat_time',$starttime)->sum('new_contact_cnt');
            $yester['worker_del'] = Db::name('fastscrm_customer_lose')->where('createtime','>=',$starttime)->where('createtime','<=',$searchtime)->where('del_type','0')->count();
            $yester['user_del'] = Db::name('fastscrm_customer_lose')->where('createtime','>=',$starttime)->where('createtime','<=',$searchtime)->where('del_type','1')->count();

            $users = Db::name('fastscrm_worker')->where('status','1')->field('id,name,userid')->select();
            $this->view->assign("yester", $yester);
            $this->view->assign("day", $day);
            $this->view->assign("users", $users);
            $this->assignconfig("days", $days);
            $this->assignconfig("user_total", $user_total);
            $this->assignconfig("new_contact_cnt", $new_contact_cnt);
            $this->assignconfig("worker_del", $worker_del);
            $this->assignconfig("user_del", $user_del);

            return $this->view->fetch();
        }

    }

    public function table1()
    {

        if ($this->request->isAjax()) {
            $total = $this->request->get("day");
            $userid = $this->request->get("userid");
            $where_user=[];
            $where_fl_user=[];
            if(!empty($userid)){
                $where_fl_user['fl_userid']=$userid;
                $where_user['userid']=$userid;
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list=[];
            $k = 0;
            for ($i = $offset; $i < $total; $i++) {
                if($k+1>=$limit){
                    break;
                }
                $list[$k]['day']    = date('Y-m-d', strtotime('-' . ($i+1) . ' day'));
                $searchtime     = strtotime(date('Y-m-d 23:59:59', strtotime('-' . ($i) . ' day')));
                $starttime     = strtotime(date('Y-m-d', strtotime('-' . ($i) . ' day')));
                $list[$k]['user_total'] = Db::name('fastscrm_customer')->where($where_fl_user)->where('fl_createtime','<=',$searchtime)->count();
                $list[$k]['new_contact_cnt'] = Db::name('fastscrm_customer_data')->where($where_user)->where('stat_time',$starttime)->sum('new_contact_cnt');
                $list[$k]['worker_del'] = Db::name('fastscrm_customer_lose')->where($where_fl_user)->where('createtime','>=',$starttime)->where('createtime','<=',$searchtime)->where('del_type','0')->count();
                $list[$k]['user_del'] = Db::name('fastscrm_customer_lose')->where($where_fl_user)->where('createtime','>=',$starttime)->where('createtime','<=',$searchtime)->where('del_type','1')->count();
                $k++;
            }

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
    }

    public function table2()
    {
        $this->model = new \app\admin\model\fastscrm\company\Worker();
        if ($this->request->isAjax()) {
            $day = $this->request->get("day");
            $userid = $this->request->get("userid");
            $where_user=[];
            if(!empty($userid)){
                $where_user['userid']=$userid;
            }else{
                $where_user['status']='1';
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->where($where_user)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->where($where_user)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            $starttime     = strtotime(date('Y-m-d', strtotime('-'.$day.' day')));
            $endtime     = strtotime(date('Y-m-d 23:59:59', strtotime('-1 day')));
            foreach ($list as $row) {
                $row->user_total = Db::name('fastscrm_customer')->where('fl_userid',$row->userid)->where('fl_createtime','<=',$endtime)->count();
                $row->new_contact_cnt = Db::name('fastscrm_customer_data')->where('userid',$row->userid)->where('stat_time',$starttime)->sum('new_contact_cnt');
                $row->worker_del = Db::name('fastscrm_customer_lose')->where('fl_userid',$row->userid)->where('createtime','>=',$starttime)->where('createtime','<=',$endtime)->where('del_type','0')->count();
                $row->user_del = Db::name('fastscrm_customer_lose')->where('fl_userid',$row->userid)->where('createtime','>=',$starttime)->where('createtime','<=',$endtime)->where('del_type','1')->count();
            }

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
    }

}
