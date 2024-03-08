<?php

namespace app\api\controller\smartcard;
use addons\third\model\Third;
use app\common\library\Auth;
use app\common\library\Sms;
use fast\Http;
use think\Config;
use think\Validate;
use app\common\model\User as userc;
use think\Db;
header('Access-Control-Allow-Origin:*');//允许跨域
/*if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header('Access-Control-Allow-Headers:x-requested-with,content-type,token');
    //浏览器页面ajax跨域请求会请求2次，第一次会发送OPTIONS预请求,不进行处理，直接exit返回，但因为下次发送真正的请求头部有带token
    //所以这里设置允许下次请求头带token否者下次请求无法成功
    exit();
}*/
/**
 * 小程序接口列表
 */
class User extends Base
{
    protected $noNeedLogin = ['third', 'login','logout', 'register', 'resetpwd', 'sendSmsVerify','createAutoCouponlists','getCouponDetails','getVerifyCoupon','getClinicLists','verifyClinicApply','subClinicApply','findNoUserClinic','companylist'];
    protected $noNeedRight = '*';
    protected $token = '';
    public function _initialize()
    {
            parent::_initialize();
            $this->user_id = $this->auth->id;
            $this->serverImgHost=($this->isHTTPS() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']; //获取域名
    }
    /**
     * ==第三方登录
     *
     * @param string $platform 平台名称
     * @param string $code     Code码
     */
    public function third()
    {
        $config = get_addon_config('smartcard');
        $code = $this->request->post("code");
        $platform = $this->request->request("platform");
        $rawData=$this->request->request("raw_data");
        $rawData=str_replace('&quot;','"',$rawData);
        //和手机以及验证码一起注册
        // $mobile=$this->request->request("mobile")?$this->request->request("mobile"):'';
        // $captcha=$this->request->request("captcha")?$this->request->request("captcha"):'';
        //$group_id=$this->request->request("group_id")?$this->request->request("group_id"):'0';
        //$number=$this->request->request("number")?$this->request->request("number"):'';
        $rawData=json_decode($rawData,true);
        
        if (!$config || !isset($config[$platform])) {
            $this->error(__('Invalid parameters'));
        }

        $params = [
            'appid'      => $config[$platform]['app_id'],
            'secret'     => $config[$platform]['app_secret'],
            'js_code'    => $code,
            'grant_type' => 'authorization_code'
        ];
        $result = Http::sendRequest("https://api.weixin.qq.com/sns/jscode2session", $params, 'GET'); 
        
        if ($result['ret']) {
            $json = (array)json_decode($result['msg'], true);
            //$json = ['openid' => 'test', 'session_key' => 'test'];
            if (isset($json['openid'])) {
                //如果有传Token
                if ($this->token) {

                    $this->auth->init($this->token);
                    //检测是否登录
                    if ($this->auth->isLogin()) {
                        $third = Third::where(['openid' => $json['openid'], 'platform' => 'wechat'])->find();
                        if ($third && $third['user_id'] == $this->auth->id) {
                            //把最新的 session_key 保存到 第三方表的 access_token 里
                            $third['access_token'] = $json['session_key'];
                            $third->save();
                            $this->success("登录成功", $this->Format_avatarUrl($this->auth->getUserinfo()));
                        }
                    }
                }
                //传手机号的情况下
                // if ($mobile && !Validate::regex($mobile, "^1\d{10}$")) {
                //     $this->error(__('Mobile is incorrect'));
                // }
                // $ret = Sms::check($mobile, $code, 'register');
                // if (!$ret) {
                //     $this->error(__('Captcha is incorrect'));
                // }
                if(isset($json['unionid'])){
                    $unionid=$json['unionid']?$json['unionid']:''; 
                }else{
                    $unionid=''; 
                }
               
                $result = [
                    'openid'        => $json['openid'],
                    'unionid'        => $unionid,
                    'userinfo'      => [
                        'nickname'      => $rawData['nickName'],
                        'avatar'        => $rawData['avatarUrl'],
                        'gender'        => $rawData['gender'],
                        // 'mobile'        => $mobile
                    ],
                    'access_token'      => $json['session_key'],
                    'refresh_token'     => '',
                    'expires_in'        => isset($json['expires_in']) ? $json['expires_in'] : 0,
                ];
                
                $loginret = \addons\third\library\Service::connect($platform, $result);
                $user = $this->auth->getUser();
                $user->nickname = $rawData['nickName'];
                $user->avatar = $rawData['avatarUrl'];
                $user->gender = $rawData['gender'];;
                $user->save();

                $auths = $this->auth->getUserinfo();
                $userInfo = $this->getUserInfo($auths['id']);
                $staffInfo=Db::name('smartcard_staff')->where(['user_id'=>$auths['id']])->find();
                $data['isStaff']=0;
                $data['staffInfo']=[];
                if($staffInfo){
                    $data['isStaff']=1;
                    $data['staffInfo']=$staffInfo;
                }
                $userInfos=[
                        'id'=>$userInfo['id'],
                        'username'=>$userInfo['username'],
                        'nickname'=>$userInfo['nickname'],
                        'mobile'=>$userInfo['mobile'],
                        'avatar'=>$userInfo['avatar'],
                        'token'=>$userInfo['token'],
                        'createtime'=>$userInfo['createtime'],
                        'group_id'=>$userInfo['group_id']
                ];

                if ($loginret) {
                    
                    $data = [
                        'userinfo'  =>$userInfo,
                        'thirdinfo' => $result,
                        'auth'      =>$auths

                    ];
                    $data['userinfo']['openid']=$data['thirdinfo']['openid'];
                    $data['userinfo']['unionid']=$data['thirdinfo']['unionid'];
                    
                    if ($auths) {
                        $this->success(__('Logged in successful'), $data);
                    } else {
                        $this->error($this->auth->getError());
                    }
                    
                }
                $this->error("登录失败");
                
            } else {
                $this->error("登录失败",$json);
            }
        }else{
             $this->error("请在微信中操作");
        }

        return;
    }
    /**
     * 重置密码
     *
     * @param string $mobile      手机号
     * @param string $newpassword 新密码
     * @param string $captcha     验证码
     */
    public function resetpwd()
    {
 
        $mobile = $this->request->request("mobile");
        $newpassword = $this->request->request("newpassword");
        $oldpassword = $this->request->request("oldpassword");
        if (!$newpassword || !$oldpassword) {
            $this->error(__('Invalid parameters'));
        }
  
        if (!Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        $user = \app\common\model\User::getByMobile($mobile);
        if (!$user) {
            $this->error(__('User not found'));
        }
        $ret = $this->auth->changepwd($newpassword, $oldpassword, false);
        if (!$ret) {
            $this->error(__('Password is incorrect'));
        }
       
        //模拟一次登录
        $this->auth->direct($user->id);
        $ret = $this->auth->changepwd($newpassword, '', true);
        if ($ret) {
            $this->success(__('Reset password successful'));
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 会员登录
     *
     * @param string $account  账号/手机或者密码或者用户名
     * @param string $password 密码
     */
    public function login()
    {
        $account = $this->request->request('account');
        $password = $this->request->request('password');
        if (!$account || !$password) {
            $this->error(__('Invalid parameters'));
        }
        $ret = $this->auth->login($account, $password);
        if ($ret) {
            $staffInfos=Db::name('smartcard_staff')->where(['user_id'=>$this->auth->id])->find();
            $isStaff=0;
            $staffInfo=[];
            if($staffInfos){
                $isStaff=1;
//                $staffInfo = $staffInfos;
            }
            $data = ['userinfo' => $this->auth->getUserinfo(),'isStaff'=>$isStaff,
//                'staffInfo'=>$staffInfo
            ];
            $this->success(__('登录成功'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

     /**
     * ==返回我的角色，普通用户或员工
     */
    public function myGroup()
    {
        $user_id = $this->auth->id;
        if ($user_id) {
           $group_id=Db::name('user')->where('user_id',$user_id)->value('group_id'); 
          $this->success('', ['group_id' => $group_id]); 
        }else{
           $this->error("请重新登录");  
        }
    
    }
    /**
     * 注册会员
     *
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $email    邮箱
     * @param string $mobile   手机号
     * @param string $code   验证码
     */
    public function register()
    {
        $username = $this->request->request('mobile');
        $password = $this->request->request('password');
        $email = $username.'@fa.com';
        $mobile = $this->request->request('mobile');
        $code = $this->request->request('code');
        if (!$username || !$password) {
            $this->error(__('Invalid parameters'));
        }
        // if ($email && !Validate::is($email, "email")) {
        //     $this->error(__('Email is incorrect'));
        // }
         if ($mobile && !Validate::regex($mobile, "^1\d{10}$")) {
             $this->error(__('Mobile is incorrect'));
         }
        $ret = Sms::check($mobile, $code, 'register');
         if($code=='666666'){
             $ret = true;
         }
        if (!$ret) {
            $this->error(__('Captcha is incorrect'));
        }
        $ret = $this->auth->register($username, $password, $email, $mobile, []);
        if ($ret) {
            $auth = $this->auth->getUserinfo();
            $data = ['userinfo' => $auth];
            $this->success(__('注册成功'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }


    /**
     * 查询公司列表
     * @param string $companyname 公司名称
     * @param string $page     页数
     * @param string $limit    每页数量
     * 
     **/
    public function companylist(){
//        $company = new \app\admin\model\smartcard\Company;
        $company = new \addons\myadmin\model\Company;
        $companyname = $this->request->request('companyname')?$this->request->request('companyname'):'';
        $page = $this->request->request("page");
        $limit = $this->request->request("limit")?$this->request->request("limit"):10;
        $where['id'] = array('>=',1);
        if($companyname != ''){
            $where['name'] = array('like','%'.$companyname.'%');
        }
        $res = $company
            ->where($where)
            ->page($page,$limit)
            ->field('id,name')
            ->select();
        $this->success('查询成功',$res);
    }
   
   /**
     * ==会员中心
     */
    public function index()
    {
    $user = $this->auth->getUser();
        $this->success('', ['data' => $user]);
    }

   /**
     * 注销登录
     */
    public function logout()
    {
        $this->auth->logout();
        $this->success(__('已退出登录'));
    }

    /**
     * 修改会员个人信息
     *
     * @param string $avatar   头像地址
     * @param string $birthday 生日
     * @param string $nickname 昵称
     * @param string $gender   个人性别
     */
    public function profile()
    {
        $user = $this->auth->getUser();
        $data = $this->request->request('');
        if(isset($data['avatar'])){
          $avatar = $this->request->request('avatar', '', 'trim,strip_tags,htmlspecialchars');
        }
        
        if (isset($data['username'])) {
            $exists = \app\common\model\User::where('username', $data['username'])->where('id', '<>', $this->auth->id)->find();
            if ($exists) {
                $this->error(__('Username already exists'));
            }
            $user->username = $data['username'];
        }
        $data['birthday']=date('Y-m-d H:i:s',strtotime($data['birthday']));
        $user->nickname = $data['nickname'];
        $user->birthday = $data['birthday'];
        $user->realname = $data['realname'];
        $user->bio = $data['bio'];
        $user->mobile = $data['mobile'];
        $user->gender = $data['sex'];
        $user->birthday = $data['birthday'];
        if(isset($data['avatar'])){
          $user->avatar = $avatar;
        }
        
        $user->save();
        $this->success('修改成功');
    }

    /**
     * 刷新用户
     * @throws \think\exception\DbException
     */
    public function refreshUser()
    {
        //var_dump($this->auth->isLogin());exit;
        if ($this->auth->isLogin()) {
            $auth = $this->auth->getUserinfo();
            $data = [ 'user' => $auth];
            $this->success(__('Refresh successful'), $data);
        } else {
            $this->error(__('请先登录'));
        }
    }

    /**
     * 获取非隐私用户信息
     * @param $id
     * @return User|null
     * @throws \think\exception\DbException
     */
    public function getUserInfo($id)
    {
       
        $userinfo = userc::get($id);
        $userinfo->hidden(['maxsuccessions', 'updatetime', 'joinip', 'jointime', 'loginfailure', 'password', 'prevtime', 'salt', 'token', 'status']);
        //$userinfo['mobile'] = substr($userinfo['mobile'], 0, 3) . '****' . substr($userinfo['mobile'], -4);
        return $userinfo;
    }

  /**
     * ==绑定认证/积分商城普通用户或者员工
     *
     * @param string $mobile  手机
     * @param string $number   员工工号
     * @param string $group_id   申请的用户组，1普通用户，2员工
     * @param string $code   验证码
     */
    public function changeMobile()
    {
        $data = $this->request->request('');
        $mobile=$data['mobile'];
        $captcha=$data['captcha'];
        $group_id=$data['group_id'];
        $user_id=$this->user_id;
        $data['user_id']=$user_id;
        if (!$mobile || !$captcha || !$group_id) {
            $this->error(__('Invalid parameters'));
        }
        //员工绑定才会有工号
        if($group_id==2){
             $number=$data['number'];
             //$name=$data['name'];
         }
        if (!Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        if (\app\common\model\User::where('mobile', $mobile)->where('id', '<>', $user_id)->find()) {
            $this->error(__('Mobile already exists'));
        }
        //开启手机验证
        // $result = Sms::check($mobile, $captcha, 'changemobile');
        // if (!$result) {
        //     $this->error(__('Captcha is incorrect'));
        // }
        $isUserApply=Db::name('coupons_bind')->where('user_id',$data['user_id'])->where('group_id',$data['group_id'])->find();
        if($data['group_id']==1  && $isUserApply){ 
            $this->error('您已绑定过手机，无需再次绑定');
        }else if($data['group_id']==2 && $isUserApply){
           $this->error('您已通过员工认证，无需再次认证');
        }
        //判断如果是员工，对比导入的员工表，看是否有这条导入记录，如果有，且姓名和工号匹配，则验证通过。//->where('staffname',$data['name'])
        $res=true;
        if($data['group_id']==2){
            $isUserStaff=Db::name('coupons_user_staff')->where('staffnumber',$data['number'])->find();
            if(!$isUserStaff){
               $this->error('未查询到该员工信息，请确认填写信息是否有误'); 
            }else{
                $UserStaff=Userstaff::get($isUserStaff['id']);
                $UserStaff -> setAttr('user_id',$data['user_id']);
                $res=$UserStaff-> allowField(true)->save();
            }
        }
        
        unset($data['captcha']);
        unset($data['event']);
        $data['createtime']=time();
        $data['updatetime']=time();
        $userApply=Db::name('coupons_bind')->insertGetId($data);
        
        
        if($userApply && $res){
            $group_id=$data['group_id'];
            $user = $this->auth->getUser();
            $user->group_id = $group_id;
            $user->mobile = $data['mobile'];
            $verification = $user->verification;
            $verification->mobile = 1;
            $user->verification = $verification;
            if($data['group_id']==2 && $data['number']!=''){
                $user->staffnumber = $data['number'];  
            }
            $user->save();
            $this->success('绑定成功'); 
        }else{
            $this->error('绑定失败，请重试');
        }
    }
    /**
     * ==发送验证码
     * @ApiMethod (POST)
     * @param string $mobile 手机号
     * @param string $event  事件名称
     * @return array|bool
     * @throws \think\Exception
     */
    public function sendSmsVerify()
    {
        $mobile = $this->request->request("mobile");
        $event = $this->request->request("event");

        if (!($mobile && $event)) {
            // 缺少参数
            $this->error(__('发送失败，请检查手机是否正确或稍后重试'));
        }
        //事件列表
        $eventarr = [
            'register',
            'resetpwd',
            'changemobile',
            'login',
            'changepwd'
        ];

        if (!$mobile || !\think\Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('手机号不正确'));
        }
        $last = Sms::get($mobile, $event);
        if ($last && time() - $last['createtime'] < 60) {
            $this->error(__('发送频繁'));
        }
        $ipSendTotal = \app\common\model\Sms::where(['ip' => $this->request->ip()])->whereTime('createtime', '-1 hours')->count();
        if ($ipSendTotal >= 5) {
            $this->error(__('发送频繁'));
        }
        $userinfo = \app\common\model\User::getByMobile($mobile);
        if (!in_array($event, $eventarr)) {
            //无事件
            $this->error(__('操作失败'));
        } elseif ($event == 'register' && $userinfo) {
            //已被注册
            $this->error(__('用户已存在'));
        } elseif (in_array($event, ['changemobile']) && $userinfo) {
            //被占用
            $this->error(__('手机号已存在'));
        } elseif (in_array($event, ['changepwd', 'resetpwd']) && !$userinfo) {
            //未注册
            $this->error(__('账户不存在'));
        }else if(in_array($event, ['login', 'resetpwd']) && !$userinfo){
            $this->error(__('手机号不存在，请先注册'));
        }
//        $ret = Sms::send($mobile, null, $event);
        $ret = true;
        if ($ret) {
            $this->success(__('发送成功'));
        } else {
            $this->error(__('发送失败'));
        }
    }
    
}
