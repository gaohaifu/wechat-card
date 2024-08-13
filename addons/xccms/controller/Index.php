<?php

namespace addons\xccms\controller;

use addons\myadmin\model\Domain;
use app\common\service\Language;
use app\common\service\ThemeService;
use think\addons\Controller;
use think\Config;
use think\Validate;

use app\admin\model\Xccmssiteconfig;
use app\admin\model\Xccmsmenuinfo;
use app\admin\model\Xccmsproductcategory;
use app\admin\model\Xccmsproductinfo;
use app\admin\model\Xccmscontentcategory;
use app\admin\model\Xccmscontentinfo;
use app\admin\model\Xccmspageinfo;
use app\admin\model\Xccmswebsitecarousel;
use app\admin\model\Xccmsfriendlink;
use app\admin\model\Xccmspartnerlink;
use app\admin\model\Xccmsnewsinfo;
use app\admin\model\Xccmsguestbook;
use app\admin\model\Xccmsjobinfo;
use app\admin\model\Xccmsfaq;


class Index extends Controller
{
    protected $site_config = [];
    protected $page_code = '';
    protected $page_id = 0;
    protected $company_id = 0;

    public function _initialize()
    {
        if (!isset($_SERVER['HTTP_HOST'])){
            $this->error('域名异常');
        }
        $menuArr = $menuIdArr = [];
        $companyId = input('cid',0);
        if ($companyId){
            $menuArr = ['cid'=>$companyId];
            $this->company_id = $companyId;
        }else{
            $domain = get_first_host($_SERVER['HTTP_HOST']);
            $domains = cache($domain);
            if(!$domains){
                $domains = Domain::where(['name'=>$domain])->cache($domain,60)->find();
                if(!$domains){
                    $this->error('该企业已被删除');
                }
            }
            $this->company_id = $domains['company_id'];
        }

        parent::_initialize();

        $where['company_id'] = $this->company_id;
        //站点配置
        $site_configM = Xccmssiteconfig::where($where)->field('id,json_data')->find();
        if (!$site_configM)
        {
            $this->error('请在后台生成站点配置后，再访问前台');
        }
        $this->site_config = json_decode($site_configM['json_data'], true);

        $this->check_site_status($this->site_config['site_status']);    //判断网站的状态，关闭就跳转


        //菜单
        $main_menu_list = Xccmsmenuinfo::where($where)->field('id,parent_id,name,en_name,menu_type,menu_object_id,url')
            ->where('parent_id', 0)
            ->where('is_top_show', 1)
            ->where('state', 1)
            ->order('weigh desc')
            ->select();
        foreach($main_menu_list as $i=>$item)
        {
            $main_menu_item_url = 'javascript:;';
            switch($item['menu_type'])
            {
                case 'index':
                    $main_menu_item_url = addon_url('xccms/index/index',$menuArr);
                    break;
                case 'partner':
                case 'job':
                    $main_menu_item_url = addon_url('xccms/index/' . $item['menu_type'],$menuArr);
                    break;
                case 'page':
                    $main_menu_item_url = addon_url('xccms/index/page', [':id'=>$item['menu_object_id'],'cid' => $companyId]);
                    break;
                case 'news':
                    $main_menu_item_url = addon_url('xccms/index/news',$menuArr);
                    break;
                case 'link':
                    $main_menu_item_url = $item['url'];
                    break;
                case 'product':
                    $main_menu_item_url = addon_url('xccms/index/product', [':id'=>$item['menu_object_id'],'cid' => $companyId]);
                    break;
                case 'content':
                    $main_menu_item_url = addon_url('xccms/index/info', [':id'=>$item['menu_object_id'],'cid' => $companyId]);
                    break;
                case 'aboutus':
                    $main_menu_item_url = addon_url('xccms/index/about_us',$menuArr);
                    break;
                case 'contactus':
                    $main_menu_item_url = addon_url('xccms/index/contact_us',$menuArr);
                    break;
                case 'faq':
                    $main_menu_item_url = addon_url('xccms/index/faq',$menuArr);
                    break;
            }
            $sub_menu = Xccmsmenuinfo::where($where)->field('id,name,menu_type,menu_object_id,url')
                ->where('parent_id', $item['id'])
                ->where('is_top_show', 1)
                ->where('state', 1)
                ->order('weigh desc')
                ->select();
            foreach($sub_menu as $s=>$sitem)
            {
                $sub_menu_item_url = 'javascript:;';
                switch($sitem['menu_type'])
                {
                    case 'index':
                        $sub_menu_item_url = addon_url('xccms/index/index',$menuArr);
                        break;
                    case 'partner':
                    case 'job':
                        $sub_menu_item_url = addon_url('xccms/index/' . $sitem['menu_type'],$menuArr);
                        break;
                    case 'page':
                        $sub_menu_item_url = addon_url('xccms/index/page', [':id'=>$sitem['menu_object_id']]);
                        break;
                    case 'news':
                        $sub_menu_item_url = addon_url('xccms/index/news',$menuArr);
                    case 'link':
                        $sub_menu_item_url = $item['url'];
                        break;
                    case 'product':
                        $sub_menu_item_url = addon_url('xccms/index/product', [':id'=>$sitem['menu_object_id']]);
                        break;
                    case 'content':
                        $sub_menu_item_url = addon_url('xccms/index/info', [':id'=>$sitem['menu_object_id']]);
                        break;
                    case 'aboutus':
                        $sub_menu_item_url = addon_url('xccms/index/about_us',$menuArr);
                        break;
                    case 'contactus':
                        $sub_menu_item_url = addon_url('xccms/index/contact_us',$menuArr);
                        break;
                    case 'faq':
                        $sub_menu_item_url = addon_url('xccms/index/faq',$menuArr);
                        break;
                }
                $sub_menu[$s]['url'] = $sub_menu_item_url;
            }

            $main_menu_item_url = count($sub_menu) > 0 ? 'javascript:;' : $main_menu_item_url;

            $main_menu_list[$i]['url'] = $main_menu_item_url;
            $main_menu_list[$i]['sub_menu'] = $sub_menu;
        }

        //轮播
        $carousel_list = Xccmswebsitecarousel::where($where)->field('id,title,list_image')->where('state', 1)->order('weigh desc')->select();

        //底部菜单
        $bottom_menu_list = Xccmsmenuinfo::where($where)->field('id,name,menu_type,menu_object_id,url')
            ->where('parent_id', 0)
            ->where('Is_bottom_show', 1)
            ->where('state', 1)
            ->order('weigh desc')
            ->select();
        foreach($bottom_menu_list as $i=>$item)
        {
            $main_menu_item_url = 'javascript:;';
            switch($item['menu_type'])
            {
                case 'index':
                    $main_menu_item_url = addon_url('xccms/index/index',$menuArr);
                    break;
                case 'partner':
                case 'job':
                    $main_menu_item_url = addon_url('xccms/index/' . $item['menu_type'],$menuArr);
                    break;
                case 'page':
                    $main_menu_item_url = addon_url('xccms/index/page', [':id'=>$item['menu_object_id'],'cid' => $companyId]);
                    break;
                case 'news':
                    $main_menu_item_url = addon_url('xccms/index/news',$menuArr);
                    break;
                case 'link':
                    $main_menu_item_url = $item['url'];
                    break;
                case 'product':
                    $main_menu_item_url = addon_url('xccms/index/product', [':id'=>$item['menu_object_id'],'cid' => $companyId]);
                    break;
                case 'content':
                    $main_menu_item_url = addon_url('xccms/index/info', [':id'=>$item['menu_object_id'],'cid' => $companyId]);
                    break;
                case 'aboutus':
                    $main_menu_item_url = addon_url('xccms/index/about_us',$menuArr);
                    break;
                case 'contactus':
                    $main_menu_item_url = addon_url('xccms/index/contact_us',$menuArr);
                    break;
                case 'faq':
                    $main_menu_item_url = addon_url('xccms/index/faq',$menuArr);
                    break;
            }

            $sub_menu = Xccmsmenuinfo::where($where)->field('id,name,menu_type,menu_object_id,url')
                ->where('parent_id', $item['id'])
                ->where('Is_bottom_show', 1)
                ->where('state', 1)
                ->order('weigh desc')
                ->select();
            foreach($sub_menu as $s=>$sitem)
            {
                $sub_menu_item_url = 'javascript:;';
                switch($sitem['menu_type'])
                {
                    case 'index':
                        $sub_menu_item_url = addon_url('xccms/index/index',$menuArr);
                        break;
                    case 'partner':
                    case 'job':
                        $sub_menu_item_url = addon_url('xccms/index/' . $sitem['menu_type'],$menuArr);
                        break;
                    case 'page':
                        $sub_menu_item_url = addon_url('xccms/index/page', [':id'=>$sitem['menu_object_id']]);
                        break;
                    case 'news':
                        $sub_menu_item_url = addon_url('xccms/index/news',$menuArr);
                    case 'link':
                        $sub_menu_item_url = $item['url'];
                        break;
                    case 'product':
                        $sub_menu_item_url = addon_url('xccms/index/product', [':id'=>$sitem['menu_object_id']]);
                        break;
                    case 'content':
                        $sub_menu_item_url = addon_url('xccms/index/info', [':id'=>$sitem['menu_object_id']]);
                        break;
                    case 'aboutus':
                        $sub_menu_item_url = addon_url('xccms/index/about_us',$menuArr);
                        break;
                    case 'contactus':
                        $sub_menu_item_url = addon_url('xccms/index/contact_us',$menuArr);
                        break;
                    case 'faq':
                        $sub_menu_item_url = addon_url('xccms/index/faq',$menuArr);
                        break;
                }

                $sub_menu[$s]['url'] = $sub_menu_item_url;
            }

            $main_menu_item_url = count($sub_menu) > 0 ? 'javascript:;' : $main_menu_item_url;
            $bottom_menu_list[$i]['url'] = $main_menu_item_url;
            $bottom_menu_list[$i]['sub_menu'] = $sub_menu;
        }

        $this->view->assign('site_config', $this->site_config);
        $this->view->assign('menu_list', $main_menu_list);
        $this->view->assign('bottom_menu_list', $bottom_menu_list);
        $this->view->assign('carousel_list', $carousel_list);
        //$this->view->assign('language_list',Language::languageList($this->company_id));
    }

