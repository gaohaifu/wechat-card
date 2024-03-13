<?php

namespace app\api\controller\smartcard;
use app\admin\model\smartcard\Su;
use app\admin\model\smartcard\Visitors;
use app\common\controller\Api;
use app\api\controller\smartcard\Base;
use app\admin\model\smartcard\Goods;
use app\admin\model\smartcard\Cases;
use app\admin\model\smartcard\Staff;
use app\admin\model\smartcard\Company;
use app\admin\model\smartcard\Message;
use app\admin\model\smartcard\Theme;

use app\admin\model\obo\Commodity;
use app\admin\model\obo\Commodityclass;
use app\admin\model\obo\Myfollow;
use app\admin\model\obo\Myrelease;
use app\admin\model\obo\Order;
use app\admin\model\obo\Orderdetails;
use app\admin\model\obo\Shop;


use app\common\model\User;

use fast\Tree;
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
 * @package app\api\controller\coupon
 */

class Common extends Base
{

    protected $noNeedLogin = ['index','indexShare','staffInfo','smartcardfind','myCompanyInfo','departInfo','showCommentLists','locationData','companyStaffAdd','companyInfo','themeEdit'];
    protected $noNeedRight = ['*'];
    public function _initialize()
    {
        parent::_initialize();
        $this->user_id = $this->auth->id;
    }


     /**
     * 获取首页数据
     * @param string $staff_id     员工id

     */
    public function index()
    {   
        $staff_id = $this->request->request("staff_id")?$this->request->request("staff_id"):0;
        $user_id = $this->request->request("user_id");
        $origin = $this->request->request("origin");
  
        $list = $this->staffData($staff_id,$user_id,0,$origin);
        $this->success('请求成功', $list);
    }
    
    /**
     * 获取分享首页数据
     * @param string $staff_id     员工id
     
     */
    public function indexShare()
    {
        $staff_id = $this->request->request("staff_id")?$this->request->request("staff_id"):0;
        $user_id = $this->request->request("user_id");
        $origin = $this->request->request("origin");
        
        $list = $this->staffData($staff_id,$user_id,1,$origin);
        $this->success('请求成功', $list);
    }
    
    /**
     * 发名片回调
     * @param string $staff_id
     *
     */
    public function sendCard()
    {
        $staff_id = $this->request->request('staff_id')?$this->request->request('staff_id'):'';
        $Staff = new Staff();
        if($staff_id == ''){
            $this->error('staff_id不能为空');
        }else{
            $res = $Staff->where('id', $staff_id)->setInc('send_num');
            if($res!==false){
                //更新成功
                $this->success('发送成功');
            }else{
                //更新失败
                $this->error('发送失败');
            }
        }
    }
    
    /**
     * 保存名片
     * @param string $staff_id
     * @param string $user_id
     *
     */
    public function saveCard()
    {
        $staff_id = $this->request->request('staff_id')?$this->request->request('staff_id'):'';
        $user_id = $this->request->request("user_id");
        $self_staff_id = $this->request->request("self_staff_id")?:0;

        $Staff = new Staff();
        if($staff_id == ''){
            $this->error('staff_id不能为空');
        }else{
            $staff = $Staff->where('id', $staff_id)->find();
            if($staff){
                $su = Su::where(['user_id'=>$user_id,'staff_id'=>$staff_id])->find();
                if($su){
                    $this->error('名片已保存');
                }else{
                    $Su = new Su();
                    $staffdata = $Su->alias('s')->join('smartcard_staff f', 's.staff_id=f.id')->
                    where(['s.user_id'=>$staff['user_id'],
                           'f.user_id'=>$user_id,
                           'f.is_default'=>1,
                           's.status'=>['in',[2,3]],
                    ])->find();
                    if(!$self_staff_id){
                        $self_staff_id = $Staff->where(['user_id'=>$user_id,'is_default'=>1])->value('id');
                    }
                    if($staffdata){
                        $staffdata->status = 4;
                        $staffdata->save();
                        $Su->save([
                            'user_id'=>$user_id,
                            'self_staff_id'=>$self_staff_id,
                            'staff_id'=>$staff_id,
                            'status'=>4,
                            'staff_user_id'=>$staff['user_id'],
                        ]);
                    }else{
                        $Su->save([
                            'user_id'=>$user_id,
                            'self_staff_id'=>$self_staff_id,
                            'staff_id'=>$staff_id,
                            'status'=>2,
                            'staff_user_id'=>$staff['user_id'],
                        ]);
                    }
                }
                $this->success('名片保存成功');
            }else{
                $this->error('名片不存在');
            }
        }
    }
    
