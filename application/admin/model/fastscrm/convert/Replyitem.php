<?php

namespace app\admin\model\fastscrm\convert;

use think\Model;
use traits\model\SoftDelete;

class Replyitem extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'fastscrm_reply_item';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'typedata_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    public function replygroup()
    {
        return $this->belongsTo('replygroup', 'group_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function getTypedataList()
    {
        return ['1' => __('Typedata 1'), '2' => __('Typedata 2')];
    }


    public function getTypedataTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['typedata']) ? $data['typedata'] : '');
        $list = $this->getTypedataList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
