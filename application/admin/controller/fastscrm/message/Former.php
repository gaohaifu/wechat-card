<?php

namespace app\admin\controller\fastscrm\message;

use addons\fastscrm\library\WeWork;
use app\common\controller\Backend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 消息模版
 *
 * @icon fa fa-circle-o
 */
class Former extends Backend
{

    /**
     * Template模型对象
     * @var \app\admin\model\fastscrm\message\Former
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\message\Former;
        $this->view->assign("msgTypeList", $this->model->getMsgTypeList());
        $this->view->assign("statusList", $this->model->getStatusList());
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params    = $this->request->post();
            $data['name'] = $params['name'];
            $data['status'] = $params['status'];
            $data['msg_type'] = $params['msg_type'];
            $data['createtime'] = time();
            $json = [];
            switch ($params['msg_type']){
                case 'text':
                    $json['text'] = $params['text'];
                    break;
                case 'image':
                    $json['picurl'] = $params['picurl'];
                    break;
                case 'news':
                    $json['title'] = $params['title'];
                    $json['description'] = $params['description'];
                    $json['url'] = $params['url'];
                    $json['picurl'] = $params['picurl'];
                    break;
                case 'file':
                    $json['file'] = $params['file'];
                    break;
                case 'markdown':
                    $json['markdown'] = $params['markdown'];
                    break;
            }
            $data['json'] = json_encode($json,JSON_UNESCAPED_UNICODE);
            $this->model->insert($data);
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
                $params    = $this->request->post();
                $data['name'] = $params['name'];
                $data['status'] = $params['status'];
                $data['msg_type'] = $params['msg_type'];
                $data['updatetime'] = time();
                $json = [];
                switch ($params['msg_type']){
                    case 'text':
                        $json['text'] = $params['text'];
                        break;
                    case 'image':
                        $json['picurl'] = $params['picurl'];
                        break;
                    case 'news':
                        $json['title'] = $params['title'];
                        $json['description'] = $params['description'];
                        $json['url'] = $params['url'];
                        $json['picurl'] = $params['picurl'];
                        break;
                    case 'file':
                        $json['file'] = $params['file'];
                        break;
                    case 'markdown':
                        $json['markdown'] = $params['markdown'];
                        break;
                }
                $data['json'] = json_encode($json,JSON_UNESCAPED_UNICODE);
                $this->model->where('id', $params['id'])->update($data);
                $this->success();
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $row->json = json_decode($row->json,JSON_UNESCAPED_UNICODE);
        $this->assignconfig("row", $row);
        return $this->view->fetch();
    }

    public function searchfind()
    {
        $departs  = collection($this->model->where('status','1')->select())->toArray();
        $searchlist = [];
        foreach ($departs as $key => $value) {
            $searchlist[] = ['id' => $value['id'], 'name' => $value['name']];
        }
        $data = ['searchlist' => $searchlist];
        $this->success('', null, $data);
    }
}