    public function closed()
    {
        $this->view->assign('seo_title', '站点关闭 - ' . $this->site_config['name']);
        $this->view->assign('seo_keywords', '');
        $this->view->assign('seo_description', '');
        $this->view->assign('page_code', $this->page_code);
        $this->view->assign('page_id', $this->page_id);
        return $this->view->fetch($this->site_config['theme'] . '/index/closed');
    }

    public function index()
    {
        $this->page_code = 'index';
        //产品中心
        $product_category_list = Xccmsproductcategory::field('id,name')
            ->where('company_id',$this->company_id)
            ->where('state', 1)
            ->where('is_recommend', 1)
            ->order('weigh desc')
            ->select();
        foreach($product_category_list as $i=>$item)
        {
            $product_category_list[$i]['product_list'] = Xccmsproductinfo::field('id,title,list_image,description')
                ->where('company_id',$this->company_id)
                ->where('category_id', $item['id'])
                ->where('state', 1)
                ->where('is_recommend', 1)
                ->order('weigh desc')
                ->select();
        }

        //新闻
        $news_limit = $this->site_config['theme'] == 'theme1' ? 4 : 6;
        $news_list = Xccmsnewsinfo::field('id,title,list_image,description,createtime')
            ->where('company_id',$this->company_id)
            ->where('state', 1)
            ->order('id desc')
            ->limit($news_limit)
            ->select();

        //友情链接
        $friendlink_list = Xccmsfriendlink::field('id,name,url')
            ->where('company_id',$this->company_id)
            ->where('state', 1)
            ->order('weigh desc')
            ->select();

        //主题扩展
        $this->load_theme_ext($this->site_config['theme']);


        $this->view->assign('page_code', $this->page_code);
        $this->view->assign('page_id', $this->page_id);
        $this->view->assign('seo_title', $this->site_config['seo_title']);
        $this->view->assign('seo_keywords', $this->site_config['seo_keywords']);
        $this->view->assign('seo_description', $this->site_config['seo_description']);
        $this->view->assign('product_category_list', $product_category_list);
        $this->view->assign('news_list', $news_list);
        $this->view->assign('friendlink_list', $friendlink_list);
        return $this->view->fetch($this->site_config['theme'] . '/index/index');
    }

