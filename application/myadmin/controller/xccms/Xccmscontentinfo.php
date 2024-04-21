<?php

namespace app\myadmin\controller\xccms;

use addons\myadmin\library\Backend;
use think\Db;
use fast\Tree;
use app\admin\library\xccms\Service;
use app\admin\model\Xccmscontentcategory;
use app\admin\model\Xccmsconfig;

/**
 * 内容管理
 *
 * @icon fa fa-circle-o
 */
class Xccmscontentinfo extends Backend
{
    
    /**
     * Xccmscontentinfo模型对象
     * @var \app\admin\model\Xccmscontentinfo
     */
    protected $model = null;
    protected $categorylist = [];
    protected $categorydata = [];
    protected $is_show_tips = true;

    public function _initialize()
    {
        parent::_initialize();
        Service::check_xccms_init();
        $this->model = new \app\admin\model\Xccmscontentinfo;

        //内容分类树
        $treeCategory = Tree::instance();
        $treeCategory->init(collection(Xccmscontentcategory::field('id,name,parent_id')->where('state', 1)->order('name, id')->select())->toArray(), 'parent_id');
        $this->categorylist = $treeCategory->getTreeList($treeCategory->getTreeArray(0), 'name');
        $categorydata = [0 => ['type' => 'all', 'name' => __('None')]];
        foreach ($this->categorylist as $k => $v) {
            $categorydata[$v['id']] = $v;
        }
        $this->is_show_tips = Xccmsconfig::get_value('is_show_tips');

        $this->categorydata = $categorydata;
        $this->view->assign("categoryparentList", $categorydata);
        $this->view->assign('is_show_tips', $this->is_show_tips);
    }


    
    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if (false === $this->request->isAjax()) {
            $treeData = array(
                'id'=>'-1',
                'text'=>'根目录(全部)',
                'type'=>'folder',
                'state'=>array('opened'=>true, 'selected'=>true),
                'children'=>$this->getTree(0)
            );

            $this->view->assign('treeData', $treeData);
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
            $cid = input('cid', 0);
            $this->view->assign("categoryparentList", $this->rebind_categorydata($this->categorydata));
            $this->view->assign('cid', $cid);
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
            $this->view->assign("categoryparentList", $this->rebind_categorydata($this->categorydata));
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

    /**
     * 获取列表
     */
    public function getList()
    {
        $cid = input('cid', -1);
        $keywords = input('search', '');
        $filter = input('filter', '');


        $whereList = [];
        if ($filter != '{}')
        {
            $filterJson = json_decode($filter, true);
            if (isset($filterJson['title']))
            {
                $whereList = ['title'=>['like', "%".$filterJson['title']."%"]];
            }

            if (isset($filterJson['is_recommend']))
            {
                $whereList = ['is_recommend'=>$filterJson['is_recommend']];
            }
        }
        
        $whereList_category = $cid == -1 ? [] : ['category_id'=>$cid];
        
        [$where, $sort, $order, $offset, $limit] = $this->buildparams();
        
        $list = $this->model->field('id,category_id,title,is_recommend,list_image,visits,state,weigh,updatetime,updatedby')
            ->where($whereList)
            ->where($whereList_category)
            ->order('title, id desc')
            ->paginate($limit);
            $rows = $list->items();

        if (count($rows) > 0)
        {
            $fields = [
                [        
                    'field'=>'updatedby', //关联数据字段
                    'display'=>'updatedby_nickname',//附加的字段名称
                    'primary'=>'id', //关联表主键
                    'column'=>'nickname',//关联表中读取需要显示的字段
                    'model'=>'\app\admin\model\Admin',//关联模型
                    'table'=>''//关联表，关联表和关联模型二选一
                ],
                [        
                    'field'=>'category_id', //关联数据字段
                    'display'=>'category_name',//附加的字段名称
                    'primary'=>'id', //关联表主键
                    'column'=>'name',//关联表中读取需要显示的字段
                    'model'=>'\app\admin\model\Xccmscontentcategory',//关联模型
                    'table'=>''//关联表，关联表和关联模型二选一
                ],
            ];
            addtion($rows, $fields);
        }

        return json(array(
            'total'=>$list->total(),
            'rows'=>$rows
        ));
    }

    /**
     * Selectpage搜索
     *
     */
    public function selectpage()
    {
        return parent::selectpage();
    }

    ///////////////////////////////////////////////
    private function getTree($pid = 0)
    {
        $list = Xccmscontentcategory::field('id,name')
            ->where('company_id',COMPANY_ID)
            ->where('parent_id', $pid)
            ->where('state', 1)
            ->order('name, id')
            ->select();


        if (count($list) > 0)
        {
            foreach($list as $i=>$item)
            {
                $list[$i]['text'] = $item['name'];
                $list[$i]['type'] = 'folder';
                $list[$i]['state'] = array('opened'=>false);

                $sub_list = $this->getTree($item['id']);
                $list[$i]['children'] = $sub_list;
                if (count($sub_list) == 0)
                {
                    $list[$i]['type'] = 'file';
                }
                
                unset($list[$i]['title']);
            }
        }
        return $list;
    }

    private function rebind_categorydata($categorydata)
    {
        $arr = [];
        foreach($categorydata as $i=>$item)
        {
            if ($i > 0)
            {
                array_push($arr, $item);
            }
        }

        return $arr;
    }

}
