<?php

namespace app\admin\controller\fastscrm\company;

use addons\fastscrm\library\Job\AddJob;
use addons\fastscrm\library\WeWork;
use app\admin\controller\fastscrm\Scrmbackend;
use fast\Http;
use fast\Tree;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\StreamedResponse;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use Exception;
use think\Model;

/**
 * 员工管理
 *
 * @icon fa fa-circle-o
 */
class Worker extends Scrmbackend
{

    /**
     * Worker模型对象
     * @var \app\admin\model\fastscrm\company\Worker
     */
    protected $model = null;
    protected $departmodel = null;
    protected $relationSearch = true;
    protected $rulelist = [];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\company\Worker;
        $this->view->assign("genderList", $this->model->getGenderList());
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->departmodel = new \app\admin\model\fastscrm\company\Depart;
        // 必须将结果集转换为数组
        $ruleList = collection($this->departmodel->order('parentid ASC,order DESC')->select())->toArray();
        Tree::instance()->init($ruleList, 'parentid');
        $this->rulelist = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'name');
        $ruledata = [];
        foreach ($this->rulelist as $k => &$v) {
            $ruledata[$v['depart_id']] = $v['name'];
        }
        $this->view->assign('ruledata', $ruledata);
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
                if ($this->request->get("iscustomer")) {
                    return $this->selectpage();
                } else {
                    return $this->selectChatpage();
                }
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);


            foreach ($list as $item) {
                $departs = Db::name('fastscrm_depart')->where('depart_id', 'in', $item['department'])->column('name');
                $item->departname = implode(',', $departs);

                $stores = Db::name('fastscrm_store')->where('id', 'in', $item['store_id'])->column('store_name');
                $item->storename = implode(',', $stores);

            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    public function searchfind()
    {
        $departs = Db::name('fastscrm_worker')->where('status', 1)->select();
        $searchlist = [];
        foreach ($departs as $key => $value) {
            $searchlist[] = ['id' => $value['userid'], 'name' => $value['name']];
        }
        $data = ['searchlist' => $searchlist];
        $this->success('', null, $data);
    }

    /**
     * 下载图片
     *  更新员工二维码
     */

    public function download()
    {

        $ids = $this->request->get("id");
        $worker = Db::name('fastscrm_worker')
            ->where('id', $ids)
            ->find();

        $work = new WeWork('App');
        $params = array();
        $params['type'] = 1;
        $params['scene'] = 2;//场景，1-在小程序中联系，2-通过二维码联系
        $params['user'][] = $worker['userid'];
        $res = $work->contactWay($params);
        Db::name('fastscrm_worker')
            ->where('userid', $worker['userid'])
            ->update(array('qr_code' => $res['qr_code']));


        $url = $res['qr_code'];

        $name = $worker['name'] . '的二维码';

        $contentType = '';
        try {
            $client = new Client();

            $response = $client->request('GET', $url,
                ['stream' => true, 'verify' => false, 'allow_redirects' => ['strict' => true]]);

            $body = $response->getBody();
            $contentType = $response->getHeader('Content-Type');
            $contentType = $contentType[0] ?? 'image/png';
        } catch (\Exception $e) {
            $this->error("图片下载失败");
        }

        $contentTypeArr = explode('/', $contentType);
        if ($contentTypeArr[0] !== 'image') {
            $this->error("只支持图片文件");
        }

        $response = new StreamedResponse(function () use ($body) {
            while (!$body->eof()) {
                echo $body->read(1024);
            }
        });
        $response->headers->set('Content-Type', 'application/x-download');
        $response->headers->set('Content-Disposition: attachment; filename=', $name . '.png');
        $response->send();
        return;
    }


    /**
     * 邀请员工
     */
    public function inviter($ids)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isAjax()) {
            $worker = Db::name('fastscrm_worker')
                ->where('id', $ids)
                ->find();
            $work = new WeWork();
            $params['user'][] = $worker['userid'];

            $work->inviterWorker($params);
            $this->success("邀请成功", null, ['id' => $ids]);
        }
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
                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $work = new WeWork();
                $params['department'] = $this->request->post("department/a");
                $work->createWorker($params);
                $params['department'] = implode(',', $params['department']);
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
     * 编辑
     */
    public function edit($ids = null)
    {
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
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                $params['department'] = $this->request->post("department/a");
                $work = new WeWork();
                $work->updateWorker($params);
                $params['department'] = implode(',', $params['department']);
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
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
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $departlist = explode(',', $row->department);
        foreach ($departlist as $k => $v) {
            $departids[] = $v;
        }
        $this->view->assign("row", $row);
        $this->view->assign("departids", $departids);
        return $this->view->fetch();
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if (!$this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ? $ids : $this->request->post("ids");
        if ($ids) {
            $pk = $this->model->getPk();
            $adminIds = $this->getDataLimitAdminIds();
            if (is_array($adminIds)) {
                $this->model->where($this->dataLimitField, 'in', $adminIds);
            }
            $list = $this->model->where($pk, 'in', $ids)->select();

            $count = 0;
            $work = new WeWork();
            Db::startTrans();
            try {
                foreach ($list as $k => $v) {
                    $temp = $v->toArray();
                    $temp['createtime'] = time();
                    unset($temp['gender_text']);
                    unset($temp['status_text']);
                    unset($temp['updatetime']);
                    unset($temp['id']);
                    Db::name('fastscrm_worker_delete')
                        ->insert($temp);
                    $work->deleteWorker($v->userid);

                    $count += $v->delete();
                }
                Db::commit();
            } catch (PDOException $e) {
                Db::rollback();
                $this->error($e->getMessage());
            } catch (Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            if ($count) {
                $this->success();
            } else {
                $this->error(__('No rows were deleted'));
            }
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }

    /**
     * 真实删除
     */
    public function destroy($ids = "")
    {
        if (!$this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ? $ids : $this->request->post("ids");
        $pk = $this->model->getPk();
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            $this->model->where($this->dataLimitField, 'in', $adminIds);
        }
        if ($ids) {
            $this->model->where($pk, 'in', $ids);
        }
        $count = 0;
        Db::startTrans();
        try {
            $list = $this->model->onlyTrashed()->select();
            foreach ($list as $k => $v) {
                $count += $v->delete(true);
            }
            Db::commit();
        } catch (PDOException $e) {
            Db::rollback();
            $this->error($e->getMessage());
        } catch (Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($count) {
            $this->success();
        } else {
            $this->error(__('No rows were deleted'));
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }

    /**
     * 同步企微员工
     */
    public function sync()
    {

        $job =  new AddJob();
        $param['admin_id'] = $this->auth->isLogin() ? $this->auth->id : 0;
        $param['username'] = $this->auth->isLogin() ? $this->auth->username : __('Unknown');
        $param['task'] = 'workerJob';
        $param['ip'] = request()->ip();
        $job->add($param);
        $this->success('同步任务已挂起');
    }

    /**
     * 查询门店
     */
    public function query($ids)
    {
        $row = $this->model->get($ids);

        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
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
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * Selectpage的实现方法
     *
     * 当前方法只是一个比较通用的搜索匹配,请按需重载此方法来编写自己的搜索逻辑,$where按自己的需求写即可
     * 这里示例了所有的参数，所以比较复杂，实现上自己实现只需简单的几行即可
     *
     */
    protected function selectChatpage()
    {
        //设置过滤方法
        $this->request->filter(['trim', 'strip_tags', 'htmlspecialchars']);

        //搜索关键词,客户端输入以空格分开,这里接收为数组
        $word = (array)$this->request->request("q_word/a");
        //当前页
        $page = $this->request->request("pageNumber");
        //分页大小
        $pagesize = $this->request->request("pageSize");
        //搜索条件
        $andor = $this->request->request("andOr", "and", "strtoupper");
        //排序方式
        $orderby = (array)$this->request->request("orderBy/a");
        //显示的字段
        $field = $this->request->request("showField");
        //主键
        $primarykey = $this->request->request("keyField");
        //主键值
        $primaryvalue = $this->request->request("keyValue");
        //搜索字段
        $searchfield = (array)$this->request->request("searchField/a");
        //自定义搜索条件
        $custom = (array)$this->request->request("custom/a");
        //是否返回树形结构
        $istree = $this->request->request("isTree", 0);
        $ishtml = $this->request->request("isHtml", 0);
        if ($istree) {
            $word = [];
            $pagesize = 999999;
        }
        $order = [];
        foreach ($orderby as $k => $v) {
            $order[$v[0]] = $v[1];
        }
        $field = $field ? $field : 'name';

        //如果有primaryvalue,说明当前是初始化传值
        if ($primaryvalue !== null) {
            $where = [$primarykey => ['in', $primaryvalue]];
            $pagesize = 999999;
        } else {
            $where = function ($query) use ($word, $andor, $field, $searchfield, $custom) {
                $logic = $andor == 'AND' ? '&' : '|';
                $searchfield = is_array($searchfield) ? implode($logic, $searchfield) : $searchfield;
                $searchfield = str_replace(',', $logic, $searchfield);
                $word = array_filter(array_unique($word));
                if (count($word) == 1) {
                    $query->where($searchfield, "like", "%" . reset($word) . "%");
                } else {
                    $query->where(function ($query) use ($word, $searchfield) {
                        foreach ($word as $index => $item) {
                            $query->whereOr(function ($query) use ($item, $searchfield) {
                                $query->where($searchfield, "like", "%{$item}%");
                            });
                        }
                    });
                }
                if ($custom && is_array($custom)) {
                    foreach ($custom as $k => $v) {
                        if (is_array($v) && 2 == count($v)) {
                            $query->where($k, trim($v[0]), $v[1]);
                        } else {
                            $query->where($k, '=', $v);
                        }
                    }
                }
            };
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            $this->model->where($this->dataLimitField, 'in', $adminIds);
        }
        $list = [];
        $owners =  Db::name('fastscrm_group_chat')->group('owner')->column('owner');
        $total =  $this->model->where($where)
            ->where('userid','in',$owners)
            ->group('userid')
            ->count();
        if ($total > 0) {
            if (is_array($adminIds)) {
                $this->model->where($this->dataLimitField, 'in', $adminIds);
            }

            $fields = is_array($this->selectpageFields) ? $this->selectpageFields : ($this->selectpageFields && $this->selectpageFields != '*' ? explode(',', $this->selectpageFields) : []);

            //如果有primaryvalue,说明当前是初始化传值,按照选择顺序排序
            if ($primaryvalue !== null && preg_match("/^[a-z0-9_\-]+$/i", $primarykey)) {
                $primaryvalue = array_unique(is_array($primaryvalue) ? $primaryvalue : explode(',', $primaryvalue));
                //修复自定义data-primary-key为字符串内容时，给排序字段添加上引号
                $primaryvalue = array_map(function ($value) {
                    return '\'' . $value . '\'';
                }, $primaryvalue);

                $primaryvalue = implode(',', $primaryvalue);

                $this->model->orderRaw("FIELD(`{$primarykey}`, {$primaryvalue})");
            } else {
                $this->model->order($order);
            }

            $datalist = $this->model->where($where)
                ->where('userid','in',$owners)
                ->page($page, $pagesize)
                ->group('userid')
                ->select();

            foreach ($datalist as $index => $item) {
                unset($item['password'], $item['salt']);
                if ($this->selectpageFields == '*') {
                    $result = [
                        $primarykey => isset($item[$primarykey]) ? $item[$primarykey] : '',
                        $field      => isset($item[$field]) ? $item[$field] : '',
                    ];
                } else {
                    $result = array_intersect_key(($item instanceof Model ? $item->toArray() : (array)$item), array_flip($fields));
                }
                $result['pid'] = isset($item['pid']) ? $item['pid'] : (isset($item['parent_id']) ? $item['parent_id'] : 0);
                $list[] = $result;
            }
            if ($istree && !$primaryvalue) {
                $tree = Tree::instance();
                $tree->init(collection($list)->toArray(), 'pid');
                $list = $tree->getTreeList($tree->getTreeArray(0), $field);
                if (!$ishtml) {
                    foreach ($list as &$item) {
                        $item = str_replace('&nbsp;', ' ', $item);
                    }
                    unset($item);
                }
            }
        }
        //这里一定要返回有list这个字段,total是可选的,如果total<=list的数量,则会隐藏分页按钮
        return json(['list' => $list, 'total' => $total]);
    }

}
