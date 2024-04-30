<?php


namespace addons\fastscrm\controller\api;

use addons\fastscrm\library\WeWork;
use fast\Http;
use think\Cache;
use think\Db;
use app\common\library\Token;


class User extends Base
{
    protected $noNeedLogin = ['login'];

    protected $userInfo = false;

    public function _initialize()
    {
        parent::_initialize();
    }


    public function getAccessToken()
    {
        $AccessToken = Cache::get('fastscrm_AccessToken');
        if (false === $AccessToken) {
            $config     = get_addon_config('fastscrm');
            $corpid     = $config['corp_id'];
            $corpsecret = $config['App_address_secret'];

            $url                 = "https://qyapi.weixin.qq.com/cgi-bin/gettoken";
            $param['corpid']     = $corpid;
            $param['corpsecret'] = $corpsecret;
            $result              = Http::get($url, $param);
            $result              = @json_decode($result, true);
            if ($result['errcode'] > 0) {
                $this->error($result['description']);
            } else {
                Cache::set('fastscrm_AccessToken', $result['access_token'], $result['expires_in'] - 200);
                return $result['access_token'];
            }
        }
        return $AccessToken;


    }




    /**
     * 小程序授权登录
     */
    public function login()
    {
        $AccessToken = $this->getAccessToken();
        $code        = $this->request->post('code');
        $result      = $this->auth($code, $AccessToken);
        if ($result['errcode'] == 0) {
            $worker = Db::name('fastscrm_worker')
                ->where('userid', $result['userid'])
                ->find();

            if (!empty($worker)) {
                if (empty($worker['fauser_id'])) {

                    $olduser = Db::name('user')->where('username', $worker['mobile'])->find();

                    if (empty($olduser)) {
                        $ret = $this->auth->register($worker['mobile'], \fast\Random::alnum(), '', $worker['mobile'],
                            []);
                        if ($ret) {
                            $userInfo = $this->auth->getUserinfo();
                            // 绑定
                            Db::name('fastscrm_worker')->where('id', $worker['id'])->update([
                                'fauser_id' => $userInfo['id']
                            ]);
                            if (!$userInfo) {
                                $this->error(__('Failed to bind new user, please try again'), [], 401);
                            }
                        } else {
                            $this->error($this->auth->getError());
                        }
                    } else {
                        Db::name('fastscrm_worker')
                            ->where('id', $worker['id'])
                            ->update(['fauser_id' => $olduser['id']]);
                        Token::clear($olduser['id']);
                        $this->auth->direct($olduser['id']);
                        $userInfo = $this->auth->getUserinfo();
                    }
                } else {
                    $fauser = Db::name('user')->where('id', $worker['fauser_id'])->find();
                    Token::clear($fauser['id']);
                    $this->auth->direct($fauser['id']);
                    $userInfo = $this->auth->getUserinfo();
                }

                $worker['token'] = $userInfo['token'];

                $this->success('ok', [
                    'worker' => $worker
                ]);
            } else {
                $this->error('员工未找到');
            }
        } else {
            $this->error('员工未找到');
        }


    }

    /**
     * 更新用户基础资料
     */
    public function updatemember()
    {
        $userid = $this->request->post('userid');
        $result = $this->checkauth($userid);
        if (!empty($result)) {
            $worker          = Db::name('fastscrm_worker')
                ->where('userid', $userid)
                ->find();
            $sysworker       = $this->auth->getUserinfo();
            $worker['token'] = $sysworker['token'];

            $this->success('ok', [
                'worker' => $worker
            ]);
        } else {
            $this->error('非法用户');
        }
    }

    public function auth($code, $AccessToken)
    {

        $url                   = "https://qyapi.weixin.qq.com/cgi-bin/miniprogram/jscode2session";
        $param['access_token'] = $AccessToken;
        $param['js_code']      = $code;
        $param['grant_type']   = 'authorization_code';
        $result                = Http::get($url, $param);
        return json_decode($result, true);
    }

    /**
     * 查询我的会员
     */
    public function getcustomer()
    {
        $page     = $this->request->post('page');
        $keyword  = $this->request->post('keyword');
        $userid   = $this->request->post('userid');
        $pagesize = 20;
        $result   = $this->checkauth($userid);
        if (!empty($result)) {
            $list = Db::name('fastscrm_customer')
                ->where('name', 'like', '%' . $keyword . '%')
                ->where('fl_userid', $userid)
                ->page($page, $pagesize)
                ->select();

            $this->success('ok', [
                'list' => $list,
                'pagesize' => $pagesize,
                'total' => count($list)
            ]);
        } else {
            $this->error('非法用户');
        }


    }