    public function news()
    {
        $this->page_code = 'news';
        $page = input('page', 1);

        $news_list = Xccmsnewsinfo::field('id,title,list_image,description,createtime')
            ->where('company_id',$this->company_id)
            ->where('state', 1)
            ->order('id desc')
            ->paginate(10,false,array('query'=>array()));

        $pagenav = $news_list->render();

        $this->view->assign('page_code', $this->page_code);
        $this->view->assign('page_id', $this->page_id);
        $this->view->assign('seo_title', '新闻资讯 - ' . $this->site_config['name']);
        $this->view->assign('seo_keywords', '新闻资讯 - ' . $this->site_config['name']);
        $this->view->assign('seo_description', '新闻资讯 - ' . $this->site_config['name']);
        $this->view->assign('news_list', $news_list);
        $this->view->assign('pagenav', $pagenav);
        return $this->view->fetch($this->site_config['theme'] . '/index/news');
    }

    public function news_detail()
    {
        $id = input('id', 0);
        $this->page_code = 'news';
        $this->page_id = $id;
        if (!$id)
        {
            $this->error('id不能为空');
        }

        $model = Xccmsnewsinfo::field('id,title,content,visits,seo_title,seo_keywords,seo_description,createtime')
            ->where('company_id',$this->company_id)
            ->where('id', $id)
            ->where('state', 1)
            ->find();

        if (!$model)
        {
            $this->error('没有找到内容');
        }
        $model['seo_title'] = $model['seo_title'] ? $model['seo_title'] : $model['title'];

        $prev_model = Xccmsnewsinfo::field('id,title')
            ->where('company_id',$this->company_id)
            ->where('id', '<', $id)
            ->where('state', 1)
            ->order('id desc')->find();
        $next_model = Xccmsnewsinfo::field('id,title')
            ->where('company_id',$this->company_id)
            ->where('id', '>', $id)
            ->where('state', 1)
            ->find();

        $this->add_visits('news', $model['id'], $model['visits']);

        $this->view->assign('page_code', $this->page_code);
        $this->view->assign('page_id', $this->page_id);
        $this->view->assign('seo_title', $model['seo_title']);
        $this->view->assign('seo_keywords', $model['seo_keywords']);
        $this->view->assign('seo_description', $model['seo_description']);
        $this->view->assign('model', $model);
        $this->view->assign('prev_model', $prev_model);
        $this->view->assign('next_model', $next_model);
        return $this->view->fetch($this->site_config['theme'] . '/index/news_detail');
    }

