<?php

namespace app\admin\model;

use think\Model;


class Posters extends Model
{

    // 表名
    protected $name = 'posters';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $deleteTime = false;


    const QR = 'qr';
    const TEXT = 'text';
    const IMAGE = 'image';

    public static $typeLabels
        = [
            self::QR    => '图片',
            self::TEXT  => '文本',
            self::IMAGE => '二维码',
        ];

    // 追加属性
    protected $append = [
        'create_time_text',
        'update_time_text'
    ];

    protected static function init()
    {
        self::afterDelete(function ($posters){
            (new PostersRecord())->where(['posters_id' => $posters->id])->delete();
        });
    }

    public function getCreateTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['create_time']) ? $data['create_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    public function getUpdateTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['update_time']) ? $data['update_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setCreateTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setUpdateTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    public function getConfigAttr($config)
    {
        return json_decode($config, true);
    }

    public function setConfigAttr($config)
    {
        return ! is_array($config) ? $config : json_encode($config, JSON_UNESCAPED_UNICODE);
    }

}