    /**
     * 修改我的昵称
     */
    public function changename()
    {
        $userid   = $this->request->post('userid');
        $nickname = $this->request->post('nickname');
        $result   = $this->checkauth($userid);
        if (!empty($result)) {
            $params['userid'] = $userid;
            $params['name']   = $nickname;
            $work             = new WeWork();
            $work->updateWorker($params);

            Db::name('fastscrm_worker')
                ->where('userid', $userid)
                ->update(['name' => $nickname]);


            $worker = Db::name('fastscrm_worker')
                ->where('userid', $userid)
                ->find();

            $sysworker       = $this->auth->getUserinfo();
            $worker['token'] = $sysworker['token'];


            $this->success('ok', [
                'worker' => $worker
            ]);
        } else {
            $this->error('非法用户');
        }


    }

    /**
     *  获取用户当前门店
     */
    public function getstore()
    {
        $userid = $this->request->post('userid');
        $result = $this->checkauth($userid);
        if (!empty($result)) {
            $worker = Db::name('fastscrm_worker')
                ->where('userid', $userid)
                ->find();


            if (empty($worker['store_id'])) {
                $this->error('请先绑定门店！');
            }

            $store_id = explode(',', $worker['store_id']);

            $stores = array();

            foreach ($store_id as $sid) {
                $stores[] = Db::name('fastscrm_store')
                    ->where('id', $sid)
                    ->find();
            }


            $this->success('ok', [
                'stores' => $stores
            ]);
        } else {
            $this->error('非法用户');
        }


    }

    /**
     *  新增客户，流失客户
     */

    public function customerdata()
    {

        $userid  = $this->request->post('userid');
        $storeid = $this->request->post('storeid');
        $dat     = $this->request->post('dat');
        $result  = $this->checkauth($userid);
        if (!empty($result)) {
            if (empty($dat)) {
                $starttime = strtotime(date('Y-m-d', time()));
                $endtime   = $starttime + 86400;
            } else {
                $starttime = strtotime($dat);
                $endtime   = strtotime("+1 month", $starttime);
            }

            $cus_num = Db::name('fastscrm_customer')
                ->where('fl_userid', $userid)
                ->whereTime('fl_createtime', 'between', [$starttime, $endtime])
                ->count();

            $cus_lose = Db::name('fastscrm_customer_lose')
                ->where('fl_userid', $userid)
                ->whereTime('createtime', 'between', [$starttime, $endtime])
                ->count();


            $this->success('ok', [
                'cus_data' => array(
                    'cus_num' => $cus_num,
                    'cus_lose' => $cus_lose,
                )
            ]);
        } else {
            $this->error('非法用户');
        }


    }

    /**
     *  新增客户，流失客户 榜单
     */

    public function getrank()
    {

        $userid  = $this->request->post('userid');
        $storeid = $this->request->post('storeid');
        $dat     = $this->request->post('dat');
        $current = $this->request->post('current');
        $result  = $this->checkauth($userid);
        if (!empty($result)) {

            $workers = Db::name('fastscrm_worker')
                ->where('find_in_set(:id,store_id)', ['id' => $storeid])
                ->select();

            if (empty($dat)) {
                $starttime = strtotime(date('Y-m-d', time()));
                $endtime   = $starttime + 86400;
            } else {
                $starttime = strtotime($dat);
                $endtime   = strtotime("+1 month", $starttime);
            }

            foreach ($workers as &$worker) {
                $worker['cus_num']  = Db::name('fastscrm_customer')
                    ->where('fl_userid', $worker['userid'])
                    ->whereTime('fl_createtime', 'between', [$starttime, $endtime])
                    ->count();
                $worker['cus_lose'] = Db::name('fastscrm_customer_lose')
                    ->where('fl_userid', $worker['userid'])
                    ->whereTime('createtime', 'between', [$starttime, $endtime])
                    ->count();
            }
            unset($worker);

            //排序

            if ($current == 0) {
                usort($workers, function ($a, $b) {
                    return ($a['cus_num'] > $b['cus_num']) ? -1 : 1;
                });
            } else {
                usort($workers, function ($a, $b) {
                    return ($a['cus_lose'] > $b['cus_lose']) ? -1 : 1;
                });
            }

            $this->success('ok', [
                'workers' => $workers
            ]);

        } else {
            $this->error('非法用户');
        }


    }


}