<?php

namespace app\api\controller\smartcard;
use addons\myadmin\model\Domain;
use app\admin\model\smartcard\Su;
use app\admin\model\Xccmsmenuinfo;
use app\admin\model\Xccmssiteconfig;
use app\common\controller\Api;
use app\admin\model\smartcard\Category;
use app\admin\model\smartcard\Company;
use app\admin\model\smartcard\Design;
use app\admin\model\smartcard\Cases;
use app\admin\model\smartcard\Goods;
use app\admin\model\smartcard\Staff;
use app\admin\model\smartcard\News;
use app\admin\model\smartcard\Visitors;
use app\admin\model\smartcard\Tags;
use app\admin\model\smartcard\Favor;
use app\admin\model\smartcard\Theme;
use app\admin\model\User;
use think\Db;
use think\Config;

header('Access-Control-Allow-Origin:*');//允许跨域
/*if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header('Access-Control-Allow-Headers:x-requested-with,content-type,token');
    //浏览器页面ajax跨域请求会请求2次，第一次会发送OPTIONS预请求,不进行处理，直接exit返回，但因为下次发送真正的请求头部有带token
    //所以这里设置允许下次请求头带token否者下次请求无法成功
    exit();
}*/

/**
 * 基本类，初始化请求
 * Class Base
 * @package app\api\controller\moyi
 */

class Base extends Api
{

    protected $noNeedLogin = ['staffData','myData','myCompanyInfoData','departInfoData','getAccessToken','isExpires','areaJson','areaOrderABCJson'];
    protected $noNeedRight = ['*'];
    public function _initialize()
    {
        parent::_initialize();
        $this->categoryModel=new Category;
        $this->companyModel=new \addons\myadmin\model\Company;
        $this->designModel=new Design;
        $this->casesModel=new Cases;
        $this->goodsModel=new Goods;
        $this->staffModel=new Staff;
        $this->newsModel=new News;
        $this->visitorsModel=new Visitors;
        $this->tagsModel=new Tags;
        $this->favorModel=new Favor;
        $this->userModel=new User;
        $this->themeModel=new Theme;
        $this->user_id = $this->auth->id;
        $this->serverImgHost=$this->is_https()?"https://".$_SERVER['HTTP_HOST']:"http://".$_SERVER['HTTP_HOST'];
    }


