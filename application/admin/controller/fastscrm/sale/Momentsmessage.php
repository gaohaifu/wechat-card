<?php

namespace app\myadmin\controller\fastscrm\sale;

use addons\fastscrm\library\Job\AddJob;
use app\myadmin\controller\fastscrm\Scrmbackend;
use fast\Http;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use Exception;
/**
 * 朋友圈营销
 *
 * @icon fa fa-circle-o
 */
class Momentsmessage extends Scrmbackend
{

    /**
     * Momentsmessage模型对象
     * @var \app\admin\model\fastscrm\sale\Momentsmessage
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\sale\Momentsmessage;
        $this->view->assign("typedataList", $this->model->getTypedataList());
        $this->view->assign("statusList", $this->model->getStatusList());
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
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            foreach ($list as $row) {
                $row->item_title = Db::name('fastscrm_material_item')->where('id', $row->item_id)->column('title');
                if(!empty($row->worker_id)){
                    $row->worker_total = count(explode(',',$row->worker_id));
                }else{
                    $row->worker_total = 0;
                }
                if(!empty($row->depart_id)){
                    $row->depart_total = count(explode(',',$row->depart_id));
                }else{
                    $row->depart_total = 0;
                }
                if(!empty($row->store_id)){
                    $row->store_total = count(explode(',',$row->store_id));
                }else{
                    $row->store_total = 0;
                }
                if(!empty($row->item_id)){
                    $row->item_total = count(explode(',',$row->item_id));
                }else{
                    $row->item_total = 0;
                }
                  if(!empty($row->creater)){
                        $row->creatername =Db::name('fastscrm_worker')->where('userid', $row->creater)->column('name');
                  }else{
                        $row->creatername = '后台';
                  }
            }

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
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                if(empty($params['content']) && empty($params['item_id'])){
                    $this->error('发布文案与素材附件不能同时为空');
                }
                if(empty($params['worker_id']) && empty($params['depart_id']) && empty($params['store_id'])){
                    $this->error('请选择营销人群');
                }
                $items =Db::name('fastscrm_material_item')->where('id','in',$params['item_id'])->field('type')->select();
                if(count($items)>1){
                    $images=0;
                    $links=0;
                    $videos=0;
                    foreach ($items as $item) {
                        switch ($item['type']){
                            case '1':
                                $images++;
                                break;
                            case '3':
                                $links++;
                                break;
                            case '5':
                                $videos++;
                                break;
                        }
                    }
                    if($images>0 && ($links>0 || $videos>0)){
                        $this->error('素材附件只能选择一种类型');
                    }elseif ($links>0 && ($images>0 || $videos>0)){
                        $this->error('素材附件只能选择一种类型');
                    }elseif ($videos>0 && ($images>0 || $links>0)){
                        $this->error('素材附件只能选择一种类型');
                    } elseif ($images>9){
                        $this->error('图片类型附件最多选择9张');
                    }
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
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    $result = $this->model->allowField(true)->save($params);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }

    /**
     * 执行任务
     */
    public function action($ids)
    {
        $job =  new AddJob();
        $param['admin_id'] = $this->auth->isLogin() ? $this->auth->id : 0;
        $param['username'] = $this->auth->isLogin() ? $this->auth->username : __('Unknown');
        $param['task'] = 'momentsSale';
        $param['ip'] = request()->ip();
        $param['id'] = $ids;
        $param['domain'] = cdnurl('', true);
        $result = $job->add($param);
        if ($result){
            Db::name('fastscrm_moments_sale')->where('id',$ids)->update(['status'=>1]);
            $this->success('朋友圈任务已挂起');
        }else{
            $this->error('执行失败');
        }
    }
}
