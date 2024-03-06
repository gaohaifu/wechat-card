<?php

namespace app\api\controller\smartcard;
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
     * @param string $type 代表分类id
     */
    public function staffData($staff_id=0,$user_id=0)
    {    
          $where['A.id']=$staff_id;
          $Company = new Company();
          $login_id = $user_id;
          $data['usertype'] = 0;
          if($login_id){
            $editpower = $Company
              ->where('FIND_IN_SET('.$login_id.',administrators_ids)')
              ->select();
            if($editpower){
              $data['usertype'] = 1;
              }
          }

           $staffInfo=[];
           if($staff_id==0){
            //如果员工id为空，默认显示当前登录用户对应的名片信息
              if($user_id!=0){
                $staff_id=$this->staffModel
                          ->where('FIND_IN_SET('.$user_id.',user_id)')
                          ->value('id');

              }else{
                $this->error('未登录且无员工ID');
              }

            }
            $info=[];
            $newsTime='';
            if($staff_id!=0){
               $staffInfo=$this->myData($staff_id);
  			       $company_id=$staffInfo['company_id'];
               $info= $this->companyModel->where('id',$staffInfo['company_id'])->find();
               $newsTime=$this->newsModel->where('company_id',$staffInfo['company_id'])->order('updatetime desc')->value('updatetime');
               if($newsTime){
                  $newsTime=date('Y-m-d H:i:s',$newsTime);
               }
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

               }
            if($staffInfo){
              //访问类型:1=访问员工主页,2=访问企业主页,3=访问企业宣传册,4=访问案例,5=查看企业商品,6=查看企业动态,7=点赞员工,8=点赞其他备用,9=点赞其他备用2
              $isFavor=0;
              //如果有登录信息，就记录一条登录记录
              if($user_id){
                 $wheres=[
                        'staff_id'=>$staff_id,
                        'user_id'=>$user_id,
                        'typedata'=>1,
                      ];

                 $datam=$this->visitorsModel->where($wheres)->find();
                 if(!is_null($datam)){
                   $wheres['createtime']=time();
                    //增加一条访问记录
                    $res=$this->visitorsModel->save($wheres);
                  } 
                  $findFavor=$this->visitorsModel->where(['user_id'=>$user_id,'staff_id'=>$staff_id,'typedata'=>7])->find();

                  if(!is_null($findFavor)){
                      $isFavor=1;
                  }
               }  
			         //
               $visitStaffNum=$this->visitorsModel
                               ->alias('A')
                               ->join('user B','A.user_id=B.id')
                               ->where(['A.staff_id'=>$staff_id,'A.typedata'=>'1'])
                               ->group('A.user_id')
                               ->field('A.user_id')
                               ->count();
				
                $visitStaffLists=$this->visitorsModel
                               ->alias('A')
                               ->join('user B','A.user_id=B.id')
                               ->where(['A.staff_id'=>$staff_id,'A.typedata'=>'1'])
                               ->field('A.user_id')
                               ->field("GROUP_CONCAT(concat_ws('#',A.id,A.user_id,A.id,A.createtime,B.avatar) separator'|') AS group_combo")
                               ->group('A.user_id')
                               ->limit(10)
                               //->order('A.createtime','desc')
                               ->select();
                $visitCompanyNum=$this->visitorsModel
                               ->alias('A')
                               ->join('user B','A.user_id=B.id')
                               ->where(['A.typedata'=>2])
                               ->field('A.user_id')
                               ->group('A.user_id')
                               ->count();
                $visitCompanyLists=$this->visitorsModel
                               ->alias('A')
                               ->join('user B','A.user_id=B.id')
                               ->where(['A.typedata'=>2,'A.company_id'=>$company_id])
                               ->field('A.user_id')
                               ->field("GROUP_CONCAT(concat_ws('#',A.id,A.user_id,A.id,A.createtime,B.avatar) separator'|') AS group_combo")
                               ->group('A.user_id')
                               ->limit(10)
                               //->order('A.createtime','desc')
                               ->select();


                 //访问企业宣传册
                 $visitDesignNum=$this->visitorsModel
                               ->alias('A')
                               ->join('user B','A.user_id=B.id')
                               ->field('A.user_id')
                               ->group('A.user_id')
                               ->where(['A.typedata'=>3,'A.company_id'=>$company_id])
                               ->count();
                 $designNum=$this->designModel
                               ->where('company_id',$company_id)
                               ->count();
                 $visitDesignLists=$this->visitorsModel
                               ->alias('A')
                               ->join('user B','A.user_id=B.id')
                               ->where(['A.typedata'=>3,'A.company_id'=>$company_id])
                               ->group('A.user_id')
                               ->field('A.user_id')
                               ->field("GROUP_CONCAT(concat_ws('#',A.id,A.user_id,A.id,A.createtime,B.avatar) separator'|') AS group_combo")
                               ->limit(10)
                               //->order('A.createtime','desc')
                               ->select();
                  //访问企业案例
                 $visitCasesNum=$this->visitorsModel
                               ->alias('A')
                               ->join('user B','A.user_id=B.id')
                               ->field('A.user_id')
                               ->group('A.user_id')
                               ->where(['A.typedata'=>4,'A.company_id'=>$company_id])
                               ->count();
                 $casesNum=$this->casesModel
                               ->where('company_id',$company_id)
                               ->count();
                 $visitCasesLists=$this->visitorsModel
                               ->alias('A')
                               ->join('user B','A.user_id=B.id')
                               ->where(['A.typedata'=>4,'A.company_id'=>$company_id])
                               ->group('A.user_id')
                               ->field('A.user_id')
                               ->field("GROUP_CONCAT(concat_ws('#',A.id,A.user_id,A.id,A.createtime,B.avatar) separator'|') AS group_combo")
                               ->limit(10)
                               //->order('A.createtime','desc')
                               ->select();
                  //访问类型:1=访问员工主页,2=访问企业主页,3=访问企业宣传册,4=访问案例,5=查看企业商品,6=查看企业动态,7=点赞员工,8=点赞其他备用,9=点赞其他备用2
                  //查看企业商品
                 $visitGoodsNum=$this->visitorsModel
                               ->alias('A')
                               ->join('user B','A.user_id=B.id')
                               ->where(['A.typedata'=>5,'A.company_id'=>$company_id])
                               ->field('A.user_id')
                               ->group('A.user_id')
                               ->count();
                 $goodsNum=$this->goodsModel
                               ->where('company_id',$company_id)
                               ->count();
                 $visitGoodsLists=$this->visitorsModel
                               ->alias('A')
                               ->join('user B','A.user_id=B.id')
                               ->where(['A.typedata'=>5,'A.company_id'=>$company_id])
                               ->group('A.user_id')
                               ->field('A.user_id')
                               ->field("GROUP_CONCAT(concat_ws('#',A.id,A.user_id,A.id,A.createtime,B.avatar) separator'|') AS group_combo")
                               ->limit(10)
                               //->order('A.createtime','desc')
                               ->select();
                   //查看企业动态
                 $visitCompanyNewsNum=$this->visitorsModel
                               ->alias('A')
                               ->join('user B','A.user_id=B.id')
                               ->where(['A.typedata'=>6,'A.company_id'=>$company_id])
                               ->field('A.user_id')
                               ->group('A.user_id')
                               ->count();
                 $companyNewsNum=$this->newsModel
                               ->where('company_id',$company_id)
                               ->count();
                 $visitCompanyNewsLists=$this->visitorsModel
                               ->alias('A')
                               ->join('user B','A.user_id=B.id')
                               ->where(['A.typedata'=>6,'A.company_id'=>$company_id])
                               ->group('A.user_id')
                               ->field('A.user_id')
                               ->field("GROUP_CONCAT(concat_ws('#',A.id,A.user_id,A.id,A.createtime,B.avatar) separator'|') AS group_combo")
                               ->limit(10)
                               //->order('A.createtime','desc')
                               ->select();
                   //点赞员工
                 $favorStaffNum=$this->visitorsModel
                               ->alias('A')
                               ->join('user B','A.user_id=B.id')
                               ->where(['A.staff_id'=>$staffInfo['id'],'A.typedata'=>7])
                               ->field('A.user_id')
                               ->group('A.user_id')
                               ->count();
                 $favorStaffLists=$this->visitorsModel
                               ->alias('A')
                               ->join('user B','A.user_id=B.id')
                               ->where(['A.staff_id'=>$staffInfo['id'],'A.typedata'=>7])
                               ->group('A.user_id')
                               ->field('A.user_id')
                               ->field("GROUP_CONCAT(concat_ws('#',A.id,A.user_id,A.id,A.createtime,B.avatar) separator'|') AS group_combo")
                               ->limit(10)
                               //->order('A.createtime','desc')
                               ->select();
                $tagsWhere=[
                  'statusdata'=>1,
                  'typedata'=>1
                ];
                $tagsWheres['staff_id']=$staff_id;
                $tagsNum=$this->tagsModel
                             ->where($tagsWhere)
                             ->whereOr($tagsWheres)
                             ->count();
                $tagsLists=$this->tagsModel
                             ->where($tagsWhere)
                             ->whereOr($tagsWheres)
                             ->order('isrecommend asc,typedata asc')
                             ->limit(3)
                             ->select();
                $themeWhere['id'] = $login_id;
                $userInfo = $this->userModel
              						  ->where($themeWhere)
              						  ->find();
              	$mystaffInfo_id=$this->staffModel
                        	->where('user_id',$login_id)
                        	->value('id');
                if($userInfo){
                	$userInfo['staff_id']=$mystaffInfo_id;
                }
                $data['staffInfo']=$staffInfo;//员工基本信息
                $data['visitStaffNum']=$visitStaffNum;//访问员工主页数量
                $data['visitStaffLists']=$visitStaffLists;//访问员工主页人员的记录信息，最多10条返回
                $data['visitCompanyNum']=$visitCompanyNum;//访问公司主页人数
                $data['visitCompanyLists']=$visitCompanyLists;//访问公司主页人员信息列表
                $data['visitDesignNum']=$visitDesignNum;//访问公司宣传册数量
                $data['designNum']=$designNum;//公司宣传册数量
                $data['visitDesignLists']=$visitDesignLists;//访问公司宣传册人员记录
                $data['visitCasesNum']=$visitCasesNum;//访问公司案例人数
                $data['casesNum']=$casesNum;//公司案例数量
                $data['visitCasesLists']=$visitCasesLists;//访问公司案例人员列表
                $data['visitGoodsNum']=$visitGoodsNum;//访问公司产品数量
                $data['goodsNum']=$goodsNum;//公司产品数量
                $data['visitGoodsLists']=$visitGoodsLists;//访问公司产品数量人员列表
                $data['visitCompanyNewsNum']=$visitCompanyNum;//访问公司动态数量
                $data['companyNewsNum']=$companyNewsNum;//公司动态数量
                $data['visitCompanyNewsLists']=$visitCompanyLists;//访问公司动态人员列表
                $data['favorStaffNum']=$favorStaffNum;//点赞员工的数量
                $data['favorStaffLists']=$favorStaffLists;//点赞员工的人员列表（最多10个）
                $data['isFavor']=$isFavor;
                $data['tagsNum']=$tagsNum;
                $data['tagsLists']=$tagsLists;
                $data['companyInfo'] = $info;
                $data['userInfo'] = $userInfo;
                $data['newsTime']=$newsTime;

                return $data;
              }else{
                $this->error('没有该员工信息');
              }             

          }else{
                $this->error('没有该员工信息');
          }
           

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
    public function myData($staff_id=0)
    { 
          if(!empty($staff_id)){
            $where['staff.id']=$staff_id;
            $staffInfo=$this->staffModel
                       ->with(['smartcardcompany','user','smartcardtheme'])
                       ->where($where)
                      //->field('A.*,A.name as realname,A.picimages as avatarimage,B.nickname,B.avatar,C.id as company_id,D.id as theme_id,D.name as theme_name')
                       ->find();
              if(!is_null($staffInfo)){
                $staffInfo['picimages']=explode(',',$staffInfo['picimages']);
		       if($staffInfo['picimages'][0]==''){
		       	$staffInfo['picimages']=[];
		       }
		       $staffInfo['videofiles']=explode(',',$staffInfo['videofiles']);
		       if($staffInfo['videofiles'][0]==''){
		       	$staffInfo['videofiles']=[];
		       }
                if($this->is_url($staffInfo['user']['avatar'])==0){
                   $staffInfo['user']['avatar']=letter_avatar($staffInfo['user']['avatar']);
                   $staffInfo['avatar']=letter_avatar($staffInfo['avatar']);
                  }
                 if($this->is_url($staffInfo['user']['avatar'])==1){
                   $staffInfo['avatar']=$staffInfo['user']['avatar'];
                  }
                if($this->is_url($staffInfo['user']['avatar'])==2){
                    $staffInfo['avatar']=$this->serverImgHost.$staffInfo['user']['avatar'];
                    //$staffInfo['avatar']=letter_avatar($staffInfo['avatar']);
                  }
              }
          return $staffInfo;
          }else{
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