    /**
     * 获取首页数据
     *
     * @param string $type 代表分类id 0=首页 1=首页-分享
     */
    public function staffData($staff_id = 0, $user_id = 0, int $type = 0,$origin=0)
    {
        $data['usertype'] = 0;
        $data['save_status'] = 0;
//        if($user_id){
//            $editpower = $this->companyModel
//                ->where('id',$user_id)
//                ->select();
//            if($editpower){
//                $data['usertype'] = 1;
//            }
//        }
        $staffInfo = [];
        if ($staff_id == 0) {
            //如果员工id为空，默认显示当前登录用户对应的名片信息
            if ($user_id != 0) {
                $staff_id = $this->staffModel->where(['user_id' => $user_id,'is_default' => 1])->value('id');
            } else {
                $this->error('未登录且无员工ID');
            }
        }
        if ($staff_id != 0) {
            $staffInfo  = $this->myData($staff_id);
            if ($staffInfo) {
                $data['staffInfo']             = $staffInfo;//员工基本信息

                //判断是否需要企业认证
                if($staffInfo['company_id']==0 || $staffInfo['smartcardcompany']['is_authentication']==3 || $staffInfo['smartcardcompany']['is_authentication']==0){
                    $data['usertype'] = 1;
                }

                //访问类型:1=访问员工主页,2=访问企业主页,3=访问企业宣传册,4=访问案例,5=查看企业商品,6=查看企业动态,7=点赞员工,8=点赞其他备用,9=点赞其他备用2
                //如果登录信息与名片用户不一致，就记录一条登录记录
                if ($user_id) {
                    if($type==1){
                        $su = Su::where(['user_id'=>$user_id,'staff_id'=>$staff_id])->find();
                        $data['save_status'] = 0;
                        if($su) $data['save_status'] = $su['status'];
                    }
                    if ($staffInfo['user_id']!=$user_id) {
                        if ($origin == 0) {
                            $this->error('访问来源不能为空');
                        }
                        $res = $this->visitorsModel->where(['user_id'=>$user_id,'staff_id'=>$staff_id])->find();
                        $is_first = 0;
                        if($res) $is_first = 1;
                        
                        $visitor = [
                            'staff_id' => $staff_id,
                            'user_id'  => $user_id,
                            'typedata' => 1,
                            'is_first' => $is_first,
                            'origin' => $origin,
                            'company_id' => $staffInfo['company_id'],
                            'createtime' => time(),
                        ];
                        //增加一条访问记录
                        $this->visitorsModel->save($visitor);
                    }
                }
                //首页
                if($type==0){
                    $staff_ids = $this->staffModel->where(['user_id' => $user_id])->column('id');
                    if(count($staff_ids)>1){
                        $wheredata = ['A.staff_id' => ['in',$staff_ids]];
                    }else{
                        $wheredata = ['A.staff_id' => $staff_id];
                    }
                    $allVisitNum = $this->visitorsModel
                        ->alias('A')
                        ->join('user B', 'A.user_id=B.id')
                        ->where($wheredata)
                        ->where(['A.typedata' => '1'])
//                    ->group('A.user_id')
                        ->field('A.user_id')
                        ->count();
                    $todayVisitNum = $this->visitorsModel
                        ->alias('A')
                        ->join('user B', 'A.user_id=B.id')
                        ->where($wheredata)
                        ->where(['A.typedata' => '1'])
                        ->whereTime('A.createtime', 'today')
//                    ->group('A.user_id')
                        ->field('A.user_id')
                        ->count();

                    $visitStaffLists   = $this->visitorsModel
                        ->alias('A')
                        ->join('user B', 'A.user_id=B.id')
                        ->where($wheredata)
                        ->where(['A.typedata' => '1'])
                        ->field('A.user_id,B.avatar,A.createtime,A.origin')
                        ->group('A.user_id')
                        ->limit(10)
                        ->order('A.createtime','desc')
                        ->select();
                    $Su = new Su();
                    foreach ($visitStaffLists as &$visitStaffList) {
                        $visitStaffList->avatar = cdnurl($visitStaffList->avatar,true);
                        $staff = $this->staffModel->with(['smartcardcompany' => function($query) {
                            $query->withField('id,name,address,longitude,latitude,is_authentication');
                        }])->where(['user_id' => $visitStaffList->user_id,'is_default' => 1])->find();
                        if($staff){
                            $visitStaffList->staff_id = $staff['id'];
                            $visitStaffList->name = $staff['name'];
                            $visitStaffList->position = $staff['position'];
                            $visitStaffList->company = $staff->smartcardcompany->name;
                        }else{
                            $visitStaffList->staff_id = null;
                            $visitStaffList->name = null;
                            $visitStaffList->position = null;
                            $visitStaffList->company = null;
                        }
                        $su = $Su->where(['user_id' => $visitStaffList->user_id,'staff_user_id'=>$user_id])->order('status desc')->find();
                        if($su){
                            if($su['status']==1){
                                $visitStaffList->status = 2;
                            }else{
                                $visitStaffList->status = 3;
                            }
                        }else{
                            $visitStaffList->status = 1;
                        }
                        $VisitNum = $this->visitorsModel
                            ->where(['staff_id' => $staff_id, 'typedata' => '1', 'user_id' => $visitStaffList->user_id])
                            ->count();
                        $visitStaffList['interviewee'] = [
                            'visitNum'=>$VisitNum,
                            'companyname'=>$staffInfo->smartcardcompany->name,
                            'position'=>$staffInfo->position,
                            'staff_id'=>$staffInfo->id,
                        ];
                    }
                    
                    $data['myCardData'] = [
                        'allVisitNum'=>$allVisitNum,
                        'todayVisitNum'=>$todayVisitNum,
                        'sendCardNum'=>$staffInfo['send_num'],
                    ];
                    $data['visitStaffLists']       = $visitStaffLists;//访问员工主页人员的记录信息，最多10条返回
                }
    
                if($type==1){
                    $data['services'] = $this->getMenu($staffInfo['company_id']);
                }
    
                $data['videofiles'] = [];
                $data['description'] = '';
                $siteconfig = Xccmssiteconfig::where(['company_id'=>$staffInfo['company_id']])->value('json_data');
                if($siteconfig){
                    $siteconfigData = json_decode($siteconfig, true);
                    $siteconfigData['videofiles'] = '/uploads/20240309/f58033bd750bf2df36819471118b1188.mp4,/uploads/20240309/46abcc6f406fb60ef78d1c0cab7a1dfd.mp4';
                    $videofiles=[];
                    if(isset($siteconfigData['videofiles'])){
                        $videofiles=explode(',',$siteconfigData['videofiles']);
                        if($videofiles){
                            foreach ($videofiles as &$videofile) {
                                $videofile = cdnurl($videofile,true);
                            }
            
                        }
                    }
                    $data['videofiles'] = $videofiles;
                    $data['description'] = $siteconfigData['description'];
                }
                return $data;
            } else {
                $this->error('没有该员工信息');
            }
            
        } else {
            $this->error('没有该员工信息');
        }
        
        
    }
    
