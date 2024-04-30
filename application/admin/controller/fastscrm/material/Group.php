<?php

namespace app\admin\controller\fastscrm\material;

use app\admin\controller\fastscrm\Scrmbackend;
use think\Db;

/**
 * 素材分组
 *
 * @icon fa fa-circle-o
 */
class Group extends Scrmbackend
{

    /**
     * Group模型对象
     * @var \app\admin\model\fastscrm\material\Group
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\fastscrm\material\Group;

    }

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

                  $list = $this->model
                      ->where($where)
                      ->order($sort, $order)
                      ->paginate($limit);

                  $result = array("total" => $list->total(), "rows" => $list->items());

                  return json($result);
            }
            return $this->view->fetch();
      }


    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


      public function searchfind()
      {
            $departs  = Db::name('fastscrm_material_group')->where('id','>', 0)->select();
            $searchlist = [];
            foreach ($departs as $key => $value) {
                  $searchlist[] = ['id' => $value['id'], 'name' => $value['title']];
            }
            $data = ['searchlist' => $searchlist];
            $this->success('', null, $data);
      }


}
