<?php

namespace app\admin\controller\fastscrm\crm;

use addons\fastscrm\library\WeWork;
use app\admin\controller\fastscrm\Scrmbackend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use Exception;
/**
 * 标签组管理
 *
 * @icon fa fa-circle-o
 */
class Taggroup extends Scrmbackend
{

    /**
     * Taggroup模型对象
     * @var \app\admin\model\fastscrm\crm\Taggroup
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\crm\Taggroup;

    }

      public function searchfind()
      {
            $departs  = Db::name('fastscrm_tag_group')->where('id','>', 0)->select();
            $searchlist = [];
            foreach ($departs as $key => $value) {
                  $searchlist[] = ['id' => $value['id'], 'name' => $value['group_name']];
            }
            $data = ['searchlist' => $searchlist];
            $this->success('', null, $data);
      }


    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

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
                $work = new WeWork('App');
                $work->updateTag($params,$ids,1);

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
                    $work->deleteTag($v->group_id,1);
                    $count += $v->delete();
                    $this->delTagGroup($v->id);
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
     * 删除标签组内的标签和用户标签
     */
    public function delTagGroup($group_id)
    {
        $list = \app\admin\model\fastscrm\crm\Tag::where('group_id', $group_id)->select();
        foreach ($list as $v) {
            $customers = \app\admin\model\fastscrm\crm\Customer::where('locate(:tag_id,fl_tags)')->bind(['tag_id'=>$v->tag_id])->field('id,fl_tags')->select();
            foreach ($customers as $customer) {
                $fl_tags =  array_values(array_diff(json_decode($customer->fl_tags), [$v->tag_id]));
                \app\admin\model\fastscrm\crm\Customer::where('id',$customer->id)->update(['fl_tags'=>json_encode($fl_tags)]);
            }
            \app\admin\model\fastscrm\crm\Tag::where('id', $v->id)->delete();
        }
        return true;
    }
}