    /**
     * 获取官网菜单
     */
    public function getMenu($company_id)
    {
//        $domain = Domain::where(['company_id'=>$company_id])->value('name');
        //菜单
        $main_menu_list = Xccmsmenuinfo::where(['company_id'=>$company_id])->field('id,parent_id,name as label,icon,en_name,menu_type,menu_object_id,url')
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
                    $main_menu_item_url = addon_url('xccms/index/index',[],true,true);
                    break;
                case 'partner':
                case 'job':
                    $main_menu_item_url = addon_url('xccms/index/' . $item['menu_type'],[],true,true);
                    break;
                case 'page':
                    $main_menu_item_url = addon_url('xccms/index/page', [':id'=>$item['menu_object_id']],true,true);
                    break;
                case 'news':
                    $main_menu_item_url = addon_url('xccms/index/news',[],true,true);
                    break;
                case 'link':
                    $main_menu_item_url = $item['url'];
                    break;
                case 'product':
                    $main_menu_item_url = addon_url('xccms/index/product', [':id'=>$item['menu_object_id']],true,true);
                    break;
                case 'content':
                    $main_menu_item_url = addon_url('xccms/index/info', [':id'=>$item['menu_object_id']],true,true);
                    break;
                case 'aboutus':
                    $main_menu_item_url = addon_url('xccms/index/about_us',[],true,true);
                    break;
                case 'contactus':
                    $main_menu_item_url = addon_url('xccms/index/contact_us',[],true,true);
                    break;
                case 'faq':
                    $main_menu_item_url = addon_url('xccms/index/faq',[],true,true);
                    break;
            }
            $sub_menu = Xccmsmenuinfo::where(['company_id'=>$company_id])->field('id,name,menu_type,menu_object_id,url')
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
                        $sub_menu_item_url = addon_url('xccms/index/index',[],true,true);
                        break;
                    case 'partner':
                    case 'job':
                        $sub_menu_item_url = addon_url('xccms/index/' . $sitem['menu_type'],[],true,true);
                        break;
                    case 'page':
                        $sub_menu_item_url = addon_url('xccms/index/page', [':id'=>$sitem['menu_object_id']],true,true);
                        break;
                    case 'news':
                        $sub_menu_item_url = addon_url('xccms/index/news',[],true,true);
                    case 'link':
                        $sub_menu_item_url = $item['url'];
                        break;
                    case 'product':
                        $sub_menu_item_url = addon_url('xccms/index/product', [':id'=>$sitem['menu_object_id']],true,true);
                        break;
                    case 'content':
                        $sub_menu_item_url = addon_url('xccms/index/info', [':id'=>$sitem['menu_object_id']],true,true);
                        break;
                    case 'aboutus':
                        $sub_menu_item_url = addon_url('xccms/index/about_us',[],true,true);
                        break;
                    case 'contactus':
                        $sub_menu_item_url = addon_url('xccms/index/contact_us',[],true,true);
                        break;
                    case 'faq':
                        $sub_menu_item_url = addon_url('xccms/index/faq',[],true,true);
                        break;
                }
                $sub_menu[$s]['url'] = $sub_menu_item_url;
            }
        
            $main_menu_item_url = count($sub_menu) > 0 ? $sub_menu[0]['url'] : $main_menu_item_url;
        
            $main_menu_list[$i]['url'] = $main_menu_item_url;
            $main_menu_list[$i]['sub_menu'] = $sub_menu;
        }
        return $main_menu_list;
    }
/**
* 企业具体基本信息
* @param string $staff_id  员工id
*
*/
 public function myCompanyInfoData($cid){
   $info=$this->companyModel->where('id',$cid)->find();
   if(!is_null($info)){
       $info['picimages']=explode(',',$info['picimages']);
       if($info['picimages'][0]==''){
       	$info['picimages']=[];
       }
       $info['videofiles']=explode(',',$info['videofiles']);
       if($info['videofiles'][0]==''){
       	$info['videofiles']=[];
       }
       $info['partner']=explode(',',$info['partner']);
       if($info['partner'][0]==''){
       	$info['partner']=[];
       }

             
    $this->success('成功',$info);
   }else{
     $this->error('查询失败');
   }
 }   
 
