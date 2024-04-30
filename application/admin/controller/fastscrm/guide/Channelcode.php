<?php

namespace app\admin\controller\fastscrm\guide;

use addons\fastscrm\library\WeWork;
use app\admin\controller\fastscrm\Scrmbackend;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\StreamedResponse;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 渠道活码
 *
 * @icon fa fa-circle-o
 */
class Channelcode extends Scrmbackend
{

    /**
     * Channelcode模型对象
     * @var \app\admin\model\fastscrm\guide\Channelcode
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\guide\Channelcode;
        $this->view->assign("typeList", $this->model->getTypeList());
        $this->view->assign("sceneList", $this->model->getSceneList());
        $this->view->assign("styleList", $this->model->getStyleList());
        $this->view->assign("skipVerifyList", $this->model->getSkipVerifyList());
        $this->view->assign("isExclusiveList", $this->model->getIsExclusiveList());
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

            $filter = $this->request->get("filter", '');

            $list = $this->model
                ->with(["channelgroup"])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);
            foreach ($list as $row) {
                $row->workertotal = Db::name('fastscrm_channel_workers')->where('code_id',$row->id)->count();
                $row->scantotal = Db::name('fastscrm_customer')->where('state',$row->name)->count();
                $row->losetotal = Db::name('fastscrm_customer_lose')->where('state',$row->name)->count();
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
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isPost()) {
            $params    = $this->request->post();
            $workers = array_values(array_unique(explode(',', $params['workers'])));
            if($params['type']==2){
                $workers = array_slice($workers,0,100);
            }else{
                $workers = array_slice($workers,0,1);
            }
            $tags = explode(',', $params['tags']);
            $work = new WeWork('App');
            $data['type'] = $params['type'];
            $data['scene'] = $params['scene'];
            $data['style'] = $params['style'];
            $data['remark'] = $params['remark'];
            $data['state'] = $params['name'];
            $data['skip_verify'] = filter_var($params['skip_verify'], FILTER_VALIDATE_BOOLEAN);
            $data['is_exclusive'] = filter_var($params['is_exclusive'], FILTER_VALIDATE_BOOLEAN);
            $data['user'] = $workers;
            $result = $work->contactWay($data);
            if(!empty($result)){
                unset($params['tags']);
                unset($params['workers']);
                $params['config_id'] = $result['config_id'];
                $params['qr_code'] = isset($result['qr_code'])?$result['qr_code']:'';
                $params['skip_verify'] = filter_var($params['skip_verify'], FILTER_VALIDATE_BOOLEAN)?1:0;
                $params['is_exclusive'] = filter_var($params['is_exclusive'], FILTER_VALIDATE_BOOLEAN)?1:0;
                $params['creater'] = $this->auth->isLogin() ? $this->auth->username : __('Unknown');
                $params['createtime'] = time();
                $id = $this->model->insert($params, false, true);
                foreach ($workers as $worker) {
                    $worker_temp['worker_id'] = $worker;
                    $worker_temp['code_id'] = $id;
                    $worker_temp['createtime'] = time();
                    Db::name('fastscrm_channel_workers')->insert($worker_temp);
                }
                foreach ($tags as $tag) {
                    $tag_temp['tag_id'] = $tag;
                    $tag_temp['code_id'] = $id;
                    $tag_temp['createtime'] = time();
                    Db::name('fastscrm_channel_tags')->insert($tag_temp);
                }
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
        $ids = $ids?$ids:$this->request->request('id');
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
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isPost()) {
            $params    = $this->request->post();
            $workers = array_values(array_unique(explode(',', $params['workers'])));
            if($params['type']==2){
                $workers = array_slice($workers,0,100);
            }else{
                $workers = array_slice($workers,0,1);
            }
            $tags = explode(',', $params['tags']);
            $work = new WeWork('App');
            $data['config_id'] = $row->config_id;
            $data['style'] = $params['style'];
            $data['remark'] = $params['remark'];
            $data['skip_verify'] = filter_var($params['skip_verify'], FILTER_VALIDATE_BOOLEAN);
            $data['user'] = $workers;
            $result = $work->editContactWay($data);
            if(!empty($result)){
                unset($params['tags']);
                unset($params['workers']);
                unset($params['id']);
                $params['skip_verify'] = filter_var($params['skip_verify'], FILTER_VALIDATE_BOOLEAN)?1:0;
                $params['is_exclusive'] = filter_var($params['is_exclusive'], FILTER_VALIDATE_BOOLEAN)?1:0;
                $params['updatetime'] = time();
                $this->model->where('id',$row->id)->update($params);
                Db::name('fastscrm_channel_workers')->where('code_id',$row->id)->delete();
                foreach ($workers as $worker) {
                    $worker_temp['worker_id'] = $worker;
                    $worker_temp['code_id'] = $row->id;
                    $worker_temp['createtime'] = time();
                    Db::name('fastscrm_channel_workers')->insert($worker_temp);
                }
                Db::name('fastscrm_channel_tags')->where('code_id',$row->id)->delete();
                foreach ($tags as $tag) {
                    $tag_temp['tag_id'] = $tag;
                    $tag_temp['code_id'] = $row->id;
                    $tag_temp['createtime'] = time();
                    Db::name('fastscrm_channel_tags')->insert($tag_temp);
                }
            }
            $this->success();
        }
        $workers = Db::name('fastscrm_channel_workers')->where('code_id',$row->id)->select();
        $workers_list = [];
        foreach ($workers as $worker){
           $user = Db::name('fastscrm_worker')->where('userid',$worker['worker_id'])->find();
           if(!empty($user)){
               $workers_list[] = $user;
           }
        }
        $tags = Db::name('fastscrm_channel_tags')->where('code_id',$row->id)->select();
        $tags_list = [];
        foreach ($tags as $tag) {
            $temp = Db::name('fastscrm_tag')->where('tag_id',$tag['tag_id'])->find();
            if(!empty($temp)){
                $tags_list[] = $temp;
            }
        }
        $row->workers = $workers_list;
        $row->tags = $tags_list;
        $this->assignconfig("row", $row);
        return $this->view->fetch();
    }


    /**
     * 下载渠道码
     */
    public function download()
    {
        $ids = $this->request->get("id");
        $row = $this->model->get(['id' => $ids]);
        $url = $row->qr_code;
        $name = '渠道活码-'.$row->name;
        header("Content-Type: application/force-download");
        header('Content-Disposition: attachment; filename="'.$name.'.png"');
        $img = file_get_contents($url);
        echo $img;
        return;
    }

}
