<?php

namespace app\myadmin\controller\fastscrm;

use app\common\controller\Backend;
use fast\Tree;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use Exception;

/**
 * 公共组件
 *
 * @icon fa fa-circle-o
 */
class Template extends Backend
{
    protected $noNeedRight = ['*'];
    /**
     * 客户标签
     */
    public function tags($ids = "")
    {
        $ids = explode(',', $ids);
        $groups = Db::name('fastscrm_tag_group')->field('id,group_name')->select();
        foreach ($groups as &$group) {
            $tags = Db::name('fastscrm_tag')->where('group_id',
                $group['id'])->field('id,group_id,tag_id,name')->select();
            foreach ($tags as &$tag) {
                if (in_array($tag['id'], $ids)) {
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
        return $this->view->fetch();
    }

    /**
     * 员工选择器
     */
    public function workers($ids = "")
    {
        if ($this->request->isAjax()) {
            $choseIds = explode(',', $this->request->post('choseids'));
            $model = new \app\admin\model\fastscrm\company\Depart;
            $ruleList = collection($model->field('id,name,parentid,depart_id')->order('parentid ASC,order DESC')->select())->toArray();
            $maxId = $model->order('id', 'DESC')->value('id');
            $treeList = [];
            $choses = [];
            $choseData = [];
            $total = 0;
            foreach ($ruleList as &$item) {
                $treeList[] = $item;
                $workers = collection(\app\admin\model\fastscrm\company\Worker::field('id,name,userid,avatar')->where('find_in_set(:depart_id,department)',
                    ['depart_id' => $item['depart_id']])->where('status', 1)->order('id', 'ASC')->select())->toArray();
                foreach ($workers as $worker) {
                    $maxId++;
                    $temp['id'] = $maxId;
                    $temp['workerid'] = $worker['id'];
                    $temp['name'] = $worker['name'];
                    $temp['parentid'] = $item['id'];
                    $temp['userid'] = $worker['userid'];
                    $temp['avatar'] = $worker['avatar'];
                    $temp['depart_id'] = $item['depart_id'];
                    $temp['depart_name'] = $item['name'];
                    $temp['childlist'] = [];
                    $treeList[] = $temp;
                    $total++;
                    if (in_array($maxId, $choseIds) || in_array($worker['id'], $choseIds)) {
                        $choses[] = $maxId;
                        $choseData[] = $temp;
                    }
                }
            }
            unset($item);
            Tree::instance()->init($treeList, 'parentid');
            $list = Tree::instance()->getTreeArray(0);
            $this->success('', '',
                array('list' => $list, 'total' => $total, 'choses' => $choses, 'chosedata' => $choseData));
        }
        $this->assignconfig("choseids", $ids);
        return $this->view->fetch();
    }

    /**
     * 部门选择器
     */
    public function departs($ids = "")
    {
        if ($this->request->isAjax()) {
            $choseIds = explode(',', $this->request->post('choseids'));
            $model = new \app\admin\model\fastscrm\company\Depart;
            $ruleList = collection($model->field('id,name,parentid,depart_id')->order('parentid ASC,order DESC')->select())->toArray();
            $choses = [];
            $choseData = [];
            foreach ($ruleList as &$item) {
                if (in_array($item['id'], $choseIds)) {
                    $choses[] = $item['id'];
                    $choseData[] = $item;
                }
            }
            unset($item);
            Tree::instance()->init($ruleList, 'parentid');
            $list = Tree::instance()->getTreeArray(0);
            $this->success('', '',
                array('list' => $list, 'total' => count($ruleList), 'choses' => $choses, 'chosedata' => $choseData));
        }
        $this->assignconfig("choseids", $ids);
        return $this->view->fetch();
    }

    /**
     * 机器人选择器
     */
    public function webhooks($ids = "")
    {
        if ($this->request->isAjax()) {
            $choseIds = explode(',', $this->request->post('choseids'));
            $model = new \app\admin\model\fastscrm\message\Webhookgroup();
            $ruleList = collection($model->select())->toArray();
            $maxId = $model->order('id', 'DESC')->value('id');
            $treeList = [];
            $choses = [];
            $choseData = [];
            $total = 0;
            foreach ($ruleList as &$item) {
                $item['parentid'] = 0;
                $treeList[] = $item;
                $webhooks = collection(\app\admin\model\fastscrm\message\Webhook::where('group_id',
                    $item['id'])->where('status', 1)->order('id', 'ASC')->select())->toArray();
                foreach ($webhooks as $webhook) {
                    $maxId++;
                    $temp['id'] = $maxId;
                    $temp['webhookid'] = $webhook['id'];
                    $temp['name'] = $webhook['name'];
                    $temp['group_id'] = $webhook['group_id'];
                    $temp['parentid'] = $item['id'];
                    $temp['childlist'] = [];
                    $treeList[] = $temp;
                    $total++;
                    if (in_array($maxId, $choseIds) || in_array($webhook['id'], $choseIds)) {
                        $choses[] = $maxId;
                        $choseData[] = $temp;
                    }
                }
            }
            unset($item);
            Tree::instance()->init($treeList, 'parentid');
            $list = Tree::instance()->getTreeArray(0);
            $this->success('', '',
                array('list' => $list, 'total' => $total, 'choses' => $choses, 'chosedata' => $choseData));
        }
        $this->assignconfig("choseids", $ids);
        return $this->view->fetch();
    }

    /**
     * 离职员工选择器
     */
    public function delWorkers($ids = "")
    {
        if ($this->request->isAjax()) {
            $choseIds = explode(',', $this->request->post('choseids'));
            $model = new \app\admin\model\fastscrm\company\Depart;
            $ruleList = collection($model->field('id,name,parentid,depart_id')->order('parentid ASC,order DESC')->select())->toArray();
            $maxId = $model->order('id', 'DESC')->value('id');
            $treeList = [];
            $choses = [];
            $choseData = [];
            $total = 0;
            foreach ($ruleList as &$item) {
                $treeList[] = $item;
                $workers = Db::name('fastscrm_worker_delete')->field('id,name,userid,avatar')->where('find_in_set(:depart_id,department)',
                    ['depart_id' => $item['depart_id']])->where('status', 1)->order('id', 'ASC')->select();
                foreach ($workers as $worker) {
                    $maxId++;
                    $temp['id'] = $maxId;
                    $temp['workerid'] = $worker['id'];
                    $temp['name'] = $worker['name'];
                    $temp['parentid'] = $item['id'];
                    $temp['userid'] = $worker['userid'];
                    $temp['avatar'] = $worker['avatar'];
                    $temp['depart_id'] = $item['depart_id'];
                    $temp['depart_name'] = $item['name'];
                    $temp['childlist'] = [];
                    $treeList[] = $temp;
                    $total++;
                    if (in_array($maxId, $choseIds) || in_array($worker['id'], $choseIds)) {
                        $choses[] = $maxId;
                        $choseData[] = $temp;
                    }
                }
            }
            unset($item);
            Tree::instance()->init($treeList, 'parentid');
            $list = Tree::instance()->getTreeArray(0);
            $this->success('', '',
                array('list' => $list, 'total' => $total, 'choses' => $choses, 'chosedata' => $choseData));
        }
        $this->assignconfig("choseids", $ids);
        return $this->view->fetch();
    }

    /**
     * 客户选择器
     */
    public function customer($tags = "",$fl_userid="")
    {
        if($this->request->isAjax()){
            $fl_userid =  $this->request->get('fl_userid');
            $param =  $this->request->get('tags');
            $model = new \app\admin\model\fastscrm\crm\Customer;
            [$where, $sort, $order, $offset, $limit] = $this->buildparams();
            $where_tags = [];
            if($param){
                $tags = explode(',',$param);
                if(count($tags) === 1){
                    $where_tags['fl_tags'] = ['like','%'.$param.'%'];
                }else{
                    foreach($tags as $k => $v) {
                        $where_tags['fl_tags'][] = ['like','%'.$v.'%'];
                    }
                    $where_tags['fl_tags'][] = 'or';
                }
            }
            $list = $model
                ->where($where)
                ->where($where_tags)
                ->where('fl_userid',$fl_userid)
                ->paginate($limit);
            foreach ($list as &$row) {
                $handover = Db::name('fastscrm_worker')->where('userid',$row->fl_userid)->find();
                if(empty($handover)){
                    $handover = Db::name('fastscrm_worker_delete')->where('userid',$row->fl_userid)->find();
                }
                $departs = Db::name('fastscrm_depart')->where('depart_id','in',$handover['department'])->column('name');
                $row->handover_name = $handover['name'];
                $row->handover_department = $departs;
                $row->resigned_time = date('Y-m-d H:i:s', $handover['createtime']);
            }
            unset($row);
            $result = ['total' => $list->total(), 'rows' => $list->items()];
            $this->success('客户列表', null, $result);
        }
        $this->assignconfig("tags", $tags);
        $this->assignconfig("fl_userid", $fl_userid);
        return $this->view->fetch();
    }

    /**
     * 群聊选择器
     */
    public function chat($fl_userid="")
    {
        if($this->request->isAjax()){
            $fl_userid =  $this->request->get('fl_userid');
            $model = new \app\admin\model\fastscrm\crm\Groupchat;
            [$where, $sort, $order, $offset, $limit] = $this->buildparams();
            $list = $model
                ->where($where)
                ->where('owner',$fl_userid)
                ->paginate($limit);
            foreach ($list as &$row) {
                $row->member_total =   \app\admin\model\fastscrm\crm\Groupuser::where('group_id',$row->id)->count();
                $row->createtime = date('Y-m-d H:i:s', $row->createtime);
            }
            unset($row);
            $result = ['total' => $list->total(), 'rows' => $list->items()];
            $this->success('群列表', null, $result);
        }
        $this->assignconfig("fl_userid", $fl_userid);
        return $this->view->fetch();
    }
}