/**
     * 获取员工的具体数据
     * @param string $staff_id  员工id
     *
     */
    public function myData($staff_id = 0)
    {
        if (!empty($staff_id)) {
            $where['staff.id'] = $staff_id;
            $staffInfo         = $this->staffModel
                ->with(['smartcardcompany' => function($query) {
                    $query->withField('id,name,address,longitude,latitude,is_authentication');
                }, 'smartcardtheme' => function($query) {
                    $query->withField('id,colour,backgroundimage,name,cardimage,fontcolor');
                }])
                ->where($where)
                //->field('A.*,A.name as realname,A.picimages as avatarimage,B.nickname,B.avatar,C.id as company_id,D.id as theme_id,D.name as theme_name')
                ->find();
                if($staffInfo) $staffInfo=$staffInfo->hidden(['tags_ids','visit','favor','picimages','videofiles','updatetime','createtime','weigh']);
            if (!is_null($staffInfo)) {
                $user = \app\common\model\User::where(['id' =>$staffInfo->user_id])->find();
                $staffInfo['avatar'] = cdnurl($user['avatar'],true);
                $staffInfo['is_certified'] = $user['is_certified'];
                $staffInfo['picimages'] = explode(',', $staffInfo['picimages']);
                if ($staffInfo['picimages'][0] == '') {
                    $staffInfo['picimages'] = [];
                }
                $staffInfo['videofiles'] = explode(',', $staffInfo['videofiles']);
                if ($staffInfo['videofiles'][0] == '') {
                    $staffInfo['videofiles'] = [];
                }
            }
            return $staffInfo;
        } else {
            $this->error('参数错误');
        }
    }
	/**
	    *判断是不是网址
	     */
	 public function is_url($v){
	    $pattern="#(http|https)://(.*\.)?.*\..*#i";
	    //排除base64头像
	    if (strpos($v, 'data:image') === 0) {
	      return 1;//无需替换
	     }
	     if ($v=='') {
	      return 0;//空头像，需要替换字母
	     }
	    if(preg_match($pattern,$v)){ 
	      return 1;//网址形式 
	    }else{ 
	      return 2;//添加域名前缀  0替换字母，1无需变动，2加域名前缀 
	    } 
	}
/**
     * 获取企业关联具体数据
     * @param string $company_id  企业id
     *
     */
    public function companyData($company_id=0,$type=null,$page,$limit=10,$keywords='',$stafftype=0)
    {     
          $user_id=$this->user_id;
          if(!empty($company_id)){
            $where['company_id']=$company_id;
            if($type){
              if($type=='design'){
                $modeControler=$this->designModel;
              }else if($type=='goods'){
                $modeControler=$this->goodsModel;
              }else if($type=='news'){
                $modeControler=$this->newsModel;
              }else if($type=='staff'){
                $modeControler=$this->staffModel;
              }else if($type=='cases'){
                $modeControler=$this->casesModel;
              }else{
                $this->error('type参数错误');
              }
                
            }else{
              $this->error('type参数错误');
            }
            $companyInfo=$this->companyModel
                       ->where(['id'=>$company_id])
                       ->find();
            if($companyInfo){
                $companyInfo['picimages']=explode(',',$companyInfo['picimages']);
                $companyInfo['videofiles']=explode(',',$companyInfo['videofiles']);
              }
            $InfosNum=0;
            $Infos=[];
            if($companyInfo){
                 if(!empty($keywords) && $type=='goods'){
                     $where['name'] =  array('like','%'.$keywords.'%');
                  }
                  if($type == 'staff' && $stafftype == 1){
                    $where['statusdata'] = 1;
                  }
                  if($type == 'staff' && $stafftype == 2){
                    $where['statusdata'] = 2;
                  }
                  if($type == 'staff' && $stafftype == 3){
                    $where['statusdata'] = 3;
                  }
                   if($type == 'staff' && $stafftype == 4){
                    $where['statusdata'] = 4;
                  }
                 $InfosNum=$modeControler
                       ->where($where)
                       ->count();
                 if($type=='staff'){
                 	
                   $Infos=$modeControler
                		->with(['user'])
                       ->where($where)
                       ->order('createtime','desc')
                       ->page($page,$limit)
                       ->select();
                 }else{
                    $Infos=$modeControler
                       ->where($where)
                       ->order('createtime','desc')
                       ->page($page,$limit)
                       ->select();
                 }
                
                if($Infos){
                  foreach ($Infos as $key => $value) {
                     $Infos[$key]['updatetime']=date('Y-m-d H:i',$value['updatetime']);
                     $Infos[$key]['createtime']=date('Y-m-d H:i',$value['createtime']);
                     $Infos[$key]['picimages']=explode(',',$value['picimages']);
                     if($Infos[$key]['picimages'][0]==''){
                       	$Infos[$key]['picimages']=[];
                       }
                     if(isset($Infos[$key]['videofiles'])){
                       $Infos[$key]['videofiles']=explode(',',$value['videofiles']);
                       if($Infos[$key]['videofiles'][0]==''){
                       	$Infos[$key]['videofiles']=[];
                       }
                     }  
                     if($type=='news'){
                      $Infos[$key]['isfavor']=$this->visitorsModel->where(['user_id'=>$user_id,'company_id'=>$company_id,'news_id'=>$value['id'],'typedata'=>8])->count()>0?1:0;
                      $Infos[$key]['favorNum']=$this->visitorsModel->where(['company_id'=>$company_id,'news_id'=>$value['id'],'typedata'=>8])->count();
                     }
                  }
                }
            }  

            $data['companyInfo'] =$companyInfo;
            $data['InfosNum']=$InfosNum;
            $data['Infos']   =$Infos;
            return $data;
          }else{
            $this->error('参数错误');
          }     

    }
