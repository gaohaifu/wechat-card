<?php

namespace addons\myadmin\model\web;


class Category extends \addons\myadmin\model\WebCategory
{
    //资讯分类
    public static function News($company_id)
    {
        return self::tableList('article', $company_id);
    }
    // 商品分类
    public static function Mall($company_id)
    {
        return self::tableList('product', $company_id);
    }

    /**
     * 列表
     */
    public static function tableList($mould_id, $company_id = 0)
    {
        $where = [];
        if ($company_id) {
            $where['company_id'] = ['in', [0, $company_id]];
        }
        if ($mould_id) {
            $where['mould_id'] = $mould_id;
        }
        $data = self::field('id,mould_id,icon,name', false)->where($where)->order('id desc')->limit(100)->select();
        return $data;
    }

    /**
     * 详情
     */
    public static function detail($param, $with = [], $company_id)
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
