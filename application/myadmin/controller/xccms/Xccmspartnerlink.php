<?php

namespace app\myadmin\controller\xccms;

use addons\myadmin\library\Backend;
use think\Db;
use app\admin\library\xccms\Service;
/**
 * 合作伙伴管理
 *
 * @icon fa fa-circle-o
 */
class Xccmspartnerlink extends Backend
{
    
    /**
     * Xccmspartnerlink模型对象
     * @var \app\admin\model\Xccmspartnerlink
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        Service::check_xccms_init();
        $this->model = new \app\admin\model\Xccmspartnerlink;

    }


    
    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if (false === $this->request->isAjax()) {
            return $this->view->fetch();
        }
        //如果发送的来源是 Selectpage，则转发到 Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        [$where, $sort, $order, $offset, $limit] = $this->buildparams();
        $fields = [
            [        
                'field'=>'updatedby', //关联数据字段
                'display'=>'updatedby_nickname',//附加的字段名称
                'primary'=>'id', //关联表主键
                'column'=>'nickname',//关联表中读取需要显示的字段
                'model'=>'\app\admin\model\Admin',//关联模型
                'table'=>''//关联表，关联表和关联模型二选一
            ]
        ];
        $list = $this->model
            ->where($where)
            ->order($sort, $order)
            ->paginate($limit);
            $rows = $list->items();
        if (count($rows) > 0)
        {
            addtion($rows, $fields);
        }

        $result = array("total" => $list->total(), "rows" => $rows);
        return json($result);
    }

    /**
     * 添加
     */
    public function add()
    {
        if (false === $this->request->isPost()) {
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);
        $params['creator'] = $this->auth->id;
        $params['updatetime'] = time();
        $params['updatedby'] = $this->auth->id;
        $params['company_id'] = COMPANY_ID;

        if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
            $params[$this->dataLimitField] = $this->auth->id;
        }
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                $this->model->validateFailException()->validate($validate);
            }
            $result = $this->model->allowField(true)->save($params);
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($result === false) {
            $this->error(__('No rows were inserted'));
        }
        $this->success();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
        }
        if (false === $this->request->isPost()) {
            $this->view->assign('row', $row);
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);
        $params['updatetime'] = time();
        $params['updatedby'] = $this->auth->id;

        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                $row->validateFailException()->validate($validate);
            }
            $result = $row->allowField(true)->save($params);
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if (false === $result) {
            $this->error(__('No rows were updated'));
        }
        $this->success();
    }
}
