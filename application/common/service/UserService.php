<?php


namespace app\common\service;

use app\admin\model\wwh\Cases;
use app\admin\model\wwh\News;
use app\admin\model\wwh\Product;
use app\admin\model\wwh\Tag;
use app\common\library\Auth;
use app\common\library\Token;
use app\common\model\User;
use fast\Http;
use fast\Mysitemap;
use fast\Random;
use think\Db;
use think\Env;
use think\Exception;
use think\Hook;

/**
 * 用户相关
 * @package app\common\service
 */
class UserService extends Base
{
    public function __construct()
    {
    }

    /**
     * 通过公司直接注册用户
     * @param $companyId
     */
    public static function registerByCompany($username,$extend = []){
        $ip = request()->ip();
        $time = time();
        $password = '888888';
        $email = '';
        $mobile = '';
        $data = [
            'username' => $username,
            'password' => $password,
            'email'    => $email,
            'mobile'   => $mobile,
            'level'    => 1,
            'score'    => 0,
            'avatar'   => '',
        ];
        $params = array_merge($data, [
            'nickname'  => preg_match("/^1[3-9]{1}\d{9}$/",$username) ? substr_replace($username,'****',3,4) : $username,
            'salt'      => Random::alnum(),
            'jointime'  => $time,
            'joinip'    => $ip,
            'logintime' => $time,
            'loginip'   => $ip,
            'prevtime'  => $time,
            'status'    => 'normal'
        ]);
        $params['password'] = (new Auth())->getEncryptPassword($password, $params['salt']);
        $params = array_merge($params, $extend);

        //账号注册时需要开启事务,避免出现垃圾数据
        Db::startTrans();
        try {
            $user = User::create($params, true);

            Db::commit();
            return $user->id;
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());
            Db::rollback();
            return false;
        }

    }
}