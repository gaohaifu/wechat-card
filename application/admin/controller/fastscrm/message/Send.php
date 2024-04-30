<?php

namespace app\admin\controller\fastscrm\message;

use app\common\controller\Backend;
use think\Db;
use fast\Date;

/**
 * 消息通知
 *
 * @icon fa fa-circle-o
 */
class Send extends Backend
{

    /**
     * Send模型对象
     * @var \app\admin\model\fastscrm\message\Send
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\message\Send;
        $this->view->assign("typeList", $this->model->getTypeList());
        $this->view->assign("mentionedTypeList", $this->model->getMentionedTypeList());
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
                ->with('template')
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
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isPost()) {
            $params = $this->request->post();
            $workers = [];
            $webhooks = explode(',', $params['webhooks']);
            if ($params['mentioned_type'] == '1') {
                $workers = array_values(array_unique(explode(',', $params['workers'])));
                $workers = array_slice($workers, 0, 1000);
            }
            if ($params['type'] == 2) {
                $params['fixedtime'] = strtotime($params['fixedtime']);
                $timespan = Date::span($params['fixedtime'], time(), 'minutes');
                if ($timespan < 5 || $params['fixedtime'] <= time()) {
                    $this->error('定时时间需在5分钟之后');
                }
            } else {
                $params['fixedtime'] = null;
            }
            unset($params['webhooks']);
            unset($params['workers']);
            $params['creater'] = $this->auth->isLogin() ? $this->auth->username : __('Unknown');
            $params['createtime'] = time();
            $id = $this->model->insert($params, false, true);
            foreach ($webhooks as $webhook) {
                $depart_temp['webhook_id'] = $webhook;
                $depart_temp['send_id'] = $id;
                $depart_temp['createtime'] = time();
                Db::name('fastscrm_webhook_send_webhooks')->insert($depart_temp);
            }
            foreach ($workers as $worker) {
                $worker_temp['worker_id'] = $worker;
                $worker_temp['send_id'] = $id;
                $worker_temp['createtime'] = time();
                Db::name('fastscrm_webhook_send_workers')->insert($worker_temp);
            }
            $this->success();
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $ids = $ids ? $ids : $this->request->request('id');
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post();
            if ($params) {
                $workers = [];
                $webhooks = explode(',', $params['webhooks']);
                if ($params['mentioned_type'] == '1') {
                    $workers = array_values(array_unique(explode(',', $params['workers'])));
                    $workers = array_slice($workers, 0, 1000);
                }
                if ($params['type'] == 2) {
                    $params['fixedtime'] = strtotime($params['fixedtime']);
                    $timespan = Date::span($params['fixedtime'], time(), 'minutes');
                    if ($timespan < 5 || $params['fixedtime'] <= time()) {
                        $this->error('定时时间需在5分钟之后');
                    }
                } else {
                    $params['fixedtime'] = null;
                }
                unset($params['webhooks']);
                unset($params['workers']);
                $params['updatetime'] = time();
                $this->model->where('id', $row->id)->update($params);
                Db::name('fastscrm_webhook_send_webhooks')->where('send_id', $row->id)->delete();
                foreach ($webhooks as $webhook) {
                    $depart_temp['webhook_id'] = $webhook;
                    $depart_temp['send_id'] = $row->id;
                    $depart_temp['createtime'] = time();
                    Db::name('fastscrm_webhook_send_webhooks')->insert($depart_temp);
                }
                Db::name('fastscrm_webhook_send_workers')->where('send_id', $row->id)->delete();
                foreach ($workers as $worker) {
                    $worker_temp['worker_id'] = $worker;
                    $worker_temp['send_id'] = $row->id;
                    $worker_temp['createtime'] = time();
                    Db::name('fastscrm_webhook_send_workers')->insert($worker_temp);
                }
                $this->success();
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $workers = Db::name('fastscrm_webhook_send_workers')->where('send_id', $row->id)->select();
        $workers_list = [];
        foreach ($workers as $worker) {
            $user = Db::name('fastscrm_worker')->where('userid', $worker['worker_id'])->find();
            if (!empty($user)) {
                $workers_list[] = $user;
            }
        }
        $webhooks = Db::name('fastscrm_webhook_send_webhooks')->where('send_id', $row->id)->select();
        $webhooks_list = [];
        foreach ($webhooks as $webhook) {
            $hook = Db::name('fastscrm_webhook')->where('id', $webhook['webhook_id'])->find();
            if (!empty($hook)) {
                $hook['webhookid'] = $hook['id'];
                $webhooks_list[] = $hook;
            }
        }
        $row->workers = $workers_list;
        $row->webhooks = $webhooks_list;
        $row->fixedtime_text = date('Y-m-d H:i:s', $row->fixedtime);
        $this->assignconfig("row", $row);
        return $this->view->fetch();
    }

    /**
     * 执行任务
     */
    public function execute($ids)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isAjax()) {
            $webhooks = Db::name('fastscrm_webhook_send_webhooks')->where('send_id', $row->id)->select();
            foreach ($webhooks as $webhook) {
                $hook = Db::name('fastscrm_webhook')->where('id', $webhook['webhook_id'])->find();
                if (!empty($hook)) {
                    $data['send_id'] = $row->id;
                    $data['webhook_id'] = $hook['id'];
                    $data['admin_id'] = $this->auth->isLogin() ? $this->auth->id : 0;
                    $data['username'] = $this->auth->isLogin() ? $this->auth->username : __('Unknown');
                    $data['ip'] = request()->ip();
                    $data['createtime'] = time();
                    Db::name('fastscrm_webhook_send_log')->insert($data);
                }
            }
            $time = 0;
            if ($row->type == 2) {
                $time = $row->fixedtime - time() > 0 ? $row->fixedtime - time() : 0;
            }
            $template = Db::name('fastscrm_message_template')->where('id', $row->template_id)->find();
            $json = json_decode($template['json'], true);
            switch ($template['msg_type']) {
                case 'text':
                    $job = '\addons\fastscrm\library\Job\Webhook@sendText';
                    break;
                case 'image':
                    $job = '\addons\fastscrm\library\Job\Webhook@sendImage';
                    $json['picurl'] = cdnurl($json['picurl'], true);
                    break;
                case 'news':
                    $job = '\addons\fastscrm\library\Job\Webhook@sendNews';
                    $json['picurl'] = cdnurl($json['picurl'], true);
                    break;
                case 'markdown':
                    $job = '\addons\fastscrm\library\Job\Webhook@sendMarkdown';
                    break;
                case 'file':
                    $job = '\addons\fastscrm\library\Job\Webhook@sendFile';
                    $json['filename'] = Db::name('attachment')->where('url',$json['file'])->value('filename');
                    $json['file'] = ROOT_PATH . DS . 'public' . DS . $json['file'];
                    break;
            }

            $params = array('send_id' => $row->id, 'mentioned_type' => $row->mentioned_type, 'json' =>$json);
            \think\Queue::later($time, $job, $params, 'ScrmQueue');
            $this->success('发生任务已挂起');
        }
    }
}
