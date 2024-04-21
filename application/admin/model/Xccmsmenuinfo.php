<?php

namespace app\admin\model;

use fast\Tree;
use think\Model;


class Xccmsmenuinfo extends Model
{

    

    

    // 表名
    protected $name = 'xccms_menu_info';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }


    public function get_category_tree($field = 'name'){
        $where = [];
        if (COMPANY_ID){
            $where['company_id'] = COMPANY_ID;
        }
        $tree = Tree::instance();
        $tree->init(collection($this->where($where)->order('weigh desc, id')->select())->toArray(), 'parent_id');
        $list = $tree->getTreeList($tree->getTreeArray(0), $field);

        $categorydata = [0 => ['type' => 'all', 'name' => __('None')]];
        foreach ($list as $k => $v) {
            $categorydata[$v['id']] = $v;
        }
        return [$list,$categorydata];
    }








}