    public function info()
    {
        $this->page_code = 'info';
        $id = input('id', 0);
        $page = input('page', 1);

        if (!$id)
        {
            $this->error('id不能为空');
        }

        $model = Xccmscontentcategory::field('id,name,seo_title,seo_keywords,seo_description,createtime')
            ->where('company_id',$this->company_id)
            ->where('id', $id)
            ->where('state', 1)
            ->find();

        if (!$model)
        {
            $this->error('没有找到内容');
        }
        $model['seo_title'] = $model['seo_title'] ? $model['seo_title'] : $model['name'];

        $info_list = Xccmscontentinfo::field('id,title,list_image,description,createtime')
            ->where('company_id',$this->company_id)
            ->where('category_id', $id)
            ->where('state', 1)
            ->order('id desc')
            ->paginate(10,false,array('query'=>array()));

        $pagenav = $info_list->render();

        $this->view->assign('page_code', $this->page_code);
        $this->view->assign('page_id', $this->page_id);
        $this->view->assign('seo_title', $model['seo_title']);
        $this->view->assign('seo_keywords', $model['seo_keywords']);
        $this->view->assign('seo_description', $model['seo_description']);
        $this->view->assign('model', $model);
        $this->view->assign('info_list', $info_list);
        $this->view->assign('pagenav', $pagenav);
        return $this->view->fetch($this->site_config['theme'] . '/index/info');
    }