/**
* 企业各个模块的具体信息
* @param string $staff_id  员工id
*
*/
 public function departInfoData($company_id=0,$id=0,$type=''){
          if($company_id!=0){
              $where['company_id']=$company_id;
              $where['id']=$id;
              if($type){
                if($type=='design'){
                  $modeControler=$this->designModel;
                }else if($type=='goods'){
                  $modeControler=$this->goodsModel;
                }else if($type=='news'){
                  $modeControler=$this->newsModel;
                }else if($type=='staff'){
                  $modeControler=$this->staffModel;
                }else if($type=='cases'){
                  $modeControler=$this->casesModel;
                }else{
                  $this->error('type参数错误');
                }
                $companyInfo=$this->companyModel
                       ->where(['id'=>$company_id])
                       ->find();
                $Infos=[];
                $serverImgHost=$this->serverImgHost;
                if($companyInfo){
                    $companyInfo['picimages']=explode(',',$companyInfo['picimages']);
                    $companyInfo['videofiles']=explode(',',$companyInfo['videofiles']);
                    $companyInfo['content']=$this->replacePicUrl($companyInfo['content'],$serverImgHost);
                    $Infos=$modeControler
                           ->where($where)
                           ->find();
                    if($Infos){
                         $Infos['updatetime']=date('Y-m-d H:i',$Infos['updatetime']);
                         $Infos['createtime']=date('Y-m-d H:i',$Infos['createtime']);
                         $Infos['maincontent']=$this->replacePicUrl($Infos['maincontent'],$serverImgHost);
                         $Infos['picimages']=explode(',',$Infos['picimages']);
                         if(isset($Infos['videofiles'])){
                           $Infos['videofiles']=explode(',',$Infos['videofiles']);
                         } 
                    }
              }  

            $data['companyInfo'] =$companyInfo;
            $data['Infos']   =$Infos;
            return $data;
                  
              }else{
                $this->error('type参数错误');
              }
        }
 }

    /**
 * 替换fckedit中的图片 添加域名
 * @param  string $content 要替换的内容
 * @param  string $strUrl 内容中图片要加的域名
 * @return string 
 * @eg 
 */
function replacePicUrl($content = null, $strUrl = null) {
    if ($strUrl) {
        //提取图片路径的src的正则表达式 并把结果存入$matches中  
        preg_match_all("/<img(.*)src=\"([^\"]+)\"[^>]+>/isU",$content,$matches);
        $img = "";  
        if(!empty($matches)) {  
        //注意，上面的正则表达式说明src的值是放在数组的第三个中  
        $img = $matches[2];  
        }else {  
           $img = "";  
        }
          if (!empty($img)) {  
                $patterns= array();  
                $replacements = array();  
                //正则判断是否有域名前缀，没有才加上
                $preg = "/^http(s)?:\\/\\/.+/";
                
                foreach($img as $imgItem){  
                    if(preg_match($preg,$imgItem))
                    {
                       $final_imgUrl = $imgItem; 
                    }else
                    {
                       $final_imgUrl = $strUrl.$imgItem; 
                    }
                    //$final_imgUrl = $strUrl.$imgItem;  
                    $replacements[] = $final_imgUrl;  
                    $img_new = "/".preg_replace("/\//i","\/",$imgItem)."/";  
                    $patterns[] = $img_new;  
                }  
  
                //让数组按照key来排序  
                ksort($patterns);  
                ksort($replacements);  
  
                //替换内容  
                $vote_content = preg_replace($patterns, $replacements, $content);
        
                return $vote_content;
        }else {
            return $content;
        }                   
    } else {
        return $content;
    }
}

 function is_https() {
        if ( !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return true;
        } elseif ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
            return true;
        } elseif ( !empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return true;
        }else{
            return false;
        }
    }