    /**
     * 回递名片
     * @param string $staff_id
     * @param string $user_id
     *
     */
    public function resendCard()
    {
        $staff_id = $this->request->request('staff_id')?$this->request->request('staff_id'):'';
        $user_id = $this->request->request("user_id");
        
        $Staff = new Staff();
        if($staff_id == ''){
            $this->error('staff_id不能为空');
        }else{
            //对方名片信息
            $staff = $Staff->where('id', $staff_id)->find();
            //己方名片信息
            $selfstaff = $Staff->where(['user_id'=>$user_id, 'is_default'=>1])->find();
            if($staff){
                //己方保存的名片
                $su = Su::where(['user_id'=>$user_id,'staff_id'=>$staff_id])->find();
                if($su && $su['status']>=2){
                    if($su['status']==3){
                        $this->success('名片已回递');
                    }
                    //对方保存的名片
                    $othersu = Su::where(['user_id'=>$staff['user_id'],'staff_id'=>$selfstaff['id']])->find();
                    if($othersu){
                        $othersu->status = 4;
                        $othersu->save();
                        $su->status = 4;
                        $su->save();
                    }else{
                        $Su = new Su();
                        $Su->save([
                            'user_id'=>$staff['user_id'],
                            'self_staff_id'=>$staff_id,
                            'staff_id'=>$selfstaff['id'],
                            'status'=>1,
                            'staff_user_id'=>$user_id,
                        ]);
                        $su->status = 3;
                        $su->save();
                    }
                    $this->success('名片已回递');
                }else{
                    $this->error('未保存名片，不能回递');
                }
            }else{
                $this->error('名片不存在');
            }
        }
    }

    /**
     * 名片夹
     *
     */
    public function cardHolder()
    {
        $user_id = $this->user_id;
        $staff = new Staff();
  
        $exchangeCards = $staff->alias('s')->join('smartcard_su u','s.id = u.staff_id')
            ->where(['u.user_id'=>$user_id,'u.status'=>1])
            ->field('s.id,s.user_id,s.name,s.company_id,s.position,u.createtime,self_staff_id')->select();
        foreach ($exchangeCards as &$exchangeCard) {
            $exchangeCard['avatar'] = cdnurl(user::where(['id'=>$exchangeCard->user_id])->value('avatar'),true);
            $exchangeCard['companyname'] = \addons\myadmin\model\Company::where(['id'=>$exchangeCard->company_id])->value('name');
            $mystaff = $staff->where(['id'=>$exchangeCard->self_staff_id])->find();
            $exchangeCard['mystaff'] = [
                'companyname'=>$mystaff->smartcardcompany->name,
                'position'=>$mystaff->position
            ];
            $exchangeCard['origin'] = Visitors::where(['user_id'=>$exchangeCard->user_id,'staff_id'=>$exchangeCard->self_staff_id])->order('createtime desc')->value('origin');
        }
        
        $allCards = $staff->alias('s')->join('smartcard_su u','s.id = u.staff_id')
            ->where(['u.user_id'=>$user_id,'u.status'=>['egt',2]])
            ->field('s.id,s.user_id,s.name,s.company_id,s.position,u.createtime')->select();
        foreach ($allCards as &$allCard) {
            $allCard['avatar'] = cdnurl(user::where(['id'=>$allCard->user_id])->value('avatar'),true);
            $allCard['companyname'] = \addons\myadmin\model\Company::where(['id'=>$allCard->company_id])->value('name');
        }
        $data['exchangeCards'] = $exchangeCards;
        $data['allCards'] = $allCards;
        $this->success('请求成功', $data);

    }
    
    /**
     * 同意交换名片
     * @param string $staff_id
     * @param string $user_id
     *
     */
    public function agreeExchange()
    {
        $su_id = $this->request->request('su_id')?:'';
        $user_id = $this->user_id;
        
        $Staff = new Staff();
        if($su_id == ''){
            $this->error('名片夹id不能为空');
        }else{
            //己方保存的名片
            $su = Su::where(['id'=>$su_id,'user_id'=>$user_id])->find();
            if($su){
                //对方保存的名片
                $othersu = Su::where(['user_id'=>$su['staff_user_id'],'staff_id'=>$su['self_staff_id']])->find();
                if($othersu && $othersu['status']>=2){
                    $othersu->status = 4;
                    $othersu->save();
                    $su->status = 4;
                    $su->save();
                }else{
                    $su->status = 2;
                    $su->save();
                }
                $this->success('名片交换成功');
            }else{
                $this->error('名片夹id非法');
            }
        }
    }

    /**
     * 获取行业列表
     *
     */
    public function industryCategoryList()
    {
        $tree = Tree::instance();
        $tree->nbsp = '';
        $IndustryCategory = new \app\admin\model\smartcard\IndustryCategory();
        $tree->init(collection($IndustryCategory->select())->toArray(), 'parent_id');
//        $categorylist = $tree->getTreeList($tree->getTreeArray(0), 'name');
        $this->success('请求成功', $tree->getTreeArray(0));
    }

    /**
     * 获取主题列表
     *
     */
    public function themeList()
    {
        $login_id = $this->user_id;
        $staff = new Staff();;
        $Theme = new Theme();
        $Userres = $staff
            ->where('user_id',$login_id)
            ->find();
        $Themeres = $Theme
            ->where('id','>=',1)
            ->select();
        //var_dump($Themeres);exit;

        foreach($Themeres as $key => $var){
            if($Themeres[$key]['id'] == $Userres['theme_id']){
                $Themeres[$key]['status'] = 2;
            }else{
                $Themeres[$key]['status'] = 1;
            }
            if($Userres['theme_id'] == 1){
                $Themeres[0]['status'] = 2;
            }else{
                $Themeres[0]['status'] = 1;
            }
        }

        $this->success('请求成功', $Themeres);

    }

