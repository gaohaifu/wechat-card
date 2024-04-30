<?php

namespace app\admin\controller\fastscrm\report;

use app\admin\controller\fastscrm\Scrmbackend;
use think\Db;

/**
 * 群分析
 */
class Groupdata extends Scrmbackend
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
                $searchtime     = strtotime(date('Y-m-d', strtotime('-' . ($i) . ' day')));
                $searchenttime     = strtotime(date('Y-m-d 23:59:59', strtotime('-' . ($i) . ' day')));
                $chat_total[$k] = Db::name('fastscrm_group_data')->where($where)->where('stat_time',$searchtime)->sum('chat_total');
                $member_total[$k] = Db::name('fastscrm_group_data')->where($where)->where('stat_time',$searchtime)->sum('member_total');
                $new_member_cnt[$k] = Db::name('fastscrm_group_data')->where($where)->where('stat_time',$searchtime)->sum('new_member_cnt');
                $lose_total[$k]  = Db::name('fastscrm_group_user_lose')->where('createtime','>=',$searchtime)->where('createtime','<=',$searchenttime)->count();
                $k++;

            }
            $this->success('', '', array(
                'days' => $days,
                'chat_total' => $chat_total,
                'member_total' => $member_total,
                'new_member_cnt' => $new_member_cnt,
                'lose_total' => $lose_total
            ));
        } else {
            $day = 7;
            $k   = 0;
            for ($i = $day; $i > 0; $i--) {
                $days[]         = date('Y-m-d', strtotime('-' . ($i) . ' day'));
                $searchtime     = strtotime(date('Y-m-d', strtotime('-' . ($i) . ' day')));
                $searchenttime     = strtotime(date('Y-m-d 23:59:59', strtotime('-' . ($i) . ' day')));
                $chat_total[$k] = Db::name('fastscrm_group_data')->where('stat_time',$searchtime)->sum('chat_total');
                $member_total[$k] = Db::name('fastscrm_group_data')->where('stat_time',$searchtime)->sum('member_total');
                $new_member_cnt[$k] = Db::name('fastscrm_group_data')->where('stat_time',$searchtime)->sum('new_member_cnt');
                $lose_total[$k]  = Db::name('fastscrm_group_user_lose')->where('createtime','>=',$searchtime)->where('createtime','<=',$searchenttime)->count();
                $k++;
            }
            $starttime            = strtotime(date('Y-m-d', strtotime('-1 day')));
            $endtime            = strtotime(date('Y-m-d 23:59:59', strtotime('-1 day')));
            $yester['chat_total'] = Db::name('fastscrm_group_data')->where('stat_time',$starttime)->sum('chat_total');
            $yester['member_total'] = Db::name('fastscrm_group_data')->where('stat_time',$starttime)->sum('member_total');
            $yester['new_member_cnt']  = Db::name('fastscrm_group_data')->where('stat_time',$starttime)->sum('new_member_cnt');
            $yester['lose_total']  = Db::name('fastscrm_group_user_lose')->where('createtime','>=',$starttime)->where('createtime','<=',$endtime)->count();
            $owners = Db::name('fastscrm_group_chat')->column('owner');
            $owners = implode(',',$owners);
            $users = Db::name('fastscrm_worker')->where('userid','in',$owners)->field('id,name,userid')->select();
            $this->view->assign("yester", $yester);
            $this->view->assign("day", $day);
            $this->view->assign("users", $users);
            $this->assignconfig("days", $days);
            $this->assignconfig("chat_total", $chat_total);
            $this->assignconfig("member_total", $member_total);
            $this->assignconfig("new_member_cnt", $new_member_cnt);
            $this->assignconfig("lose_total", $lose_total);


            return $this->view->fetch();
        }

    }

    public function table1()
    {

        if ($this->request->isAjax()) {
            $total = $this->request->get("day");
            $userid = $this->request->get("userid");
            $where_user=[];
            if(!empty($userid)){
                $where_user['owner']=$userid;
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list=[];
            $k = 0;
            for ($i = $offset; $i < $total; $i++) {
                if($k+1>=$limit){
                    break;
                }
                $list[$k]['day']    = date('Y-m-d', strtotime('-' . ($i+1) . ' day'));
                $searchtime     = strtotime(date('Y-m-d', strtotime('-' . ($i+1) . ' day')));
                $searchenttime     = strtotime(date('Y-m-d 23:59:59', strtotime('-' . ($i+1) . ' day')));
                $list[$k]['chat_total'] = Db::name('fastscrm_group_data')->where($where_user)->where('stat_time',$searchtime)->sum('chat_total');
                $list[$k]['member_total'] = Db::name('fastscrm_group_data')->where($where_user)->where('stat_time',$searchtime)->sum('member_total');
                $list[$k]['new_member_cnt'] = Db::name('fastscrm_group_data')->where($where_user)->where('stat_time',$searchtime)->sum('new_member_cnt');
                $list[$k]['lose_total']  = Db::name('fastscrm_group_user_lose')->where('createtime','>=',$searchtime)->where('createtime','<=',$searchenttime)->count();
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
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $owners = Db::name('fastscrm_group_chat')->column('owner');
            $owners = implode(',',$owners);
            $total = $this->model
                ->where($where)
                ->where('userid','in',$owners)
                ->where($where_user)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->where('userid','in',$owners)
                ->where($where_user)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            $starttime     = strtotime(date('Y-m-d', strtotime('-'.$day.' day')));
            $endtime     = strtotime(date('Y-m-d 23:59:59', strtotime('-1 day')));
            foreach ($list as $row) {
                $row->chat_total = Db::name('fastscrm_group_data')->where('owner',$row->userid)->order('stat_time','desc')->limit(1)->column('chat_total');
                $row->member_total = Db::name('fastscrm_group_data')->where('owner',$row->userid)->order('stat_time','desc')->limit(1)->column('member_total');
                $row->new_member_cnt = Db::name('fastscrm_group_data')->where('owner',$row->userid)->where('stat_time','>=',$starttime)->where('stat_time', '<=', $endtime)->sum('new_member_cnt');

            }

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
    }

}
