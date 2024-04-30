<?php

namespace app\myadmin\controller\fastscrm\report;

use app\myadmin\controller\fastscrm\Scrmbackend;
use think\Db;

/**
 * 客户会话统计
 */
class Customerdata extends Scrmbackend
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
                $chat_cnt[$k] = Db::name('fastscrm_customer_data')->where('stat_time',$searchtime)->sum('chat_cnt');
                $message_cnt[$k] = Db::name('fastscrm_customer_data')->where('stat_time',$searchtime)->sum('message_cnt');
                $k++;
            }
            $this->success('', '', array(
                'days' => $days,
                'chat_cnt' => $chat_cnt,
                'message_cnt' => $message_cnt,
            ));
        } else {
            $day = 7;
            $k   = 0;
            for ($i = $day; $i > 0; $i--) {
                $days[]         = date('Y-m-d', strtotime('-' . ($i) . ' day'));
                $searchtime     = strtotime(date('Y-m-d', strtotime('-' . ($i) . ' day')));
                $chat_cnt[$k] = Db::name('fastscrm_customer_data')->where('stat_time',$searchtime)->sum('chat_cnt');
                $message_cnt[$k] = Db::name('fastscrm_customer_data')->where('stat_time',$searchtime)->sum('message_cnt');
                $k++;
            }
            $starttime            = strtotime(date('Y-m-d', strtotime('-1 day')));
            $yester['chat_cnt'] = Db::name('fastscrm_customer_data')->where('stat_time',$starttime)->sum('chat_cnt');
            $yester['message_cnt'] = Db::name('fastscrm_customer_data')->where('stat_time',$starttime)->sum('message_cnt');
            $yester['reply_percentage']  = Db::name('fastscrm_customer_data')->where('stat_time',$starttime)->sum('reply_percentage');
            $yester['avg_reply_time']  = Db::name('fastscrm_customer_data')->where('stat_time',$starttime)->sum('avg_reply_time');
            $users = Db::name('fastscrm_worker')->where('status','1')->field('id,name,userid')->select();
            $this->view->assign("yester", $yester);
            $this->view->assign("day", $day);
            $this->view->assign("users", $users);
            $this->assignconfig("days", $days);
            $this->assignconfig("chat_cnt", $chat_cnt);
            $this->assignconfig("message_cnt", $message_cnt);

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
                $searchtime     = strtotime(date('Y-m-d', strtotime('-' . ($i+1) . ' day')));
                $list[$k]['chat_cnt'] = Db::name('fastscrm_customer_data')->where($where_user)->where('stat_time',$searchtime)->sum('chat_cnt');
                $list[$k]['message_cnt'] = Db::name('fastscrm_customer_data')->where($where_user)->where('stat_time',$searchtime)->sum('message_cnt');
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
                $row->chat_cnt = Db::name('fastscrm_customer_data')->where('userid',$row->userid)->where('stat_time','>=',$starttime)->where('stat_time', '<=', $endtime)->sum('chat_cnt');
                $row->message_cnt = Db::name('fastscrm_customer_data')->where('userid',$row->userid)->where('stat_time','>=',$starttime)->where('stat_time', '<=', $endtime)->sum('message_cnt');
            }

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
    }

}
