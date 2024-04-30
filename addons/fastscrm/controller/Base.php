<?php

namespace addons\fastscrm\controller;

use app\common\library\Auth;
use app\common\library\Token;
use think\addons\Controller;
use think\Cookie;
use think\Db;
use addons\fastscrm\library\WeWork;

class Base extends Controller
{
    public function _initialize()
    {
        $this->auth = Auth::instance();
        // token
        $token = $this->request->server('HTTP_TOKEN', $this->request->request('token', \think\Cookie::get('token')));
        if ($token) {
            $this->auth->init($token);
        }
        if (!$this->auth->isLogin()) {
            $work        = new WeWork('H5');
            $code = $this->request->get('code');
            $state = $this->request->get('state');
            $controller =$this->request->controller();
            if(in_array($controller,['reply'])){
                $scope = 'snsapi_base';
            }else{
                $scope = 'snsapi_privateinfo';
            }
            if(!empty($code)){
                $result = $work->getUser($code);
                if ($result['errcode'] == 0 && isset($result['UserId'])) {
                    if(isset($result['user_ticket'])){
                        $detail = $work->getWorkerDetail($result['user_ticket']);
                        if($detail['errcode'] == 0){
                            if(!isset($detail['mobile']) && $scope=='snsapi_privateinfo'){
                                $this->error('请先授权手机号');
                            }
                            $data = [
                                'mobile' => isset($detail['mobile'])?$detail['mobile']:'',
                                'gender' => isset($detail['gender'])?$detail['gender']:'0',
                                'email' => isset($detail['email'])?$detail['email']:'',
                                'biz_mail' => isset($detail['biz_mail'])?$detail['biz_mail']:'',
                                'avatar' => isset($detail['avatar'])?$detail['avatar']:'',
                                'thumb_avatar' => isset($detail['avatar'])?$detail['avatar']:'',
                                'qr_code' => isset($detail['qr_code'])?$detail['qr_code']:'',
                                'address' => isset($detail['address'])?$detail['address']:'',
                                'updatetime' => time()
                            ];
                            Db::name('fastscrm_worker')
                                ->where('userid', $detail['userid'])
                                ->update($data);
                        }
                    }

                    $worker = Db::name('fastscrm_worker')
                        ->where('userid', $result['UserId'])
                        ->find();
                    if (!empty($worker)) {
                        if (empty($worker['fauser_id'])) {

                            $olduser = Db::name('user')->where('mobile', $worker['mobile'])->find();

                            if (empty($olduser)) {
                                $ret = $this->auth->register($worker['mobile'], \fast\Random::alnum(), '', $worker['mobile'], []);
                                if ($ret) {
                                    $userInfo = $this->auth->getUserinfo();
                                    if (!$userInfo) {
                                        $this->error('系统错误');
                                    }
                                    // 绑定
                                    Db::name('fastscrm_worker')->where('id', $worker['id'])->update([
                                        'fauser_id' => $userInfo['id']
                                    ]);
                                    $this->scrmLogin($userInfo['id']);
                                } else {
                                    $this->error('系统错误');
                                }
                            } else {
                                Db::name('fastscrm_worker')
                                    ->where('id', $worker['id'])
                                    ->update(['fauser_id' => $olduser['id']]);
                                Token::clear($olduser['id']);
                                $this->scrmLogin($olduser['id']);
                            }
                        } else {
                            $fauser = Db::name('user')->where('id', $worker['fauser_id'])->find();
                            Token::clear($fauser['id']);
                            $this->scrmLogin($fauser['id']);
                        }
                    } else {
                        $this->error('不在可见范围内');
                    }
                } else {
                    $url = $this->request->url(true);
                    $redirect_uri   = $work->getOAuthRedirectUrl($url,$scope,$controller);
                    $this->redirect($redirect_uri);
                }

            }else{
                $url = $this->request->url(true);
                $redirect_uri   = $work->getOAuthRedirectUrl($url,$scope,$controller);
                $this->redirect($redirect_uri);
            }

        }
    }

    private function scrmLogin($id)
    {
        $result = $this->auth->direct($id);
        if($result){
            Cookie::set('uid', $id, 30 * 86400 );
            Cookie::set('token', $this->auth->getToken(), 30 * 86400 );
            $url = $this->request->baseUrl(true);
            $this->redirect($url);
        }else{
            $this->error('登录异常');
        }
    }

}