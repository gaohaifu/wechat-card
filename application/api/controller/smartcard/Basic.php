<?php

namespace app\api\controller\smartcard;
use app\common\controller\Api;
use think\Db;
use think\Config;

header('Access-Control-Allow-Origin:*');//允许跨域
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header('Access-Control-Allow-Headers:x-requested-with,content-type,token');
    //浏览器页面ajax跨域请求会请求2次，第一次会发送OPTIONS预请求,不进行处理，直接exit返回，但因为下次发送真正的请求头部有带token
    //所以这里设置允许下次请求头带token否者下次请求无法成功
    exit();
}

/**
 * 基本类，初始化请求
 * Class Base
 * @package app\api\controller\coupon
 */

class Basic extends Api
{

    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    public function _initialize()
    {
        parent::_initialize();
        $this->user_id = $this->auth->id=5;
    }


 public function clinicApplyQr(){
    $res=$this->getQrCode(3);
    var_dump($res);exit;
 }

     /**
     * 获取小程序二维码
     *
     * @param string $type  1代表优惠券验证模式 2代表门店二维码模式
     * @param string $couponNumber  优惠券号码，仅$type=1的时候有效果
     * @param string $category    代表优惠券分类，仅$type=1的时候有效果
     * @param string $clinic_id   代表门店id，仅$type=2的时候有效果
     */ 
    public function getQrCode($type,$couponNumber=null,$category=null,$clinic_id=null){   

        $access_token=$this->getAccessToken();

        if($access_token){
          if($type==1){
            $path='{"path":"pages/user/coupon?category='.$category.'&number='.$couponNumber.'","width":430}';
            $image_name=$couponNumber.'_'.date('YmdHis',time());
          }elseif($type==2){
            $path='{"path":"pages/user/clinicqrverify?clinic_id='.$clinic_id.'","width":430}';
            $image_name=$clinic_id.'_'.date('YmdHis',time());
          }elseif($type==3){
             $path='{"path":"pages/user/claim","width":430}';
             $image_name='clinic_apply_'.date('YmdHis',time());
          }else{
             $this->error('参数错误');  
          }
            
            $url= 'https://api.weixin.qq.com/wxa/getwxacode?access_token='.$access_token;
            $data=$this->https_curl_json($url,$path,'json');
            file_put_contents('uploads/wxqrcode/'.$image_name.'.jpg',$data);
            return '/uploads/wxqrcode/'.$image_name.'.jpg';
        }else{
            $this->error('请先下载微信登录插件，并正确配置');  
        }
    }

     /**
     * 获取小程序Token
     *
     * 
     */
    public function getAccessToken()
    {   
        $config = get_addon_config('smartcard');
        //var_dump($config);exit;
        if($config['wechat']){
          $appid=$config['wechat']['app_id'];
        $appsecret =$config['wechat']['app_secret'];  
        }else{
           $this->error('请下载并正确配置第三方登录插件的微信模块'); 
        }
        
        //var_dump($appid);exit;
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
     * 更新access_token.json
     *
     * 
     */
    public function isExpires(){
        if(!file_exists(APP_PATH . '/access_token.json')){
            return false;
        }
        $res = file_get_contents(APP_PATH . '/access_token.json');
        $arr = json_decode($res,true);
        if($arr && time()<(intval($arr['time'])+intval($arr['expires_in']))){
            //未过期
            return $arr['access_token'];
        }else{
            return false;
        }
    }
     /**
     * curl方法
     *
     * 
     */
    public function https_curl_json($url,$data,$type)
    {
        if($type=='json'){//json $_POST=json_decode(file_get_contents('php://input'), TRUE);
            $headers = array("Content-type: application/json;charset=UTF-8","Accept: application/json","Cache-Control: no-cache", "Pragma: no-cache");
            //$data=json_encode($data);
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers );
        $output = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);//捕抓异常
        }
        curl_close($curl);
        return $output;
    }
    
}