    /**
     * 修改主题信息
     * @param string $id     主题id
     * 
     */
    public function themeEdit()
    {   
        $theme_id = $this->request->request('Theme_id')?$this->request->request('Theme_id'):'';
        $login_id = $this->user_id;
        $Staff = new Staff();
        if($theme_id == ''){
            $this->success('更新成功');
        }else{
            $res = $Staff->where('user_id', $login_id)->update(['theme_id' => $theme_id]);
            if($res!==false){
                //更新成功
                $this->success('更新成功');
            }else{
                //更新失败
                $this->error('更新失败');
            } 
        }        
    }


     /**
     * 获取公司基本数据
     * @param string $cid     员工id

     */
    public function myCompanyInfo()
    {   
        $cid = $this->request->request("cid");
        $res = $this->myCompanyInfoData($cid);
        
        
    }
     /**
     * 获取员工基本数据
     * @param string $staff_id     员工id
     */
    public function staffInfo()
    {   
        $staff_id = $this->request->request("staff_id");
        $list = $this->myData($staff_id);
        $this->success('请求成功', $list);
    }

    /**
     * 员工基本数据
     * 
     **/
    public function stafffind(){
        $Staff = new Staff();
        $staff_id = $this->request->request("staff_id")?$this->request->request("staff_id"):'';
        if($staff_id == ''){
            $this->error('未找到相关员工信息');
        }
        $staffres = $Staff
            ->with('user')
            ->where('user_id',$staff_id)
            ->select();
        $this->success('查询成功',$staffres);  
    }

    /**
     * 名片维护
     *  
     **/
    public function smartcardfind(){
        $Staff = new Staff();
        $user_id = $this->user_id;
        if($user_id == ''){
            $this->error('未找到相关员工信息');
        }
//        $config = get_addon_config('smartcard');
        
        $staffres = $Staff
            ->with(['smartcardcompany' => function($query) {
                $query->withField('id,name,address_area');
            }])
            ->where('user_id',$user_id)
            ->select();
//        if($staffres){
//            $staffres=collection($staffres)->toArray();
//            if($this->is_url($staffres[0]['user']['avatar'])==0){
//                   //$staffres[0]['user']['avatarimage']=letter_avatar($staffres[0]['name']);
//                   $staffres[0]['user']['avatar']=letter_avatar($staffres[0]['name']);
//                  }
//            if($this->is_url($staffres[0]['user']['avatar'])==2){
//                  $staffres[0]['user']['avatar']=cdnUrl($staffres[0]['user']['avatar'],true);
//                  //$staffres[0]['user']['avatarimage']=cdnUrl($staffres[0]['user']['avatarimage'],true);
//              }
//             $staffres[0]['platform_status']=2;
//        }
        $this->success('查询成功',$staffres);  
    }


    /**
     * 员工的基本信息修改
     * @param string $staff_id  员工id
     * 
     **/
    public function staffEdit(){
        $Staff = new Staff();
        $login_id = $this->user_id;
       
        $data = $this->request->request();
        $user = $this->auth->getUser();
        if(isset($data['avatar'])){
          $avatar = $this->request->request('avatar', '', 'trim,strip_tags,htmlspecialchars');
          $user->avatar = $avatar;
          
        }
         if(!isset($data['company_id'])){
            $this->error('参数错误');
        }
        if(isset($data['name'])){
         $datam['name']=	$data['name'];
         $user->nickname = $data['name'];
        }
        if(isset($data['company_id'])){
         $datam['company_id']=	$data['company_id'];
        }
        if(isset($data['position'])){
         $datam['position']=	$data['position'];
        }
        if(isset($data['mobile'])){
         $datam['mobile']=	$data['mobile'];
          $User = new User(); 
          $hasMobile=$User->where('mobile',$data['mobile'])->value('id');
          //dump($hasMobile);exit;
          if(!is_null($hasMobile) && $hasMobile!=$login_id){
             $this->error('手机号已被占用');
          }
         $user->mobile = $data['mobile'];
        }
        if(isset($data['email'])){
         $datam['email']=	$data['email'];
        }
        if(isset($data['address'])){
         $datam['address']=	$data['address'];
        }
        if(isset($data['picimages'])){
         $datam['picimages']=	$data['picimages'];
        }
        if(isset($data['introcontent'])){
         $datam['introcontent']=	$data['introcontent'];
        }
        if(isset($data['wechat'])){
         $datam['wechat']=	$data['wechat'];
        }
        if(isset($data['company_id_s'])){
           if($data['company_id_s']!=$data['company_id']){
           	$datam['statusdata']=2;
           }
        }else{
        	 $where['company_id']=$data['company_id'];
        }
        if(isset($data['id'])){
             $where['id']=intval($data['id']);
        }
        if(isset($data['avatar']) || isset($data['mobile']) || isset($data['name'])){
    		 $user->save();
        }
        $where['user_id']=$login_id;
        //dump($where);exit;
        $res = $Staff
            ->where($where)
            ->update($datam);

        if($res!==false){
            //更新成功
            $this->success('更新成功');
        }else{
            //更新失败
            $this->error('更新失败');
        }
    }
      /**
     * 申请加入企业
     * @param string $company_id 企业id
     * @param string $position 职位
     * @param string $mobile 手机号
     * @param string $email 邮箱
     * @param string $wechat 微信号
     * @param string $address 地址
     * @param string $avatar 头像
     **/
    public function applyStaffAdd(){
        $Staff = new Staff();        
        $data = $this->request->request();
        $login_id = $this->user_id;
        $userres = $Staff
            ->where('user_id',$login_id)
            ->find();
        if(!isset($data['company_id']) || $data['company_id']=='undefined'){
            $this->error('请选择所属公司');
        }

		$user = $this->auth->getUser();
        if(isset($data['avatar'])){
          $avatar = $this->request->request('avatar', '', 'trim,strip_tags,htmlspecialchars');
          $user->avatar = $avatar;
          $user->save();
          unset($data['avatar']);
        }

        if(!is_null($userres)){
            $userres->allowField(true)->save($data);
            $this->success('更新员工信息成功');
        }

        $data['user_id']=$login_id;
        $data['statusdata']='2';
        $res = $Staff->isUpdate(false)->allowField(true)->save($data);
        if($res!==false){
                $this->success('提交申请成功',$Staff->id);
         }else{
                $this->error('提交申请失败，请重试');
         }
    }