/**
 * 获取标签数据
 * @param string $staff_id  员工id
 *
 */
    public function tagsLists($staff_id)
    {   
           $user_id=$this->user_id;
           //dump($staff_id);exit;
           $where=[
                  'statusdata'=>1,
                  'typedata'=>1
                  ];
           $wheres['staff_id']=$staff_id;
           //员工标签等于系统标签+个人标签
           $staffTags=$this->tagsModel
                       ->where($where)
                       ->whereOr($wheres)
                       ->select();
            if($staffTags){
            	$staffTags=collection($staffTags)->toArray();
            	
            }
                       
          foreach ($staffTags as $key => $value) {
              $staffTags[$key]['num']=$this->favorModel->where(['tags_id'=>$value['id'],'staff_id'=>$staff_id])->count();
              $staffTags[$key]['isFavor']=$this->favorModel->where(['tags_id'=>$value['id'],'staff_id'=>$staff_id,'user_id'=>$user_id])->count()>0?1:0;
              $staffTags[$key]['isdelAble']=$this->tagsModel->where(['id'=>$value['id'],'staff_id'=>$staff_id,'user_id'=>$user_id])->count()>0?1:0;

          }
          return $staffTags;    

    }
  /**
 * 新增tags
 * @param string $tag_name  标签名称
 *
 */
    public function tagsAdd($tag_name=null,$staff_id=0)
    {   
           $user_id=$this->user_id;
           if($staff_id==0 || $tag_name==null || $tag_name==''){
            $this->error('参数错误');
           }
           $where=[
                  'name'=>$tag_name,
                  'createtime'=>time(),
                  'updatetime'=>time(),
                  'staff_id'  =>$staff_id,
                  'user_id'   =>$user_id
                  ];
           //员工标签等于系统标签+个人标签
           $staffTags=$this->tagsModel->save($where);
           $tagids=$this->tagsModel->id;
           $wheres=[
                      'weigh'=>$tagids,
                      'id'   =>$tagids,
                      'updatetime'=>time()
                    ];
          //更新排序
          $res=$this->tagsModel->update($wheres,true);
          if($res){
            $wheret=[
                     'user_id'=>$user_id,
                    'staff_id'=>$staff_id,
                    'tags_id'=>$tagids,
                    'createtime'=>time() 
                    ];
           //新增标签增加一条点赞记录
           $ress=$this->favorModel->save($wheret); 
           if($ress){
            $this->success('添加成功');
           }else{
            $this->error('操作失败');
           }        
          }else{
           $this->error('操作失败');
          } 

    }
   /**
 * 删除自己新增的tags
 * @param string $tag_id  标签id
 *
 */
    public function tagsDel($tag_id=0)
    {   
           $user_id=$this->user_id;
           if($tag_id==0){
            $this->error('参数错误');
           }
           $where=[
                  'id'=>$tag_id,
                  'user_id'   =>$user_id
                  ];
           //员工标签--删除自己创建的
           $delTags=$this->tagsModel->where($where)->delete();
           
           if($delTags){
            $this->success('删除成功');
           }else{
            $this->error('删除失败');
           }         

    }
 /**
 * 标签点赞
 * @param string $staff_id  员工id
 *
 */
    public function tagsFavorOption($tags_id=0,$staff_id=0)
    {   
          $user_id=$this->user_id;
          if($staff_id==0 || $tags_id==0){
            $this->error('参数错误');
          }
         
          $where=[
                    'user_id'=>$user_id,
                    'staff_id'=>$staff_id,
                    'tags_id'=>$tags_id
                  ];
          $wheres=[
                    'staff_id'=>$staff_id,
                    'tags_id'=>$tags_id
                  ];
           //dump($where);exit;
           $data=$this->favorModel->where($where)->find();
           if($data){
            //有点赞数据就删除，取消点赞
                $cancelFavor=$this->favorModel->where($where)->delete();
                $favorCount = $this->favorModel->where($wheres)->count();
                $this->success("已取消点赞", ['favorNum' => $favorCount]);
            }else{
              //增加一条点赞记录
              $where['createtime']=time();
              $res=$this->favorModel->save($where);
              if($res){
                $favorCount = $this->favorModel->where(['staff_id'=>$staff_id,'tags_id'=>$tags_id])->count();
                $this->success("赞好啦！", ['favorNum' => $favorCount]);
              }else{
                $this->error('点赞失败！');
              }
            } 

    }
  /**
 * 企业动态点赞
 * @param string $staff_id  员工id
 *
 */
    public function visitorsOption($typedata='',$staff_id=0,$company_id=0,$news_id=0)
    {   
          $user_id=$this->user_id;
          if($staff_id==0 || $typedata=='' || $company_id==0 || $news_id==0){
            $this->error('参数错误');
          }
          $where=[
                    'user_id'=>$user_id,
                    'company_id'=>$company_id,
                    'news_id'=>$news_id,
                    'typedata'  =>$typedata
                  ];
          $wheres=[

                    'company_id'=>$company_id,
                    'news_id'=>$news_id,
                    'typedata'  =>$typedata
                  ];
           $data=$this->visitorsModel->where($where)->find();
           if($data){
            //有点赞数据就删除，取消点赞
                $cancelFavor=$this->visitorsModel->where($where)->delete();
                $favorCount = $this->visitorsModel->where($wheres)->count();
                $this->success("已取消点赞", ['favorNum' => $favorCount]);
            }else{
              //增加一条点赞记录
              $where['createtime']=time();
              $where['staff_id']=$staff_id;
              $res=$this->visitorsModel->save($where);
              if($res){
                $favorCount = $this->visitorsModel->where(['company_id'=>$company_id,'news_id'=>$news_id,'typedata'=>$typedata])->count();
                $this->success("赞好啦！", ['favorNum' => $favorCount]);
              }else{
                $this->error('点赞失败！');
              }
            } 

    }
    /**
 * 首页点--靠谱
 * @param string $staff_id  员工id
 *
 */
    public function favorOption($staff_id,$typedata='7')
    {   
          $user_id=$this->user_id;
          if($staff_id==0){
            $this->error('参数错误');
          }
          //访问类型:1=访问员工主页,2=访问企业主页,3=访问企业宣传册,4=访问案例,5=查看企业商品,6=查看企业动态,7=点赞员工,8=点赞其他备用,9=点赞其他备用2
          $where=[
                    'user_id'=>$user_id,
                    'staff_id'=>$staff_id,
                    'typedata'=>'7'
                  ];
          $company_id=$this->staffModel->where('id',$staff_id)->value('company_id');
          $wheres=[
                    'staff_id'=>$staff_id,
                    'typedata'=>'7'
                    ];
          if($company_id){
            $where['company_id']=$company_id;
          }
           $data=$this->visitorsModel->where($where)->find();
           if(!is_null($data)){
            //有点赞数据就删除，取消点赞
                $cancelFavor=$this->visitorsModel->where($where)->delete();
                $favorCount = $this->visitorsModel->where($wheres)->group('user_id')->select();
                $this->success("已取消点赞", ['favorNum' => count($favorCount),'list'=>$favorCount]);
            }else{
              //增加一条点赞记录
              //var_dump($where);exit;
              $res=$this->visitorsModel->save($where);
              if($res){
                $favorCount = $this->visitorsModel->where($wheres)->group('user_id')->select();
                $this->success("赞好啦！", ['favorNum' => count($favorCount),'list'=>$favorCount]);
              }else{
                $this->error('点赞失败！');
              }
            } 

    }

