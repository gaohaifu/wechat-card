<?php

namespace app\myadmin\controller\xccms;

use addons\myadmin\library\Backend;
use think\Db;
use app\admin\library\xccms\Service;
use app\admin\model\Xccmsnewsinfo;
use app\admin\model\Xccmscontentinfo;
use app\admin\model\Xccmsproductinfo;
use app\admin\model\Xccmspageinfo;
use app\admin\model\Xccmsjobinfo;

/**
 * 留言板管理
 *
 * @icon fa fa-circle-o
 */
class Xccmsguestbook extends Backend
{
    
    /**
     * Xccmsguestbook模型对象
     * @var \app\admin\model\Xccmsguestbook
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        Service::check_xccms_init();
        $this->model = new \app\admin\model\Xccmsguestbook;

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
        $list = $this->model
            ->where($where)
            ->order($sort, $order)
            ->paginate($limit);
        $rows = $list->items();
        foreach($rows as $i=>$item)
        {
            $rows[$i]['resource_title'] = '';
            switch($item['guest_book_type'])
            {
                case 'product':
                    $rows[$i]['resource_title'] = Xccmsproductinfo::get_title($item['resource_id']);
                    break;
                case 'content':
                    $rows[$i]['resource_title'] = Xccmscontentinfo::get_title($item['resource_id']);
                    break;
                case 'page':
                    $rows[$i]['resource_title'] = Xccmspageinfo::get_title($item['resource_id']);
                    break;
                case 'news':
                    $rows[$i]['resource_title'] = Xccmsnewsinfo::get_title($item['resource_id']);
                    break;
                case 'job':
                    $rows[$i]['resource_title'] = Xccmsjobinfo::get_name($item['resource_id']);
                    break;
            }
        }

        $result = array("total" => $list->total(), "rows" => $rows);
        return json($result);
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
            $row['guest_book_type_name'] = '-';
            $row['resource_title'] = '';
            $row['resource_url'] = '';
            switch($row['guest_book_type'])
            {
                case 'index':
                    $row['guest_book_type_name'] = '首页';
                    break;
                case 'product':
                    $row['guest_book_type_name'] = '产品';
                    $row['resource_title'] = Xccmsproductinfo::get_title($row['resource_id']);
                    $row['resource_url'] = addon_url('xccms/index/product_detail', [':id'=>$row['resource_id']]);
                    break;
                case 'content':
                    $row['guest_book_type_name'] = '内容';
                    $row['resource_title'] = Xccmscontentinfo::get_title($row['resource_id']);
                    $row['resource_url'] = addon_url('xccms/index/info_detail', [':id'=>$row['resource_id']]);
                    break;
                case 'page':
                    $row['guest_book_type_name'] = '单页';
                    $row['resource_title'] = Xccmspageinfo::get_title($row['resource_id']);
                    $row['resource_url'] = addon_url('xccms/index/page', [':id'=>$row['resource_id']]);
                    break;
                case 'news':
                    $row['guest_book_type_name'] = '新闻';
                    $row['resource_title'] = Xccmsnewsinfo::get_title($row['resource_id']);
                    $row['resource_url'] = addon_url('xccms/index/news_detail', [':id'=>$row['resource_id']]);
                    break;
                case 'aboutus':
                    $row['guest_book_type_name'] = '关于我们';
                    break;
                case 'contactus':
                    $row['guest_book_type_name'] = '联系我们';
                    break;
                case 'partner':
                    $row['guest_book_type_name'] = '合作伙伴';
                    break;
                case 'job':
                    $row['guest_book_type_name'] = '招聘';
                    $row['resource_title'] = Xccmsjobinfo::get_name($row['resource_id']);
                    $row['resource_url'] = addon_url('xccms/index/job_detail', [':id'=>$row['resource_id']]);
                    break;
            }

            $this->view->assign('row', $row);
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);

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