    /**
     * 公司负责人新增员工
     * @param string $company_id 企业id
     * @param string $login_id 登录者id
     * @param string $user_id 用户id
     *  
     **/
    public function companyStaffAdd(){
        $Company = new Company();
        $Staff = new Staff();        
        $User = new User();        
        $company_id = $this->request->request("company_id")?$this->request->request("company_id"):'';
        $user_id = $this->request->request("user_id")?$this->request->request("user_id"):'';
        if($company_id == ''){
            $this->error('未找到企业相关信息');
        }
        $login_id = $this->user_id;
        $companyres = $Company
            ->where('id',$company_id)
            ->value('administrators_ids');
        $hasStaff=$Staff->where(['user_id'=>$user_id,'company_id'=>$company_id,'statusdata'=>array('neq','3')])->find();
        if($hasStaff){
        	$this->error('该用户已申请或已审核为企业员工,请勿重复添加');
        }
        $data = $this->request->request();
        $userres = $User
            ->where('id',$user_id)
            ->find();
        $data = [
            'name' => $data['name'],
            'position' => $data['position'],
            'user_id' => $user_id,
            'company_id' => $company_id,
            'mobile' => $userres['mobile'],
            'email' => $userres['email'],
            'picimages' => $userres['avatar'],
            'createtime' => time(),
            'statusdata' => 4,
        ];
         $companyres=explode(',',$companyres);
        if(in_array($login_id,$companyres)){
            $res = $Staff->isUpdate(false)->allowField(true)->save($data);
            if($res){
               $datas=[
            	    'staff_id' =>$Staff->id,
            	    'invite_id'=>$login_id,
		            'position' => $data['position'],
		            'company_id' => $company_id,
		            'createtime' => time(),
               ];
                $Message=new Message();
                $resmsg=$Message->allowField(true)->save($datas);
                $this->success('添加成功');
            }else{
                $this->error('添加失败');
            }
        }else{
            $this->error('您不是此企业的负责人');
        }
    }
    
    /**
     * 公司负责人修改员工信息
     * @param string $company_id 企业id
     * @param string statusdata 状态
     * @param string $position 员工职位
     * @param string $id 员工id
     * 
     **/
    public function companyStaffEdit(){
        $Company = new Company();
        $Staff = new Staff();
        $login_id=$this->user_id;
        $data=$this->request->request();
        if(!isset($data['id']) || !isset($data['company_id'])){
        	$this->error('参数错误');;
        }
        $company_id=$data['company_id'];
        $companyres = $Company
            ->where('id',$company_id)
            ->value('administrators_ids');
        $id=$data['id'];
        unset($data['id']);
        $companyres=explode(',',$companyres);
        if(in_array($login_id,$companyres)){
            $res = $Staff
                ->where([
                    'id' => $id,
                    'company_id' => $company_id,                  
                ])
                ->update($data);
            if($res){
                $this->success('更新成功');
            }else{
                $this->error('更新失败');
            }
        }else{
            $this->error('您不是此企业负责人,无权限修改');
        }
        
    }
    