/**
把用户输入的文本转义（主要针对特殊符号和emoji表情）
*/
public function userTextEncode($str){
  if(!is_string($str)) return $str;
  if(!$str || $str='undefined') return '';
  $text=json_encode($str);//暴露出unicode
  $text = preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i",function($str){
    var_dump($str);exit;
    return addslashes($str[0]);
  },$text);

  //将emoji的unicode留下，其他不动，这里的正则比原答案增加了d，因为我发现我很多emoji实际上是\ud开头的，反而暂时没发现有\ue开头。
  return json_decode($text);
}
/**
解码上面的转义
*/
public function userTextDecode($str){
  $text=json_decode($str);
  $text=preg_replace_callback('/\\\\\\\\/i',function($str){
    return '\\';
  },$text);
  return json_decode($text);
}
 

    /**
     * 腾讯地图crul
     * 
     * 
     */
    public function http_curl($url){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        if($output === FALSE ){
            echo "CURL Error:".curl_error($ch);
        }
        curl_close($ch);
        return $output;
    }
    
    
//主动判断是否HTTPS
  function isHTTPS()
  {
      if (defined('HTTPS') && HTTPS) return true;
      if (!isset($_SERVER)) return FALSE;
      if (!isset($_SERVER['HTTPS'])) return FALSE;
      if ($_SERVER['HTTPS'] === 1) {  //Apache
          return TRUE;
      } elseif ($_SERVER['HTTPS'] === 'on') { //IIS
          return TRUE;
      } elseif ($_SERVER['SERVER_PORT'] == 443) { //其他
          return TRUE;
      }
      return FALSE;
    }
