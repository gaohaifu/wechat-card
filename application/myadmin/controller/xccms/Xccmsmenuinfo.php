<?php

namespace app\myadmin\controller\xccms;

use addons\myadmin\library\Backend;
use think\Db;
use app\admin\library\xccms\Service;
use fast\Tree;
use app\admin\model\Xccmscontentcategory;
use app\admin\model\Xccmsproductcategory;
use app\admin\model\Xccmspageinfo;
use app\admin\model\Xccmsnewsinfo;
use app\admin\model\Xccmsconfig;

/**
 * 菜单管理
 *
 * @icon fa fa-circle-o
 */
class Xccmsmenuinfo extends Backend
{
    
    /**
     * Xccmsmenuinfo模型对象
     * @var \app\admin\model\Xccmsmenuinfo
     */
    protected $model = null;
    protected $categorylist = [];
    protected $categorydata = [];
    protected $is_show_tips = true;

    public function _initialize()
    {
        parent::_initialize();
        Service::check_xccms_init();
        $this->model = new \app\admin\model\Xccmsmenuinfo;
        [$categorylist,$categorydata] = $this->model->get_category_tree();

        $this->is_show_tips = Xccmsconfig::get_value('is_show_tips');
        $this->categorylist = $categorylist;
        $this->categorydata = $categorydata;
        $this->view->assign("parentList", $categorydata);
        $this->view->assign('is_show_tips', $this->is_show_tips);
    }


    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            $search = $this->request->request("search");
            $type = $this->request->request("type");

            //构造父类select列表选项数据
            $list = [];

            foreach ($this->categorylist as $k => $v) {
                $this->categorylist[$k]['updatedby_nickname'] = Service::get_admin_nickname($v['updatedby']);
                if ($search) {
                    if ($v['type'] == $type && stripos($v['name'], $search) !== false || stripos($v['nickname'], $search) !== false) {
                        if ($type == "all" || $type == null) {
                            $list = $this->categorylist;
                        } else {
                            $list[] = $v;
                        }
                    }
                } else {
                    if ($type == "all" || $type == null) {
                        $list = $this->categorylist;
                    } elseif ($v['type'] == $type) {
                        $list[] = $v;
                    }
                }
            }

            foreach($list as $i=>$item)
            {
                $list[$i]['menu_object_name'] = null;
                switch($item['menu_type'])
                {
                    case 'product':
                        $list[$i]['menu_object_name'] = Xccmsproductcategory::get_name($item['menu_object_id']);
                        break;
                    case 'content':
                        $list[$i]['menu_object_name'] = Xccmscontentcategory::get_name($item['menu_object_id']);
                        break;
                    case 'page':
                        $list[$i]['menu_object_name'] = Xccmspageinfo::get_name($item['menu_object_id']);
                        break;
                }
            }

            $total = count($list);
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {
        if (false === $this->request->isPost()) {
            $categorydata = $this->categorydata;
            $categorydata_new = [];
            foreach($categorydata as $i=>$item)
            {
                if (!isset($item['parent_id']) || $item['parent_id'] == 0)
                {
                    array_push($categorydata_new, $item);
                }
            }
            $this->view->assign("parentList", $categorydata_new);

            $this->view->assign("product_category_list", $this->bind_product_category());
            $this->view->assign("content_category_list", $this->bind_content_category());
            $this->view->assign("page_list", $this->bind_page());
            $this->view->assign("news_list", $this->bind_news());

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

        switch($params['menu_type'])
        {
            case 'index':
            case 'partner':
            case 'job':
            case 'link':
            case 'aboutus':
            case 'contactus':
                $params['menu_object_id'] = null;
                break;
            case 'product':
            case 'content':
            case 'page':
                $params['url'] = '';
                break;
        }

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
            $categorydata = $this->categorydata;
            $categorydata_new = [];
            foreach($categorydata as $i=>$item)
            {
                if (!isset($item['parent_id']) || $item['parent_id'] == 0)
                {
                    array_push($categorydata_new, $item);
                }
            }
            $this->view->assign("parentList", $categorydata_new);

            $this->view->assign("product_category_list", $this->bind_product_category());
            $this->view->assign("content_category_list", $this->bind_content_category());
            $this->view->assign("page_list", $this->bind_page());
            $this->view->assign("news_list", $this->bind_news());
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
        switch($params['menu_type'])
        {
            case 'index':
            case 'partner':
            case 'job':
            case 'link':
            case 'aboutus':
            case 'contactus':
                $params['menu_object_id'] = null;
                break;
            case 'product':
            case 'content':
            case 'page':
                $params['url'] = '';
                break;
        }

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


    private function bind_product_category()
    {
        $this->model = new \app\admin\model\Xccmsproductcategory;
        [$categorylist,$categorydata] = $this->model->get_category_tree();
        $categorydata = [];
        foreach ($categorylist as $k => $v) {
            // $categorydata[$v['id']] = $v;
            array_push($categorydata, $v);
        }

        return $categorydata;
    }

    private function bind_content_category()
    {
        $this->model = new \app\admin\model\Xccmscontentcategory;
        [$categorylist,$categorydata] = $this->model->get_category_tree();
        $categorydata = [];
        foreach ($categorylist as $k => $v) {
            // $categorydata[$v['id']] = $v;
            array_push($categorydata, $v);
        }

        return $categorydata;
    }

    private function bind_page()
    {
        $this->model = new \app\admin\model\Xccmspageinfo;
        $categorylist = $this->model->field('id,title as name')->where('state', 1)->order('id desc')->select();
        $categorydata = [];
        foreach ($categorylist as $k => $v) {
            $v['parent_id'] = 0;
            $v['spacer'] = '';
            $v['haschild'] = 0;
            $categorydata[$v['id']] = $v;
        }

        return $categorydata;
    }

    private function bind_news()
    {
        $this->model = new \app\admin\model\Xccmsnewsinfo;
        $categorylist = $this->model->field('id,title as name')->where('state', 1)->order('id desc')->select();
        $categorydata = [];
        foreach ($categorylist as $k => $v) {
            $v['parent_id'] = 0;
            $v['spacer'] = '';
            $v['haschild'] = 0;
            $categorydata[$v['id']] = $v;
        }

        return $categorydata;
    }
}
