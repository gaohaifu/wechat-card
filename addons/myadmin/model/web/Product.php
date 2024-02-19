<?php

namespace addons\myadmin\model\web;

class Product extends \addons\myadmin\model\WebProduct
{
    use \addons\myadmin\library\traits\AttrModel;
    public function category()
    {
        return $this->hasOne('Category', 'id', 'category_id', [], 'LEFT');
    }

    /**
     * 列表
     */
    public static function tableList($param, $with = [], $company_id = 0)
    {
        $limit = 10;
        if (isset($param['limit'])) {
            $limit = $param['limit'];
        }
        $where = [];
        if ($company_id) {
            $where['company_id'] = $company_id;
        }
        if (isset($param['category_id']) && !empty($param['category_id'])) {
            $where['category_id'] = $param['category_id'];
        }
        $data = self::field('contents,deletetime', true)->with($with)->where($where)->where(function ($query) use ($param) {
            $query->where('status', 'normal');
            if (isset($param['title']) && !empty($param['title'])) {
                $query->where('title', 'like', '%' . $param['title'] . '%');
            }
            if (isset($param['keyword']) && !empty($param['keyword'])) {
                $query->where('title', 'like', '%' . $param['keyword'] . '%');
            }
        })->order('weigh desc')->paginate($limit);
        foreach ($data as $res) {
            //$res->hidden(['images']);
            //$res->append(['image', 'images_text']);
        }
        return $data;
    }

    /**
     * 详情
     */
    public static function detail($param, $with = [], $company_id=0)
    {
        if (is_array($param)) {
            $where = $param;
        } else {
            $where['id'] = $param;
        }
        if ($company_id) {
            $where['company_id'] = $company_id;
        }
        $detail = self::field('deletetime', true)->with($with)->where($where)->find();
        return $detail;
    }
}