    /**
     * 公司负责人删除员工信息
     * @param string $company_id 企业id
     * @param string $login_id 登录者id
     * 
     **/
    public function companyStaffDelete(){
        $Company = new Company();
        $Message = new Message();
        $Staff = new Staff();        
        $company_id = $this->request->request("company_id")?$this->request->request("company_id"):'';
        if($company_id == ''){
            $this->error('未找到企业相关信息');
        }
        $login_id = $this->user_id;
        $id = $this->request->request("id")?$this->request->request("id"):'';
        $companyres = $Company
            ->where('id',$company_id)
            ->value('administrators_ids');
         $companyres=explode(',',$companyres);
        if(in_array($login_id,$companyres)){
            $res = $Staff
                ->where([
                    'id' => $id,
                ])
                ->delete();
            if($res){
                $message_s=$Message->where('staff_id',$id)->select();
                if($message_s){
                   $message_del=$Message->where('staff_id',$id)->delete();
                }
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }else{
            $this->error('不好意思，您不是此企业的负责人');
        }
    }

    /**
    * 获取公司基本数据 以及各个模块的信息
    * @param string $company_id     员工id
    * @param string $stafftype    员工状态 0=全部,1=已通过,2=待确认,3=审核失败
    * @param string $type     type值为design 宣传册列表数据| goods 企业商品列表数据 | news 公司新闻数据 | staff 公司员工列表数据 | cases anli
    * @param string $page     页数
    * @param string $limit    每页数量
    */
    public function companyInfo()
    {   
        $company_id = $this->request->request("company_id");
        $page = $this->request->request("page");
        $limit = $this->request->request("limit")?$this->request->request("limit"):10;
        $type = $this->request->request("type");
        $stafftype = $this->request->request("stafftype")?$this->request->request("stafftype"):0;

        $keywords='';
        if(!isset($page) || !isset($company_id) || !isset($limit) || !isset($type)){
            $this->error('参数错误');
        }
        if($type=='goods'){
            $keywords= $this->request->request("keywords");
        }

        $list = $this->companyData($company_id,$type,$page,$limit,$keywords,$stafftype);
      	if($type=='staff'){
	        foreach($list['Infos'] as $key => $val){
	         	if($this->is_url($list['Infos'][$key]['user']['avatar'])==0){
	                   $list['Infos'][$key]['user']['avatar']=letter_avatar($list['Infos'][$key]['user']['avatar']);
	                  }
	            if($this->is_url($list['Infos'][$key]['user']['avatar'])==2){
	                  $list['Infos'][$key]['user']['avatar']=$this->serverImgHost.$list['Infos'][$key]['user']['avatar'];
	              }
	         }  
      	}
        
        $this->success('请求成功', $list);
        
        
    }
    
    /**
    * 提交公司入驻信息
    */
    public function companyApply()
    {   
        $data = $this->request->request("");
        $Company = new Company();
        if(!isset($data['name']) || !isset($data['licensenumber']) || !isset($data['licenseimage']) || !isset($data['address'])){
            $this->error('参数错误');
        }
        $res = $Company->isUpdate(false)->allowField(true)->save($data);
        if($res!==false){
            //添加成功
            $this->success('提交成功');
        }else{
            //添加失败
            $this->error('提交失败，请重试');
        }
        
        
    }
    /**
    * 获取公司基本数据 以及各个模块的信息
    * @param string $company_id     员工id
    * @param string $id      具体id的数据
    * @param string $type      type值为design 宣传册列表数据| goods 企业商品列表数据 | news 公司新闻数据 | staff 公司员工列表数据 | cases 案例
    */
    public function departInfo()
    {   
        $company_id = $this->request->request("company_id");
        $id = $this->request->request("id")?$this->request->request("id"):0;
        $type = $this->request->request("type")?$this->request->request("type"):'';
        if(!isset($id) || !isset($company_id) || !isset($type) || $type=='' || $company_id==0 || $id==0){
            $this->error('参数错误');
        }
        $list = $this->departInfoData($company_id,$id,$type);
        $this->success('请求成功', $list);
        
        
    }



    /**
    * 企业动态点赞
    * @param string $staff_id     员工id
    * @param string $typedata     动态点赞传8
    * @param string $company_id   公司id
    * @param string $news_id      动态id
    */
    public function visitorsOptionData()
    {   
        $staff_id=$this->request->request('staff_id')?$this->request->request('staff_id'):0;
        $typedata=$this->request->request('typedata')?$this->request->request('typedata'):0;
        $company_id=$this->request->request('company_id')?$this->request->request('company_id'):0;
        $news_id=$this->request->request('news_id')?$this->request->request('news_id'):0;
        $list = $this->visitorsOption($typedata,$staff_id,$company_id,$news_id);
        $this->success('请求成功', $list);
        
    }
    /**
    * 首页点赞和取消
    * @param string $staff_id     员工id
    */
    public function favorOptionData()
    {   
        $staff_id=$this->request->request('staff_id')?$this->request->request('staff_id'):0;
        $typedata=$this->request->request('typedata')?$this->request->request('typedata'):1;
        $res = $this->favorOption($staff_id,$typedata);
        
    }
    /**
    * 员工页面标签列表
    * @param string $staff_id     员工id
    */
    public function tagsData()
    {   
        $staff_id=$this->request->request('staff_id')?$this->request->request('staff_id'):0;
        $list = $this->tagsLists($staff_id);
        $this->success('请求成功', $list);
        
    } 
    /**
    * 员工页面标签点赞
    * @param string $staff_id     员工id
    * @param string $tags_id     标签id
    */
    public function tagsFavorOptionData()
    {   
        $staff_id=$this->request->request('staff_id')?$this->request->request('staff_id'):0;
        $tags_id=$this->request->request('tags_id')?$this->request->request('tags_id'):0;
        $list = $this->tagsFavorOption($tags_id,$staff_id);
        $this->success('请求成功', $list);
        
    }
    /**
    * 员工页面标签点赞
    * @param string $staff_id     员工id
    * @param string $tags_name     标签名称
    */
    public function tagAddData()
    {   
        $staff_id=$this->request->request('staff_id')?$this->request->request('staff_id'):0;
        $tags_name=$this->request->request('tags_name')?$this->request->request('tags_name'):'';
        $list = $this->tagsAdd($tags_name,$staff_id);
        
    }  
    /**
    * 员工页面标签删除
    * @param string $tags_id     标签id
    */
    public function tagDelData()
    {   
        $tags_id=$this->request->request('tags_id')?$this->request->request('tags_id'):0;
        $res = $this->tagsDel($tags_id);
        
    }  

    /**
     * 分类
     */
    public function type()
    {   $type=$this->request->request('type')?$this->request->request('type'):0;
        $list = $this->typeData($type);
        $this->success('请求成功', $list);
        
        
    }
    /**
     * 获取发现详情
     * @param string $id     id

     */
    public function detail()
    {   
        $id = $this->request->request("id");
        $user_id = $this->request->request("user_id")?$this->request->request("user_id"):0;
        if(!isset($id) || $id==''){
            $this->error('参数错误');
        }
        $detail = $this->detailData($id,$user_id);
        $this->success('请求成功', $detail);   
    }
    /**
     * 发布 动态
     * @param string $title        标题
     * @param string $content      内容
     * @param string $coverimage   封面图 
     * @param string $coverimages  组图
     * @param string $tag_ids      标签
     * @param string $top_ids      话题
     * @param string $city         省市区
     * @param string $address      具体地址（非必填）
     * @param string $latlng        经纬度
     * @param string $iscommentdata 是否开启评论:1=开启,2=关闭
     * //$title,$content,$coverimage,$coverimages,$tag_ids,$top_ids,$city,$address,$latlng,$iscommentdata,$id

     */
    public function publish()
    {   $data=$this->request->request();
        // $id = $this->request->request("id")?$this->request->request("id"):'';
        // if(!isset($id) || $id==''){
        //     $this->error('参数错误');
        // }
        if(!isset($data['id'])){

        }
        if(!isset($data['title'])){
            $this->error('请填写动态标题');
        }else{
           // $data['title']=$this->userTextEncode($data['title']);
        }
        if(!isset($data['description'])){
            $this->error('请填写动态描述');
        }else{
           // $data['content']=$this->userTextEncode($data['content']);
        }
         if(!isset($data['content'])){
            $this->error('请填写动态内容');
        }else{
          //  $data['content']=$this->userTextEncode($data['content']);
        }
         if(!isset($data['coverimages'])){
            $this->error('请至少上传一张图片');
        }
        if(!isset($data['coverimage'])){

        }
        if($data['latlng']!=''){
            $url='https://apis.map.qq.com/ws/geocoder/v1/?location='.$data['latlng'].'&key=KGMBZ-3YBLD-M7F4F-PAOZ6-DVBC2-UNBFL'; 
            $result= $this ->http_curl($url);      
            $result=json_decode($result,true);
            $result2=$result['result']['address_component'];
            $result3=$result['result']['formatted_addresses'];
            $municipality=array('上海市','重庆市','天津市','北京市');
            foreach ($municipality as $key => $value) {
              if($result2['province']==$value){
                $result2['province']=str_replace('市','',$value);
              }
            }
            $data['city']=$result2['province'].'/'.$result2['city'].'/'.$result2['district'];
            $data['address']=$result3['rough'];
        }
        $newDescover = $this->publishData($data);
        if($newDescover){
            $this->success('发布成功');
        }else{
            $this->error('发布失败');
        }
    }
    /**
     * 地址逆解析
     * @param string $latlng        经纬度
     */

    public function locationData(){
        $data=$this->request->request();
        if(isset($data['latlng']) && $data['latlng']!=''){
            $url='https://apis.map.qq.com/ws/geocoder/v1/?location='.$data['latlng'].'&key=KGMBZ-3YBLD-M7F4F-PAOZ6-DVBC2-UNBFL'; 
            $result= $this ->http_curl($url);      
            $result=json_decode($result,true);
            $result2=$result['result']['address_component'];
            $result3=$result['result']['formatted_addresses'];
            $datas['province']=$result2['province'];
            $municipality=array('上海市','重庆市','天津市','北京市');
            foreach ($municipality as $key => $value) {
              if($datas['province']==$value){
                $datas['province']=str_replace('市','',$value);
              }
            }
            //var_dump($datas['province']);exit;
            //if($datas['province'])
            $datas['city']=$result2['city'].'/'.$result2['district'];
            $datas['address']=$result3['rough'];
            $this->success('获取成功',$datas);
        }else{
            $this->error('参数错误');
        }
    }
    
    /**
     * 获取评论列表
     * @param string $discover_id
     * @param string $page 第几页，1开始 
     * @param string $limit 每一页显示多少数量
    
     */
    
    public function showCommentLists()
    {   
        $user_id=$this->request->request('user_id')?$this->request->request('user_id'):0;
        $page=$this->request->request('page');
        $discover_id=$this->request->request('discover_id');
        $limit=$this->request->request('limit')?$this->request->request('limit'):10;
        $listData = $this->showCommentListsMethod($discover_id,$user_id,$page,$limit);
        $this->success('获取成功',$listData);     
    }
    /**
     * 是否点赞了某个动态
     * @param string $discover_id 
     */
    
    public function isFavor()
    {   
        $discover_id=$this->request->request('discover_id')?$this->request->request('discover_id'):0;;
        $listData = $this->isFavorMethod($discover_id);
        $this->success('获取成功',$listData);
    }
    /**
     * 获取小程序Token
     *
     * 
     */
    public function getAccessToken()
    {   
        $config = get_addon_config('third');
        //var_dump($config);exit;
        $appid=$config['wechat']['app_id'];
        $appsecret =$config['wechat']['app_secret'];
        $isExpires = $this->isExpires();

        if($isExpires === false){
            //到期，获取新的
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appsecret;
            $res = $this->https_curl_json($url,'','json');
            // dump($res);
            $arr = json_decode($res,true);
            if($arr && !isset($arr['errcode'])){
                $arr['time'] = time();
                file_put_contents(APP_PATH . '/access_token.json', json_encode($arr));
                return $arr['access_token'];
            }else{
                echo 'error on get access_token';die;
            }
        }else{
            return $isExpires;
        }
        
    }

    /**
     * 添加商品
     * @param string $name  商品名称
     * @param string $picimages 商品图片
     * @param string $maincontent 商品详情
     * @param string $company_id 企业id
     * 
     **/
    public function addGoods(){
        $Goods = new \app\admin\model\smartcard\Goods;
        $login_id = $this->user_id;
        $name = $this->request->request('name')?$this->request->request('name'):'';
        $picimages = $this->request->request('picimages')?$this->request->request('picimages'):'';
        $maincontent = $this->request->request('maincontent')?$this->request->request('maincontent'):'';
        $company_id = $this->request->request('company_id')?$this->request->request('company_id'):'';
        if($name == ''){
            $this->error('请填写商品名称');
        }
        if($picimages == ''){
            $this->error('请填写商品图片');
        }
        if($maincontent == ''){
            $this->error('请填写商品详情');
        }        
        if($company_id == ''){
            $this->error('未获取到企业相关信息');
        }

        $data = [
            'name' => $name,
            'picimages' => $picimages,
            'maincontent' => $maincontent,
            'company_id' => $company_id,
        ];
        $res = $Goods->isUpdate(false)->allowField(true)->save($data);
        if($res!==false){
            //添加成功
            $this->success('添加成功');
        }else{
            //添加失败
            $this->error('添加失败');
        }
    }

    /**
     * 修改商品
     * @param string $id  商品id
     * 
     **/
    public function editGoods(){
        $Goods = new \app\admin\model\smartcard\Goods;
        $id = $this->request->request('id')?$this->request->request('id'):'';
        if($id == ''){
            $this->error('未找到相关信息');
        }
        $data = $this->request->request();
        $data['id'] = $id;
        $login_id = $this->user_id;
        $condition='find_in_set('.$login_id.',administrators_ids)';
        $editpower = $Company
            ->where($condition)
            ->select();
        if($editpower){
            $res = $Goods->isUpdate(true)->allowField(true)->save($data);
        }else{
            $this->error('不好意思,您不是企业负责人');
        }
        if($res!==false){
            //更新成功
            $this->success('更新成功');
        }else{
            //更新失败
            $this->error('更新失败');
        }
    }

    /**
     * 商品详情
     * @param string $id  商品id
     * 
     **/
    public function findGoods(){
        $Goods = new \app\admin\model\smartcard\Goods;
        $id = $this->request->request('id')?$this->request->request('id'):'';

        if($id == ''){
            $this->error('未找到相关信息');
        }
        $res = $Goods
            ->where('id',$id)
            ->find();
        if($res){
            //查询成功
            $this->success('查询成功',$res);
        }
    }

    /**
     * 添加动态
     * 
     * 
     **/
    public function addNews(){
        $News = new \app\admin\model\smartcard\News;
        $login_id = $this->user_id;
        $data = $this->request->request();
        $res = $News->isUpdate(false)->allowField(true)->save($data);
        if($res!==false){
            //添加成功
            $this->success('添加成功');
        }else{
            //添加失败
            $this->error('添加失败');
        }
    }

    /**
     * 修改动态
     * @param string $id  动态id
     * 
     **/
    public function editNews(){
        $News = new \app\admin\model\smartcard\News;
        $id = $this->request->request('id')?$this->request->request('id'):'';
        if($id == ''){
            $this->error('未找到相关信息');
        }
        $data = $this->request->request();
        $data['id'] = $id;
        $login_id = $this->user_id;
        $condition='find_in_set('.$login_id.',administrators_ids)';
        $editpower = $Company
            ->where($condition)
            ->select();
        if($editpower){
            $res = $News->isUpdate(true)->allowField(true)->save($data);
        }else{
            $this->error('不好意思,您不是企业负责人');
        }
        if($res!==false){
            //更新成功
            $this->success('更新成功');
        }else{
            //更新失败
            $this->error('更新失败');
        }
    }


    /**
     * 动态详情
     * @param string $id  动态id
     * 
     **/
    public function findNews(){
        $News = new \app\admin\model\smartcard\News;
        $id = $this->request->request('id')?$this->request->request('id'):'';
        if($id == ''){
            $this->error('未找到相关信息');
        }
        $res = $News
            ->where('id',$id)
            ->find();
        if($res){
            //查询成功
            $this->success('查询成功',$res);
        }
    }

    /**
     * 添加案例
     * 
     * 
     **/
    public function addCases(){
        $Cases = new \app\admin\model\smartcard\Cases;
        $login_id = $this->user_id;
        $data = $this->request->request();
        $res = $Cases->isUpdate(false)->allowField(true)->save($data);
        if($res!==false){
            //添加成功
            $this->success('添加成功');
        }else{
            //添加失败
            $this->error('添加失败');
        }
    }

    /**
     * 修改案例
     * @param string $id  案例id
     * 
     **/
    public function editCases(){
        $Cases = new Cases;
        $Company = new Company();
        $id = $this->request->request('id')?$this->request->request('id'):'';
        if($id == ''){
            $this->error('未找到相关信息');
        }
        $data = $this->request->request();
        $data['id'] = $id;
        $login_id = $this->user_id;
        $condition='find_in_set('.$login_id.',administrators_ids)';
        $editpower = $Company
            ->where($condition)
            ->select();
        if($editpower){
            $res = $Cases->isUpdate(true)->allowField(true)->save($data);
        }else{
            $this->error('不好意思,您不是企业负责人');
        }

        if($res!==false){
            //更新成功
            $this->success('更新成功');
        }else{
            //更新失败
            $this->error('更新失败');
        }
    }

    /**
     * 案例详情
     * @param string $id  案例id
     * 
     **/
    public function findCases(){
        $Cases = new \app\admin\model\smartcard\Cases;
        $id = $this->request->request('id')?$this->request->request('id'):'';
        if($id == ''){
            $this->error('未找到相关信息');
        }
        $res = $Cases
            ->where('id',$id)
            ->find();
        if($res){
            //查询成功
            $this->success('查询成功',$res);
        }
    }

    /**
     * 用户列表查询
     * @param string $username  用户姓名
     * @param string $page 页数
     * @param string $limit 条数
     * 
     **/   
    public function userlist(){
        $User = new User();
        $Staff = new Staff();
        $Staff_userid = $Staff
            ->where('statusdata','in',array('1','2'))
            ->column('user_id');
        //var_dump($Staff_userid);exit;
        $page = $this->request->request('page')?$this->request->request('page'):1;
        $limit = $this->request->request('limit')?$this->request->request('limit'):10;
        $username = $this->request->request('username')?$this->request->request('username'):'';
        $where['nickname'] = array('like','%'.$username.'%');
        $where['mobile']=array('neq','');
        $whereOr['mobile'] = $username;
        $where['id'] = array('not in',$Staff_userid);
        $res = $User
            ->where($where)
            ->whereOr($whereOr)
            ->page($page,$limit)
            ->select();
        $this->success('查询成功',$res);
        
    }

    /**
     * 消息列表列表查询
     * @param string $type 消息状态 0为全部 1=待确认,2=已同意,3=已拒绝
     * @param string $page 页数
     * @param string $limit 条数
     * 
     **/   
    public function messageList(){
        $Message = new Message();
        $type = $this->request->request('type')?$this->request->request('type'):0;
        $page = $this->request->request('page')?$this->request->request('page'):1;
        $limit = $this->request->request('limit')?$this->request->request('limit'):10;
        $login_id = $this->user_id;
        $Staff = new Staff();
        $Staff_userid = $Staff
            ->where('user_id',$login_id)
            ->column('id');
        $where['message.staff_id'] =array('in',$Staff_userid);
        if($type == 0){
            $where['message.statusdata'] = array('>=',0);
        }else{
    		$where['message.statusdata']=$type;
        }
        
        $Messageres = $Message
            ->with(['smartcardcompany'])
            ->where($where)
            ->page($page,$limit)
            ->select();
        $User=new User;
        foreach($Messageres as $key => $var){
            if($Messageres){
                $Messageres[$key]['createtime'] = date('Y-m-d H:i:s', $var['createtime']); 
                $Messageres[$key]['user']=$User->where('id',$var['invite_id'])->find();
	            if($this->is_url($Messageres[$key]['user']['avatar'])==0){
	                   $Messageres[$key]['user']['avatar']=letter_avatar($Messageres[$key]['user']['avatar']);
	             }
            	if($this->is_url($Messageres[$key]['user']['avatar'])==2){
                	 $Messageres[$key]['user']['avatar']=$this->serverImgHost.$Messageres[$key]['user']['avatar'];
            	}
            }
        }
       
        $this->success('查询成功',$Messageres);
    } 
    /**
     * 消息详情
     * @param string $id 消息id
     * 
     **/ 
    public function messageFind(){
        $Message = new Message();
        $id = $this->request->request('id')?$this->request->request('id'):'';
        if($id == ''){
            $this->error('未找到相关信息');
        }
        $where['id'] = $id;
        $Messageres = $Message
            ->where($where)
            ->find();
        $this->success('查询成功',$Messageres);
    }

    /**
     * 消息修改
     * @param string $id 消息id
     * @param string $statusdata 2为同意  3为拒绝
     * 
     **/ 
    public function messageEdit(){
        $Message = new Message();
        $Staff = new Staff();
        $id = $this->request->request('id')?$this->request->request('id'):'';
        if($id == ''){
            $this->error('未找到相关信息');
        }
        $login_id = $this->user_id;
        $Messageres = $Message->where('id',$id)->find();
        if(is_null($Messageres)){
            $this->error('消息不存在');
        }
        $uid=$Staff->where('id',$Messageres['staff_id'])->value('user_id');
        $statusdata = $this->request->request('statusdata')?$this->request->request('statusdata'):1;
        $data = [
            'statusdata' => $statusdata,
            'updatetime' => time()
        ];
        //var_dump($uid);exit;
        if($login_id == $uid){
            $res = $Message->where('id',$id)->update($data); 
            $resstaff=true;
            if($statusdata!=1){
            	if($statusdata==2){
            		$staffStatus=1;
            	}
            	if($statusdata==3){
            		$staffStatus=3;
            	}
               $resstaff = $Staff->where('id',$Messageres['staff_id'])->update(['statusdata'=>$staffStatus]); 
            }
            
        }else{
            $this->error('您不是此消息的邀请对象,无法操作');
        }
        
        if($res!==false && $resstaff!==false ){
            //更新成功
            $this->success('更新成功');
        }else{
            //更新失败
            $this->error('更新失败');
        }
    }

   



}