    public function info_detail()
    {
        $id = input('id', 0);
        $this->page_code = 'info';
        $this->page_id = $id;
        if (!$id)
        {
            $this->error('id不能为空');
        }

        $model = Xccmscontentinfo::field('id,category_id,title,content,visits,seo_title,seo_keywords,seo_description,createtime')
            ->where('company_id',$this->company_id)
            ->where('id', $id)
            ->where('state', 1)
            ->find();

        if (!$model)
        {
            $this->error('没有找到内容');
        }
        $model['seo_title'] = $model['seo_title'] ? $model['seo_title'] : $model['title'];

        $prev_model = Xccmscontentinfo::field('id,title')
            ->where('company_id',$this->company_id)
            ->where('id', '<', $id)
            ->where('category_id', $model['category_id'])
            ->where('state', 1)
            ->order('id desc')
            ->find();
        $next_model = Xccmscontentinfo::field('id,title')
            ->where('company_id',$this->company_id)
            ->where('id', '>', $id)
            ->where('category_id', $model['category_id'])
            ->where('state', 1)
            ->find();

        $category_model = Xccmscontentcategory::field('id,name')
            ->where('company_id',$this->company_id)
            ->where('id', $model['category_id'])
            ->find();

        $this->add_visits('info', $model['id'], $model['visits']);

        $this->view->assign('page_code', $this->page_code);
        $this->view->assign('page_id', $this->page_id);
        $this->view->assign('seo_title', $model['seo_title']);
        $this->view->assign('seo_keywords', $model['seo_keywords']);
        $this->view->assign('seo_description', $model['seo_description']);
        $this->view->assign('model', $model);
        $this->view->assign('prev_model', $prev_model);
        $this->view->assign('next_model', $next_model);
        $this->view->assign('category_model', $category_model);
        return $this->view->fetch($this->site_config['theme'] . '/index/info_detail');
    }

    public function product()
    {
        $id = input('id', 0);
        $page = input('page', 1);
        if (!$id)
        {
            $where = null;
        }
        else
        {
            $where = ['id'=>$id];
        }

        $model = Xccmsproductcategory::field('id,name,seo_title,seo_keywords,seo_description,createtime')
            ->where('company_id',$this->company_id)
            ->where($where)
            ->where('state', 1)
            ->find();

        if (!$model)
        {
            $this->error('没有找到内容');
        }
        $model['seo_title'] = $model['seo_title'] ? $model['seo_title'] : $model['name'];

        $product_category_list = Xccmsproductcategory::field('id,name')
            ->where('company_id',$this->company_id)
            ->where('state', 1)
            ->order('weigh desc')
            ->select();
        if (!$id && count($product_category_list))
        {
            $model['id'] = $product_category_list[0]['id'];
        }

        foreach($product_category_list as $i=>$item)
        {
            $product_category_list[$i]['product_count'] = Xccmsproductinfo::field('id')
                ->where('company_id',$this->company_id)
                ->where('category_id', $item['id'])
                ->where('state', 1)
                ->count();
        }

        $product_list = Xccmsproductinfo::field('id,title,list_image,price,description')
            ->where('company_id',$this->company_id)
            ->where('category_id', $model['id'])
            ->where('state', 1)
            ->order('weigh desc')
            ->paginate(10,false,array('query'=>array()));

        $pagenav = $product_list->render();

        $this->view->assign('page_code', $this->page_code);
        $this->view->assign('page_id', $this->page_id);
        $this->view->assign('seo_title', $model['seo_title']);
        $this->view->assign('seo_keywords', $model['seo_keywords']);
        $this->view->assign('seo_description', $model['seo_description']);
        $this->view->assign('model', $model);
        $this->view->assign('product_category_list', $product_category_list);
        $this->view->assign('product_list', $product_list);
        $this->view->assign('pagenav', $pagenav);
        return $this->view->fetch($this->site_config['theme'] . '/index/product');
    }