/**
 *输出省市区按照ABC排序json
 * 
 *
 */
public function areaOrderABCJson(){
  $province=Db::name('area')->where('pid',0)->select();
  $area=[];
  foreach ($province as $key => $value) {
    $area[$key]['code']=$value['zip'];
    $area[$key]['name']=$value['name'];
    //循环市
    $city=Db::name('area')->where('pid',$value['id'])->select();
    foreach ($city as $k => $v) {
      $area[$key]['code']=$city[0]['zip'];
      $area[$key]['first']=$city[0]['first'];
      $area[$key]['cityList'][$k]['code']=$v['zip'];
      $area[$key]['cityList'][$k]['name']=$v['name'];
      $arealist=Db::name('area')->where('pid',$v['id'])->select();
      foreach ($arealist as $kk => $vv) {
        $area[$key]['cityList'][$k]['areaList'][$kk]['code']=$vv['zip'];
        $area[$key]['cityList'][$k]['areaList'][$kk]['name']=$vv['name'];
      }
    }
  }
}

/**
 *输出省市区json
 * 
 *
 */
public function areaJson(){
  $province=Db::name('area')->where('pid',0)->select();
  $area=[];
  foreach ($province as $key => $value) {
    $area[$key]['code']=$value['zip'];
    $area[$key]['name']=$value['name'];
    //循环市
    $city=Db::name('area')->where('pid',$value['id'])->select();
    foreach ($city as $k => $v) {
      $area[$key]['code']=$city[0]['zip'];
      $area[$key]['first']=$city[0]['first'];
      $area[$key]['cityList'][$k]['code']=$v['zip'];
      $area[$key]['cityList'][$k]['name']=$v['name'];
      $arealist=Db::name('area')->where('pid',$v['id'])->select();
      foreach ($arealist as $kk => $vv) {
        $area[$key]['cityList'][$k]['areaList'][$kk]['code']=$vv['zip'];
        $area[$key]['cityList'][$k]['areaList'][$kk]['name']=$vv['name'];
      }
    }
  }
  $this->success('返回成功', $area);
}
  
   /*
     *  @param $saveWhere ：想要更新主键ID数组
     *  @param $saveData    ：想要更新的ID数组所对应的数据
     *  @param $tableName  : 想要更新的表明
     *  @param $saveWhere  : 返回更新成功后的主键ID数组
     * */
    public function saveAll($saveWhere,$saveData,$tableName){
        if($saveWhere==null||$tableName==null)
            return false;
        //获取更新的主键id名称
        $key = array_keys($saveWhere)[0];
        //获取更新列表的长度
        $len = count($saveWhere[$key]);
        $flag=true;
        // isset($model)?$model:
        $model =Db::name($tableName);
        //开启事务处理机制
        $model->startTrans();
        //记录更新失败ID
        $error=[];
        for($i=0;$i<$len;$i++){
            //预处理sql语句
            $isRight=$model->where($key.'='.$saveWhere[$key][$i])->update($saveData[$i]);
            if($isRight==0){
                //将更新失败的记录下来
                $error[]=$i;
                $flag=false;
            }
            //$flag=$flag&&$isRight;
        }
        if($flag ){
            //如果都成立就提交
            $model->commit();
            return $saveWhere;
        }elseif(count($error)>0&count($error)<$len){
            //先将原先的预处理进行回滚
            $model->rollback();
            for($i=0;$i<count($error);$i++){
                //删除更新失败的ID和Data
                unset($saveWhere[$key][$error[$i]]);
                unset($saveData[$error[$i]]);
            }
            //重新将数组下标进行排序
            $saveWhere[$key]=array_merge($saveWhere[$key]);
            $saveData=array_merge($saveData);
            //进行第二次递归更新
            $this->saveAll($saveWhere,$saveData,$tableName);
            return $saveWhere;
        }
        else{
            //如果都更新就回滚
            $model->rollback();
            return false;
        }
    }
}
