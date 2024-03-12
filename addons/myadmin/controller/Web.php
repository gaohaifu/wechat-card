<?php

namespace addons\myadmin\controller;

use think\Config;

class Web extends \addons\myadmin\library\FrontAddon
{

    protected $layout = 'company';

    public function index()
    {
        $addon = get_addon_config('myadmin');
        $this->view->assign('background', isset($addon['indexbackground']) && $addon['indexbackground'] ? $addon['indexbackground'] : '');
        $single = \addons\myadmin\model\web\Single::tableList(['limit' => 3], [], COMPANY_ID);
        $product = \addons\myadmin\model\web\Product::tableList(['limit' => 3], [], COMPANY_ID);
        $this->view->assign('product', $product);
        $this->view->assign('company', Config::get('company'));
        $this->view->assign('single', $single);
        return $this->view->fetch('default/index');
    }

    /**
     * 资讯列表
     */
    public function content()
    {
        $this->view->assign('title', '全部');
        $category_id = $this->request->param('category_id');
        $mould_id = $this->request->param('mould_id');
        if ($mould_id) {
            $mould = \addons\myadmin\model\WebMould::get($mould_id);
            if ($mould) {
                $this->view->assign('title', __($mould['name']));
            }
        }

        if ($category_id) {
            $category =  \addons\myadmin\model\web\Category::get($category_id);
            $this->view->assign('title', $category['name']);
            $mould_id = $category['mould_id'];
        }
        $this->view->assign('mould_id', $mould_id);
        $mouldList = \addons\myadmin\model\WebMould::order('weigh desc')->column('id,icon,name', 'id');
        $this->view->assign('mouldList', $mouldList);
        $this->view->assign('category', \addons\myadmin\model\web\Category::tableList($mould_id, COMPANY_ID));
        $this->view->assign('list', \addons\myadmin\model\web\Content::tableList($this->request->param(), [], COMPANY_ID));
        $this->view->assign('category_id', $category_id);
        return $this->view->fetch('default/content');
    }

    /**
     * 文章详情
     */
    public function content_detail()
    {
        $id = $this->request->param('id');
        $row = \addons\myadmin\model\web\Content::detail($id, [], COMPANY_ID);
        if ($row) {
            $row->setInc('views');
            $this->view->assign('category', \addons\myadmin\model\web\Category::tableList($row['mould_id'], COMPANY_ID));
            $this->view->assign('title', $row['title']);
            $this->view->assign('category_id', $row['category_id']);
            $this->view->assign('row', $row);
            return $this->view->fetch('default/content_detail');
        }

        $this->error('找不到产品信息');
    }

    public function product()
    {
        $this->view->assign('title', '商品列表');
        $category_id = $this->request->param('category_id');

        if ($category_id) {
            $category =  \addons\myadmin\model\web\ProductCategory::get($category_id);
            $this->view->assign('title', $category['name']);
        }
        $this->view->assign('list', \addons\myadmin\model\web\Product::tableList($this->request->param(), [], COMPANY_ID));

        $this->view->assign('category', \addons\myadmin\model\web\ProductCategory::tableList(COMPANY_ID));
        $this->view->assign('category_id', $category_id);
        return $this->view->fetch('default/product');
    }

    /**
     * 商品详情
     */
    public function product_detail()
    {
        $id = $this->request->param('id');
        $row = \addons\myadmin\model\web\Product::detail($id, [], COMPANY_ID);
        if ($row) {
            $row->setInc('views');
            $this->view->assign('category', \addons\myadmin\model\web\ProductCategory::tableList(COMPANY_ID));
            $this->view->assign('title', $row['name']);
            $this->view->assign('category_id', $row['category_id']);
            $this->view->assign('row', $row);
            return $this->view->fetch('default/product_detail');
        }
        $this->error('找不到产品信息');
    }

    public function single()
    {
        $single = \addons\myadmin\model\web\Single::tableList($this->request->param(), [], COMPANY_ID);
        if ($single) {
            $this->view->assign('single', $single);
            $id = $this->request->param('id', isset($single[0]['id']) && $single[0]['id'] ? $single[0]['id'] : '');
            $row = \addons\myadmin\model\web\Single::detail($id, []);
            $row->setInc('views');
            $this->view->assign('title', $row['name']);
            $this->view->assign('row', $row);
            return $this->view->fetch('default/single');
        }
        $this->error('找不到页面信息');
    }
}