    public function product_detail()
    {
        $id = input('id', 0);
        $this->page_code = 'product';
        $this->page_id = $id;
        if (!$id)
        {
            $this->error('id不能为空');
        }

        $model = Xccmsproductinfo::field('id,category_id,title,description,banners,summary,price,content,visits,seo_title,seo_keywords,seo_description,createtime')
            ->where('company_id',$this->company_id)
            ->where('id', $id)
            ->where('state', 1)
            ->find();
        if (!$model)
        {
            $this->error('没有找到内容');
        }
        $model['banners_list'] = $model['banners'] ? explode(',', $model['banners']) : [];
        $model['seo_title'] = $model['seo_title'] ? $model['seo_title'] : $model['title'];

        //产品参数
        $model['summary_json'] = json_decode($model['summary'], true);

        $this->add_visits('product', $model['id'], $model['visits']);

        $this->view->assign('page_code', $this->page_code);
        $this->view->assign('page_id', $this->page_id);
        $this->view->assign('seo_title', $model['seo_title']);
        $this->view->assign('seo_keywords', $model['seo_keywords']);
        $this->view->assign('seo_description', $model['seo_description']);
        $this->view->assign('model', $model);
        return $this->view->fetch($this->site_config['theme'] . '/index/product_detail');
    }

    public function page()
    {
        $id = input('id', 0);

        if (!$id)
        {
            $this->error('id不能为空');
        }

        $model = Xccmspageinfo::field('id,title,content,visits,seo_title,seo_keywords,seo_description,createtime')
            ->where('company_id',$this->company_id)
            ->where('id', $id)
            ->where('state', 1)
            ->find();

        if (!$model)
        {
            $this->error('没有找到内容');
        }

        $model['seo_title'] = $model['seo_title'] ? $model['seo_title'] : $model['title'];

        $this->add_visits('page', $model['id'], $model['visits']);

        $this->view->assign('page_code', $this->page_code);
        $this->view->assign('page_id', $this->page_id);
        $this->view->assign('seo_title', $model['seo_title']);
        $this->view->assign('seo_keywords', $model['seo_keywords']);
        $this->view->assign('seo_description', $model['seo_description']);
        $this->view->assign('model', $model);
        return $this->view->fetch($this->site_config['theme'] . '/index/page');
    }

    public function partner()
    {
        $this->page_code = 'partner';
        $partner_list = Xccmspartnerlink::field('id,name,url,list_image')
            ->where('company_id',$this->company_id)
            ->where('state', 1)
            ->order('weigh desc')
            ->select();

        $this->view->assign('page_code', $this->page_code);
        $this->view->assign('page_id', $this->page_id);
        $this->view->assign('seo_title', '合作伙伴 - ' . $this->site_config['seo_title']);
        $this->view->assign('seo_keywords', '合作伙伴 - ' . $this->site_config['seo_keywords']);
        $this->view->assign('seo_description', '合作伙伴 - ' . $this->site_config['seo_description']);
        $this->view->assign('partner_list', $partner_list);
        return $this->view->fetch($this->site_config['theme'] . '/index/partner');
    }

    public function about_us()
    {
        $this->page_code = 'aboutus';
        $this->view->assign('page_code', $this->page_code);
        $this->view->assign('page_id', $this->page_id);
        $this->view->assign('seo_title', '关于我们 - ' . $this->site_config['seo_title']);
        $this->view->assign('seo_keywords', '关于我们 - ' . $this->site_config['seo_keywords']);
        $this->view->assign('seo_description', '关于我们 - ' . $this->site_config['seo_description']);
        return $this->view->fetch($this->site_config['theme'] . '/index/about_us');
    }

    public function contact_us()
    {
        $this->page_code = 'contactus';
        $this->view->assign('page_code', $this->page_code);
        $this->view->assign('page_id', $this->page_id);
        $this->view->assign('seo_title', '联系我们 - ' . $this->site_config['seo_title']);
        $this->view->assign('seo_keywords', '联系我们 - ' . $this->site_config['seo_keywords']);
        $this->view->assign('seo_description', '联系我们 - ' . $this->site_config['seo_description']);
        return $this->view->fetch($this->site_config['theme'] . '/index/contact_us');
    }

