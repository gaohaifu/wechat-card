<?php

namespace app\admin\controller\xccms;

use app\common\controller\Backend;
use app\common\service\ThemeService;
use think\Db;
use app\admin\library\xccms\Service;
use app\admin\model\Xccmsconfig;
use app\admin\model\Xccmsmenuinfo;
use app\admin\model\Xccmswebsitecarousel;
use app\admin\model\Xccmsproductcategory;
use app\admin\model\Xccmsproductinfo;
use app\admin\model\Xccmscontentcategory;
use app\admin\model\Xccmscontentinfo;
use app\admin\model\Xccmspageinfo;

/**
 * 站点配置
 *
 * @icon fa fa-circle-o
 */
class Xccmssiteconfig extends Backend
{
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Xccmssiteconfig;
    }

    /**
     * 查看
     */
    public function index()
    {
        $where['company_id'] = $this->auth->company_id;
        if (false === $this->request->isPost()) {
            $theme_list = Xccmsconfig::selectpage('theme');

            $privacy_cookie_policy = '';
            //初始化站点配置
            $data = [
                'name'=>'XC企业建站',
                'icp'=>'测ICP备88888888号',
                'logo'=>'/assets/addons/xccms/img/test-logo.jpg',
                'logo_bottom'=>'/assets/addons/xccms/img/test-logo.jpg',
                'videofiles'=>'/uploads/20240309/f58033bd750bf2df36819471118b1188.mp4',
                'tel'=>'400-XXXX-XXXX',
                'email'=>'xccms@example.com',
                'address'=>'测试通信地址',
                'center_point'=>'',
                'desc_image'=>'/assets/addons/xccms/img/test-img.png',
                'description'=>'XXX网络科技股份有限公司成立于19xx年xx月。20xx年xx月，公司股票在深交所挂牌上市，是XX第一家网络科技类上市公司。上市xx年来，通过转增股本、增发扩股等方式，公司总股本已增至8.6亿股，已累计成交客户1.8万人次，实现收入xx亿元，现金分红xx亿元。现有下属企业xx家，其中分公司xx家、全资公司xx家、控股子公司xx家、参股公司xx家。截止20xx年底，在职职工xx人。总资产xx亿元，净资产xx亿元。',
                'summary'=>'XXX网络科技股份有限公司成立于19xx年xx月。20xx年xx月，公司股票在深交所挂牌上市，是XX第一家网络科技类上市公司。上市xx年来，通过转增股本、增发扩股等方式，公司总股本已增至8.6亿股，已累计成交客户1.8万人次，实现收入xx亿元，现金分红xx亿元。现有下属企业xx家，其中分公司xx家、全资公司xx家、控股子公司xx家、参股公司xx家。截止20xx年底，在职职工xx人。总资产xx亿元，净资产xx亿元。',
                'seo_title'=>'XC企业建站',
                'seo_keywords'=>'',
                'seo_description'=>'',
                'theme'=>'theme1',
                'analysis_code'=>'',
                'privacy_cookie_policy'=>$privacy_cookie_policy,
                'map_ak'=>'',
                'site_status'=>1,
                'site_closed_desciption'=>'抱歉！站点维护已关闭，请稍后访问。',
                'site_grayscale'=>0,        //0=关闭灰度，1=首页灰度，2=全站灰度
                'guestbook_title'=>'反馈留言',
                'guestbook_description'=>'感谢您的关注，请填写一下您的信息，我们会尽快和您联系。',
            ];
            $m = $this->model->where($where)->find();
            if (!$m)
            {
                $this->model->insert([
                    'company_id' => $this->auth->company_id,
                    'json_data'=>json_encode($data),
                    'updatetime'=>time(),
                    'updatedby'=>$this->auth->id
                ]);
            }
            else
            {
                $data_new = json_decode($m['json_data'], true);
                foreach($data as $key=>$value)
                {
                    if (!isset($data_new[$key]))
                    {
                        $data_new[$key] = $data[$key];
                    }
                }
                $data = $data_new;
            }


            //初始化一个站点菜单
            if (Xccmsmenuinfo::where($where)->count() == 0)
            {
                $temp_data = [
                    ['company_id' => $this->auth->company_id,'parent_id'=>0, 'name'=>'首页', 'en_name'=>'HOME', 'menu_type'=>'index', 'menu_object_id'=>0, 'url'=>'', 'is_top_show'=>1, 'is_bottom_show'=>0, 'state'=>1, 'weigh'=>99, 'createtime'=>time(), 'creator'=>0, 'updatetime'=>time(), 'updatedby'=>0],
                    ['company_id' => $this->auth->company_id,'parent_id'=>0, 'name'=>'新闻资讯', 'en_name'=>'NEWS', 'menu_type'=>'news', 'menu_object_id'=>0, 'url'=>'', 'is_top_show'=>1, 'is_bottom_show'=>0, 'state'=>1, 'weigh'=>98, 'createtime'=>time(), 'creator'=>0, 'updatetime'=>time(), 'updatedby'=>0],
                    ['company_id' => $this->auth->company_id,'parent_id'=>0, 'name'=>'关于我们', 'en_name'=>'ABOUT', 'menu_type'=>'aboutus', 'menu_object_id'=>0, 'url'=>'', 'is_top_show'=>0, 'is_bottom_show'=>1, 'state'=>1, 'weigh'=>97, 'createtime'=>time(), 'creator'=>0, 'updatetime'=>time(), 'updatedby'=>0],
                    ['company_id' => $this->auth->company_id,'parent_id'=>0, 'name'=>'联系我们', 'en_name'=>'CONTACT', 'menu_type'=>'contactus', 'menu_object_id'=>0, 'url'=>'', 'is_top_show'=>0, 'is_bottom_show'=>1, 'state'=>1, 'weigh'=>96, 'createtime'=>time(), 'creator'=>0, 'updatetime'=>time(), 'updatedby'=>0],
                    ['company_id' => $this->auth->company_id,'parent_id'=>0, 'name'=>'合作伙伴', 'en_name'=>'PARTNER', 'menu_type'=>'partner', 'menu_object_id'=>0, 'url'=>'', 'is_top_show'=>1, 'is_bottom_show'=>1, 'state'=>1, 'weigh'=>95, 'createtime'=>time(), 'creator'=>0, 'updatetime'=>time(), 'updatedby'=>0],
                    ['company_id' => $this->auth->company_id,'parent_id'=>0, 'name'=>'在线招聘', 'en_name'=>'JOB', 'menu_type'=>'job', 'menu_object_id'=>0, 'url'=>'', 'is_top_show'=>1, 'is_bottom_show'=>1, 'state'=>1, 'weigh'=>94, 'createtime'=>time(), 'creator'=>0, 'updatetime'=>time(), 'updatedby'=>0],
                    ['company_id' => $this->auth->company_id,'parent_id'=>0, 'name'=>'FAQ', 'en_name'=>'FAQ', 'menu_type'=>'faq', 'menu_object_id'=>0, 'url'=>'', 'is_top_show'=>0, 'is_bottom_show'=>1, 'state'=>1, 'weigh'=>93, 'createtime'=>time(), 'creator'=>0, 'updatetime'=>time(), 'updatedby'=>0],
                ];
                Xccmsmenuinfo::insertAll($temp_data);

                $this->add_menu_product_category();
                $this->add_menu_content_category();
                $this->add_menu_page();
            }

            //初始化轮播
            if (Xccmswebsitecarousel::where($where)->count() == 0)
            {
                $temp_data = [
                    ['company_id' => $this->auth->company_id,'title'=>'演示轮播1', 'list_image'=>'/assets/addons/xccms/img/test-carousel.png', 'weigh'=>10, 'state'=>1, 'createtime'=>time(), 'creator'=>0, 'updatetime'=>time(), 'updatedby'=>0],
                    ['company_id' => $this->auth->company_id,'title'=>'演示轮播2', 'list_image'=>'/assets/addons/xccms/img/test-carousel.png', 'weigh'=>9, 'state'=>1, 'createtime'=>time(), 'creator'=>0, 'updatetime'=>time(), 'updatedby'=>0]
                ];
                Xccmswebsitecarousel::insertAll($temp_data);
            }


            $this->view->assign('theme_list', $theme_list);
            $this->view->assign('json_data', $data);
            return $this->view->fetch();
        }

        $m = $this->model->where($where)->find();
        $id = $m['id'];
        $json_data = json_decode($m['json_data'], true);

        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);

        $name = $params['name'];
        $logo = $params['logo'];
        $logo_bottom = $params['logo_bottom'];
        $icp = $params['icp'];
        $tel = $params['tel'];
        $email = $params['email'];
        $address = $params['address'];
        $desc_image = $params['desc_image'];
        $description = $params['description'];
        $summary = $params['summary'];
        $seo_title = $params['seo_title'];
        $seo_keywords = $params['seo_keywords'];
        $seo_description = $params['seo_description'];
        $theme = $json_data['theme'];
        $analysis_code = $params['analysis_code'];
        $map_ak = $params['map_ak'];
        $site_status = $params['site_status'];
        $site_closed_desciption = $params['site_closed_desciption'];
        $site_grayscale = $params['site_grayscale'];
        $guestbook_title = $params['guestbook_title'];
        $guestbook_description = $params['guestbook_description'];

        $data = [
            'name'=>$name,
            'logo'=>$params['videofiles']??'',
            'videofiles'=>$logo,
            'logo_bottom'=>$logo_bottom,
            'icp'=>$icp,
            'tel'=>$tel,
            'email'=>$email,
            'address'=>$address,
            'desc_image'=>$desc_image,
            'description'=>$description,
            'summary'=>$summary,
            'seo_title'=>$seo_title,
            'seo_keywords'=>$seo_keywords,
            'seo_description'=>$seo_description,
            'theme'=>$theme,
            'analysis_code'=>$analysis_code,
            'map_ak'=>$map_ak,
            'site_status'=>$site_status,
            'site_closed_desciption'=>$site_closed_desciption,
            'site_grayscale'=>$site_grayscale,
            'guestbook_title'=>$guestbook_title,
            'guestbook_description'=>$guestbook_description,
        ];

        $this->model->where('id', $id)->update([
            'json_data'=>json_encode($data),
            'updatetime'=>time(),
            'updatedby'=>$this->auth->id
        ]);

        $this->success();
    }

    /**
     * 配置百度AK
     */
    public function ak()
    {
        if (false === $this->request->isPost()) {
            $m = $this->model->find();
            $id = $m['id'];
            $json_data = json_decode($m['json_data'], true);

            $this->view->assign('json_data', $json_data);
            return $this->view->fetch();
        }

        $m = $this->model->find();
        $id = $m['id'];
        $json_data = json_decode($m['json_data'], true);

        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);

        $json_data['map_ak'] = $params['ak'];

        $this->model->where('id', $id)->update([
            'json_data'=>json_encode($json_data),
            'updatetime'=>time(),
            'updatedby'=>$this->auth->id
        ]);

        $this->success();
    }

    /**
     * 查找地图
     */
    public function map()
    {
        if (false === $this->request->isPost()) {
            $center_point = input('center_point', '');

            $this->view->assign('center_point', $center_point);
            return $this->view->fetch();
        }

        $center_point = input('center_point', '');
        if ($center_point)
        {
            $m = $this->model->find();
            $id = $m['id'];
            $json_data = json_decode($m['json_data'], true);
            $json_data['center_point'] = $center_point;

            $this->model->where('id', $id)->update([
                'json_data'=>json_encode($json_data),
                'updatetime'=>time(),
                'updatedby'=>$this->auth->id
            ]);
            $this->success();
        }

        $this->error();
    }

    /**
     * 设置站点主题
     */
    public function edit_theme()
    {
        if (true === $this->request->isPost()) {
            $params = $this->request->post('row/a');
            if (empty($params)) {
                $this->error(__('Parameter %s can not be empty', ''));
            }
            $params = $this->preExcludeFields($params);

            $theme = $params['theme'];

            $m = $this->model->where('company_id',COMPANY_ID)->find();

            if (!$m)
            {
                $this->error(__('Parameter %s can not be empty', ''));
            }
            $id = $m['id'];
            $data = json_decode($m['json_data'], true);
            $data['theme'] = $theme;

            $this->model->where('id', $id)->update([
                'json_data'=>json_encode($data),
                'updatetime'=>time(),
                'updatedby'=>$this->auth->id
            ]);

            $this->success('设置成功');
        }
    }
    /**
     * 扩展配置Theme
     */
    public function set_theme_ext(){
        $themeName = input('t_name','theme1');
        if (false === $this->request->isPost()) {
            //$config = get_addon_config('xccms');
            //$config_theme_ext_theme = json_decode($config['theme_ext'][$themeName], true);
            $config_theme_ext_theme = ThemeService::getThemeText(COMPANY_ID, $themeName);

            if (!isset($config_theme_ext_theme['aboutus']))
            {
                $config_theme_ext_theme['aboutus'] = ['status'=>0, 'bg_image'=>''];
            }

            if (!isset($config_theme_ext_theme['datareport']))
            {
                $config_theme_ext_theme['datareport'] = ['status'=>0, 'title'=>'', 'sub_title'=>'', 'rows'=>''];
            }
            if (!isset($config_theme_ext_theme['services']))
            {
                $config_theme_ext_theme['services'] = ['status'=>0, 'title'=>'', 'sub_title'=>'', 'description'=>'', 'rows'=>'', 'url'=>''];
            }


            $this->view->assign('config_ext', $config_theme_ext_theme);
            return $this->view->fetch('set_'.$themeName.'_ext');
        }

        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);

        $datareport_status = $params['datareport_status'];
        $datareport = $params['datareport'];
        $services_status = $params['services_status'];
        $services_title = $params['services_title'];
        $services_data = $params['services_data'];

        $data = [
            'datareport'=>[
                'status'=>$datareport_status,
                'rows'=>$datareport
            ],
            'services'=>[
                'status'=>$services_status,
                'title'=>$services_title,
                'rows'=>$services_data
            ]
        ];

        $config = $config = get_addon_config('xccms');
        $config['theme_ext'][$themeName] = json_encode($data);
        set_addon_config('xccms', $config, true);

        $this->success();
    }

    /**
     * 扩展配置Theme1
     */
    public function set_theme1_ext()
    {
        if (false === $this->request->isPost()) {
            /*$config = get_addon_config('xccms');
            $config_theme_ext_theme1 = json_decode($config['theme_ext']['theme1'], true);*/
            $config_theme_ext_theme1 = ThemeService::getThemeText(COMPANY_ID, 'theme1');

            if (!isset($config_theme_ext_theme1['datareport']))
            {
                $config_theme_ext_theme1['datareport'] = ['status'=>0, 'rows'=>[]];
            }
            if (!isset($config_theme_ext_theme1['services']))
            {
                $config_theme_ext_theme1['services'] = ['status'=>0, 'title'=>'', 'rows'=>[]];
            }


            $this->view->assign('config_ext', $config_theme_ext_theme1);
            return $this->view->fetch();
        }

        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);

        $datareport_status = $params['datareport_status'];
        $datareport = $params['datareport'];
        $services_status = $params['services_status'];
        $services_title = $params['services_title'];
        $services_data = $params['services_data'];

        $data = [
            'datareport'=>[
                'status'=>$datareport_status,
                'rows'=>$datareport
            ],
            'services'=>[
                'status'=>$services_status,
                'title'=>$services_title,
                'rows'=>$services_data
            ]
        ];

        /*$config = $config = get_addon_config('xccms');
        $config['theme_ext']['theme1'] = json_encode($data);
        set_addon_config('xccms', $config, true);*/
        ThemeService::setThemeText(COMPANY_ID, 'theme1', json_encode($data));

        $this->success();
    }

    /**
     * 扩展配置Theme2
     */
    public function set_theme2_ext()
    {
        if (false === $this->request->isPost()) {
            /*$config = get_addon_config('xccms');
            $config_theme_ext_theme2 = json_decode($config['theme_ext']['theme2'], true);*/
            $config_theme_ext_theme2 = ThemeService::getThemeText(COMPANY_ID, 'theme2');

            if (!isset($config_theme_ext_theme2['datareport']))
            {
                $config_theme_ext_theme2['datareport'] = ['status'=>0, 'rows'=>[]];
            }
            if (!isset($config_theme_ext_theme2['services']))
            {
                $config_theme_ext_theme2['services'] = ['status'=>0, 'title'=>'', 'rows'=>[]];
            }


            $this->view->assign('config_ext', $config_theme_ext_theme2);
            return $this->view->fetch();
        }

        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);

        $datareport_status = $params['datareport_status'];
        $datareport = $params['datareport'];
        $services_status = $params['services_status'];
        $services_title = $params['services_title'];
        $services_data = $params['services_data'];

        $data = [
            'datareport'=>[
                'status'=>$datareport_status,
                'rows'=>$datareport
            ],
            'services'=>[
                'status'=>$services_status,
                'title'=>$services_title,
                'rows'=>$services_data
            ]
        ];

        /*$config = $config = get_addon_config('xccms');
        $config['theme_ext']['theme2'] = json_encode($data);
        set_addon_config('xccms', $config, true);*/

        ThemeService::setThemeText(COMPANY_ID, 'theme2', json_encode($data));

        $this->success();
    }

    /**
     * 扩展配置Theme3
     */
    public function set_theme3_ext()
    {
        if (false === $this->request->isPost()) {

            /*$config = get_addon_config('xccms');
            $config_theme_ext_theme3 = json_decode($config['theme_ext']['theme3'], true);*/
            $config_theme_ext_theme3 = ThemeService::getThemeText(COMPANY_ID, 'theme3');

            if (!isset($config_theme_ext_theme3['aboutus']))
            {
                $config_theme_ext_theme3['aboutus'] = ['status'=>0, 'aboutus_bg_image'=>''];
            }
            if (!isset($config_theme_ext_theme3['datareport']))
            {
                $config_theme_ext_theme3['datareport'] = ['status'=>0, 'title'=>'', 'sub_title'=>'', 'rows'=>''];
            }
            if (!isset($config_theme_ext_theme3['services']))
            {
                $config_theme_ext_theme3['services'] = ['status'=>0, 'title'=>'', 'sub_title'=>'', 'description'=>'', 'rows'=>''];
            }

            $config_theme_ext_theme3['services'] = $this->fill_not_exists_key($config_theme_ext_theme3['services'], ['title', 'sub_title', 'description', 'url']);

            $this->view->assign('config_ext', $config_theme_ext_theme3);
            return $this->view->fetch();
        }

        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);

        $aboutus_status = $params['aboutus_status'];
        $aboutus_bg_image = $params['aboutus_bg_image'];

        $datareport_status = $params['datareport_status'];
        $datareport_title = $params['datareport_title'];
        $datareport_sub_title = $params['datareport_sub_title'];
        $datareport = $params['datareport'];

        $services_status = $params['services_status'];
        $services_title = $params['services_title'];
        $services_sub_title = $params['services_sub_title'];
        $services_url = $params['services_url'];
        $services_description = $params['services_description'];
        $services_data = $params['services_data'];

        $data = [
            'aboutus'=>[
                'status'=>$aboutus_status,
                'bg_image'=>$aboutus_bg_image
            ],
            'datareport'=>[
                'status'=>$datareport_status,
                'title'=>$datareport_title,
                'sub_title'=>$datareport_sub_title,
                'rows'=>$datareport
            ],
            'services'=>[
                'status'=>$services_status,
                'title'=>$services_title,
                'url'=>$services_url,
                'sub_title'=>$services_sub_title,
                'description'=>$services_description,
                'rows'=>$services_data
            ]
        ];

        /*$config = $config = get_addon_config('xccms');
        $config['theme_ext']['theme3'] = json_encode($data);
        set_addon_config('xccms', $config, true);*/

        ThemeService::setThemeText(COMPANY_ID, 'theme3', json_encode($data));
        $this->success();
    }

    /**
     * 扩展配置imitate
     */
    public function set_imitate_ext()
    {
        if (false === $this->request->isPost()) {

            /*$config = get_addon_config('xccms');
            $config_theme_ext_theme3 = json_decode($config['theme_ext']['theme3'], true);*/
            $config_theme_ext_theme3 = ThemeService::getThemeText(COMPANY_ID, 'theme3');

            if (!isset($config_theme_ext_theme3['aboutus']))
            {
                $config_theme_ext_theme3['aboutus'] = ['status'=>0, 'aboutus_bg_image'=>''];
            }
            if (!isset($config_theme_ext_theme3['datareport']))
            {
                $config_theme_ext_theme3['datareport'] = ['status'=>0, 'title'=>'', 'sub_title'=>'', 'rows'=>''];
            }
            if (!isset($config_theme_ext_theme3['services']))
            {
                $config_theme_ext_theme3['services'] = ['status'=>0, 'title'=>'', 'sub_title'=>'', 'description'=>'', 'rows'=>''];
            }

            $config_theme_ext_theme3['services'] = $this->fill_not_exists_key($config_theme_ext_theme3['services'], ['title', 'sub_title', 'description', 'url']);

            $this->view->assign('config_ext', $config_theme_ext_theme3);
            return $this->view->fetch();
        }

        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);

        $aboutus_status = $params['aboutus_status'];
        $aboutus_bg_image = $params['aboutus_bg_image'];

        $datareport_status = $params['datareport_status'];
        $datareport_title = $params['datareport_title'];
        $datareport_sub_title = $params['datareport_sub_title'];
        $datareport = $params['datareport'];

        $services_status = $params['services_status'];
        $services_title = $params['services_title'];
        $services_sub_title = $params['services_sub_title'];
        $services_url = $params['services_url'];
        $services_description = $params['services_description'];
        $services_data = $params['services_data'];

        $data = [
            'aboutus'=>[
                'status'=>$aboutus_status,
                'bg_image'=>$aboutus_bg_image
            ],
            'datareport'=>[
                'status'=>$datareport_status,
                'title'=>$datareport_title,
                'sub_title'=>$datareport_sub_title,
                'rows'=>$datareport
            ],
            'services'=>[
                'status'=>$services_status,
                'title'=>$services_title,
                'url'=>$services_url,
                'sub_title'=>$services_sub_title,
                'description'=>$services_description,
                'rows'=>$services_data
            ]
        ];

        $config = $config = get_addon_config('xccms');
        $config['theme_ext']['theme3'] = json_encode($data);
        set_addon_config('xccms', $config, true);

        $this->success();
    }

    //初始化一个产品分类和一个推荐产品,并添加到菜单中作为演示
    private function add_menu_product_category()
    {
        $category_id = Xccmsproductcategory::insertGetId([
            'company_id' => $this->auth->company_id,
            'parent_id'=>0,
            'name'=>'产品分类1',
            'is_recommend'=>1,
            'weigh'=>1,
            'state'=>1,
            'createtime'=>time(),
            'creator'=>0,
            'updatetime'=>time(),
            'updatedby'=>0
        ]);
        Xccmsproductinfo::insert([
            'company_id' => $this->auth->company_id,
            'category_id'=>$category_id,
            'title'=>'产品1',
            'is_recommend'=>1,
            'list_image'=>'/assets/addons/xccms/img/test-img.png',
            'banners'=>'/assets/addons/xccms/img/test-img.png',
            'price'=>0,
            'description'=>'产品描述',
            'visits'=>1,
            'weigh'=>1,
            'state'=>1,
            'createtime'=>time(),
            'creator'=>0,
            'updatetime'=>time(),
            'updatedby'=>0
        ]);

        $parent_menu_id = Xccmsmenuinfo::insertGetId(['company_id' => $this->auth->company_id,'parent_id'=>0, 'name'=>'产品中心', 'en_name'=>'PRODUCT', 'menu_type'=>'link', 'menu_object_id'=>0, 'url'=>'', 'is_top_show'=>1, 'is_bottom_show'=>0, 'state'=>1, 'weigh'=>93, 'createtime'=>time(), 'creator'=>0, 'updatetime'=>time(), 'updatedby'=>0]);
        Xccmsmenuinfo::insert(['company_id' => $this->auth->company_id,'parent_id'=>$parent_menu_id, 'name'=>'产品分类1', 'en_name'=>'', 'menu_type'=>'product', 'menu_object_id'=>$category_id, 'url'=>'', 'is_top_show'=>1, 'is_bottom_show'=>0, 'state'=>1, 'weigh'=>99, 'createtime'=>time(), 'creator'=>0, 'updatetime'=>time(), 'updatedby'=>0]);

    }

    //初始化一个内容分类和一个内容信息,并添加到菜单中作为演示
    private function add_menu_content_category()
    {
        $category_id = Xccmscontentcategory::insertGetId([
            'company_id' => $this->auth->company_id,
            'parent_id'=>0,
            'name'=>'项目案例',
            'is_recommend'=>1,
            'weigh'=>1,
            'state'=>1,
            'createtime'=>time(),
            'creator'=>0,
            'updatetime'=>time(),
            'updatedby'=>0
        ]);
        Xccmscontentinfo::insert([
            'company_id' => $this->auth->company_id,
            'category_id'=>$category_id,
            'title'=>'内容1',
            'is_recommend'=>1,
            'list_image'=>'/assets/addons/xccms/img/test-img.png',
            'description'=>'内容描述',
            'visits'=>1,
            'weigh'=>1,
            'state'=>1,
            'createtime'=>time(),
            'creator'=>0,
            'updatetime'=>time(),
            'updatedby'=>0
        ]);

        $parent_menu_id = Xccmsmenuinfo::insertGetId(['company_id' => $this->auth->company_id,'parent_id'=>0, 'name'=>'内容中心', 'en_name'=>'CONTENT', 'menu_type'=>'link', 'menu_object_id'=>0, 'url'=>'', 'is_top_show'=>1, 'is_bottom_show'=>0, 'state'=>1, 'weigh'=>92, 'createtime'=>time(), 'creator'=>0, 'updatetime'=>time(), 'updatedby'=>0]);
        Xccmsmenuinfo::insert(['company_id' => $this->auth->company_id,'parent_id'=>$parent_menu_id, 'name'=>'内容分类1', 'en_name'=>'', 'menu_type'=>'content', 'menu_object_id'=>$category_id, 'url'=>'', 'is_top_show'=>1, 'is_bottom_show'=>0, 'state'=>1, 'weigh'=>99, 'createtime'=>time(), 'creator'=>0, 'updatetime'=>time(), 'updatedby'=>0]);
    }

    //初始化一个单页信息,并添加到菜单中作为演示
    private function add_menu_page()
    {
        $page_info_id = Xccmspageinfo::insertGetId([
            'company_id' => $this->auth->company_id,
            'title'=>'单页内容',
            'content'=>'单页内容',
            'visits'=>1,
            'state'=>1,
            'createtime'=>time(),
            'creator'=>0,
            'updatetime'=>time(),
            'updatedby'=>0
        ]);
        Xccmsmenuinfo::insert(['company_id' => $this->auth->company_id,'parent_id'=>0, 'name'=>'单页内容', 'en_name'=>'PAGE', 'menu_type'=>'page', 'menu_object_id'=>$page_info_id, 'url'=>'', 'is_top_show'=>1, 'is_bottom_show'=>0, 'state'=>1, 'weigh'=>91, 'createtime'=>time(), 'creator'=>0, 'updatetime'=>time(), 'updatedby'=>0]);
    }

    //填补缺失的键
    private function fill_not_exists_key($data, $config_keys)
    {
        foreach($config_keys as $key)
        {
            if (!isset($data[$key]))
            {
                $data[$key] = '';
            }
        }

        return $data;
    }
}
