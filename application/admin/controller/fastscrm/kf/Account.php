<?php

namespace app\myadmin\controller\fastscrm\kf;

use addons\fastscrm\library\Job\AddJob;
use addons\fastscrm\library\WeWork;
use app\common\controller\Backend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 客服账号
 *
 * @icon fa fa-circle-o
 */
class Account extends Backend
{

    /**
     * Account模型对象
     * @var \app\admin\model\fastscrm\kf\Account
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\kf\Account;

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
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $work = new WeWork('App');
                try {
                    $mediaInfo = $work->tempMedia('image', $params['avatar']);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
                $params['media_id'] = $mediaInfo['media_id'];
                $params['endtime'] = $mediaInfo['created_at'] + (2 * 24 * 3600);
                $work = new WeWork('App');
                try {
                    $kfInfo = $work->kfAdd(['name' => $params['name'], 'media_id' => $params['media_id']]);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
                $params['open_kfid'] = $kfInfo['open_kfid'];
                $params['manage_privilege'] = '1';
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
                $media_id = $row->media_id;
                if ($params['avatar'] != $row->avatar || $row->endtime > time()) {
                    $work = new WeWork('App');
                    try {
                        $mediaInfo = $work->tempMedia('image', $params['avatar']);
                    } catch (\Exception $e) {
                        $this->error($e->getMessage());
                    }
                    $params['media_id'] = $mediaInfo['media_id'];
                    $params['endtime'] = $mediaInfo['created_at'] + (2 * 24 * 3600);
                    $media_id = $params['media_id'];
                }
                $work = new WeWork('App');
                try {
                    $work->kfUpdate([
                        'name' => $params['name'],
                        'open_kfid' => $row->open_kfid,
                        'media_id' => $media_id
                    ]);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
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
            $work = new WeWork('App');
            $count = 0;
            Db::startTrans();
            try {
                foreach ($list as $k => $v) {
                    try {
                        $work->kfDel(['open_kfid' => $v->open_kfid]);
                    } catch (\Exception $e) {
                        $this->error($e->getMessage());
                    }
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

    public function searchfind()
    {
        $departs  = Db::name('fastscrm_kf_account')->select();
        $searchlist = [];
        foreach ($departs as $key => $value) {
            $searchlist[] = ['open_kfid' => $value['open_kfid'], 'name' => $value['name']];
        }
        $data = ['searchlist' => $searchlist];
        $this->success('', null, $data);
    }

    /**
     * 同步客服
     */
    public function sync()
    {
        $job = new AddJob();
        $param['admin_id'] = $this->auth->isLogin() ? $this->auth->id : 0;
        $param['username'] = $this->auth->isLogin() ? $this->auth->username : __('Unknown');
        $param['task'] = 'kf';
        $param['ip'] = request()->ip();
        $job->add($param);
        $this->success('同步任务已挂起');
    }

    /**
     * 获取客户账号链接
     */
    public function getUrl($ids)
    {
        $row = $this->model->get(['id' => $ids]);
        $work = new WeWork('App');
        if($row->url){
            $this->success('获取成功',null,$row);
        }
        try {
            $result = $work->kfUrl(['open_kfid' => $row->open_kfid]);
            $row->url = $result['url'];
            $row->save();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('获取成功',null,$row);
    }

}