    public function job()
    {
        $this->page_code = 'job';
        $page = input('page', 1);

        $job_list = Xccmsjobinfo::field('id,name,list_image,description,createtime')
            ->where('company_id',$this->company_id)
            ->where('state', 1)
            ->order('weigh desc, id desc')
            ->paginate(10,false,array('query'=>array()));

        $pagenav = $job_list->render();

        $this->view->assign('page_code', $this->page_code);
        $this->view->assign('page_id', $this->page_id);
        $this->view->assign('seo_title', '在线招聘 - ' . $this->site_config['seo_title']);
        $this->view->assign('seo_keywords', '在线招聘 - ' . $this->site_config['seo_keywords']);
        $this->view->assign('seo_description', '在线招聘 - ' . $this->site_config['seo_description']);
        $this->view->assign('job_list', $job_list);
        $this->view->assign('pagenav', $pagenav);
        return $this->view->fetch($this->site_config['theme'] . '/index/job');
    }

    public function job_detail()
    {
        $id = input('id', 0);
        $this->page_code = 'job';
        $this->page_id = $id;
        if (!$id)
        {
            $this->error('id不能为空');
        }

        $model = Xccmsjobinfo::field('id,name,content,visits,createtime')
            ->where('company_id',$this->company_id)
            ->where('id', $id)
            ->where('state', 1)
            ->find();

        if (!$model)
        {
            $this->error('没有找到内容');
        }

        $this->add_visits('job', $model['id'], $model['visits']);

        $prev_model = Xccmsjobinfo::field('id,name')
            ->where('company_id',$this->company_id)
            ->where('id', '<', $id)->where('state', 1)->order('weigh desc, id desc')->find();
        $next_model = Xccmsjobinfo::field('id,name')
            ->where('company_id',$this->company_id)
            ->where('id', '>', $id)->where('state', 1)->order('weigh desc')->find();

        $this->view->assign('page_code', $this->page_code);
        $this->view->assign('page_id', $this->page_id);
        $this->view->assign('seo_title', '招聘 - ' . $model['name'] . ' - ' . $this->site_config['name']);
        $this->view->assign('seo_keywords', '招聘 - ' . $model['name'] . ' - ' . $this->site_config['name']);
        $this->view->assign('seo_description', '招聘 - ' . $model['name'] . ' - ' . $this->site_config['name']);
        $this->view->assign('model', $model);
        $this->view->assign('prev_model', $prev_model);
        $this->view->assign('next_model', $next_model);
        return $this->view->fetch($this->site_config['theme'] . '/index/job_detail');
    }

    public function guestbook()
    {
        $this->page_code = input('page_code', 'index');
        $this->page_id = input('id', 0);


        $this->view->assign('seo_title', $this->site_config['guestbook_title'] . ' - ' . $this->site_config['name']);
        $this->view->assign('seo_keywords', $this->site_config['guestbook_title'] . ' - ' . $this->site_config['name']);
        $this->view->assign('seo_description', $this->site_config['guestbook_description'] . ' - ' . $this->site_config['name']);
        $this->view->assign('page_code', $this->page_code);
        $this->view->assign('page_id', $this->page_id);
        return $this->view->fetch($this->site_config['theme'] . '/index/guestbook');
    }

    public function faq()
    {
        $this->page_code = input('page_code', 'faq');
        $this->page_id = input('id', 0);

        $faq_list = Xccmsfaq::field('id,question,answer')
            ->where('company_id',$this->company_id)
            ->where('state', 1)
            ->order('weigh desc, id desc')
            ->select();

        $this->view->assign('seo_title', 'FAQ - ' . $this->site_config['name']);
        $this->view->assign('seo_keywords', 'FAQ - ' . $this->site_config['name']);
        $this->view->assign('seo_description', 'FAQ - ' . $this->site_config['name']);
        $this->view->assign('page_code', $this->page_code);
        $this->view->assign('page_id', $this->page_id);
        $this->view->assign('faq_list', $faq_list);
        return $this->view->fetch($this->site_config['theme'] . '/index/faq');
    }

