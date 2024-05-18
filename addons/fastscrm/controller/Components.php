<?php

namespace addons\fastscrm\controller;

use think\addons\Controller;
use think\Db;

class Components extends Controller
{
    protected $model = null;

    /**
     * 客户标签列表
     */
    public function getData()
    {
        $this->model = new \app\admin\model\fastscrm\guide\Batch;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isPost()) {
            $ids = $this->request->post('ids');
            $ids = explode(',', $ids);
            $groups = Db::name('fastscrm_tag_group')->field('id,group_name')->select();
            foreach ($groups as &$group) {
                $tags = Db::name('fastscrm_tag')->where('group_id',
                    $group['id'])->field('id,group_id,tag_id,name')->select();
                foreach ($tags as &$tag) {
                    if (in_array($tag['id'], $ids)) {
                        $tag['active'] = true;
                    } else {
                        $tag['active'] = false;
                    }
                }
                unset($tag);
                $group['tags'] = $tags;
            }
            unset($group);
            $result = array("rows" => $groups);
            return json($result);
        }
    }
}