    public function guestbook_add()
    {
        if ($this->request->isPost())
        {
            $page_code_config = ['index', 'product', 'info', 'page', 'news', 'aboutus', 'contactus', 'partner', 'job'];

            // $token = $this->request->post('__token__');
            $page_code = input('page_code', '');
            $res_id = input('page_id', 0);
            $realname = input('realname', '');
            $tel = input('tel', '');
            $email = input('email', '');
            $content = input('content', '');

            $rule = [
                // '__token__' => 'require|token',
            ];
            $data = [
                // '__token__' => $token,
            ];

            $rule['captcha'] = 'require|captcha';
            $data['captcha'] = $this->request->post('captcha');

            $validate = new Validate($rule, [], ['captcha' => __('验证码')]);
            $result = $validate->check($data);

            $url = $this->request->get('url', addon_url('xccms/index/' . $page_code));
            switch($page_code)
            {
                case 'info':
                    $url = $this->request->get('url', addon_url('xccms/index/info_detail', [':id'=>$res_id]));
                    break;
                case 'product':
                    $url = $this->request->get('url', addon_url('xccms/index/product_detail', [':id'=>$res_id]));
                    break;
                case 'page':
                    $url = $this->request->get('url', addon_url('xccms/index/page_detail', [':id'=>$res_id]));
                    break;
                case 'news':
                    $url = $this->request->get('url', addon_url('xccms/index/news_detail', [':id'=>$res_id]));
                    break;
                case 'job':
                    $url = $this->request->get('url', addon_url('xccms/index/job_detail', [':id'=>$res_id]));
                    break;
                case 'aboutus':
                    $url = addon_url('xccms/index/about_us');
                    break;
                case 'contactus':
                    $url = addon_url('xccms/index/contact_us');
                    break;
                case 'partner':
                    $url = addon_url('xccms/index/partner');
                    break;
            }

            if (!$result) {
                $this->error($validate->getError(), $url, ['token' => $this->request->token()]);
            }

            if (!$realname || !$tel || !$content)
            {
                $this->error('参数不能为空');
            }

            if (!in_array($page_code, $page_code_config))
            {
                $this->error('参数错误');
            }

            Xccmsguestbook::insert([
                'company_id' => $this->company_id,
                'guest_book_type'=>$page_code == 'info' ? 'content' : $page_code,
                'resource_id'=>$res_id,
                'realname'=>$realname,
                'tel'=>$tel,
                'email'=>$email,
                'content'=>$content,
                'state'=>0,
                'createtime'=>time()
            ]);

            $this->success('提交成功');
        }
        $this->error('参数不能为空');
    }

    private function add_visits($t, $id, $visits)
    {
        // $ip = request()->ip();
        $key = md5('visits_info_' . $t . '_' . $id);
        if (!cookie($key))
        {
            $visits++;

            switch($t)
            {
                case 'info':
                    Xccmscontentinfo::where('id', $id)->update([
                        'visits'=>$visits
                    ]);
                    break;
                case 'product':
                    Xccmsproductinfo::where('id', $id)->update([
                        'visits'=>$visits
                    ]);
                    break;
                case 'page':
                    Xccmspageinfo::where('id', $id)->update([
                        'visits'=>$visits
                    ]);
                    break;
                case 'news':
                    Xccmsnewsinfo::where('id', $id)->update([
                        'visits'=>$visits
                    ]);
                    break;
                case 'job':
                    Xccmsjobinfo::where('id', $id)->update([
                        'visits'=>$visits
                    ]);
                    break;
            }
            cookie($key, '1', 3600);
        }
    }

    //载入主题扩展
    private function load_theme_ext($theme)
    {
        $config = get_addon_config('xccms');

        $config_theme_ext_theme = null;
        if (isset($config['theme_ext'][$theme]))
        {
            //$config_theme_ext_theme = json_decode($config['theme_ext'][$theme], true);
            $config_theme_ext_theme = ThemeService::getThemeText($this->company_id, $theme);
        }

        $this->view->assign('theme_ext', $config_theme_ext_theme);
    }

    //检查网站状态，关闭就跳转
    private function check_site_status($site_status)
    {
        if ($site_status == 0)
        {
            if ($this->request->action() != 'closed')
            {
                header('Location:' . addon_url('xccms/index/closed'));
                exit;
            }
        }
        else
        {
            if ($this->request->action() == 'closed')
            {
                header('Location:' . addon_url('xccms/index/index'));
                exit;
            }
        }
    }
}
