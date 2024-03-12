<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use addons\myadmin\model\Company;
use addons\myadmin\model\CompanyGroup;

use think\Db;
use Exception;
use think\exception\PDOException;
use think\exception\ValidateException;

use fast\Random;
use addons\myadmin\model\AuthGroup;
use addons\myadmin\model\Admin;
use addons\myadmin\model\AuthGroupAccess;
use addons\myadmin\model\ConfigValue;
use app\common\model\User;
use addons\myadmin\model\UserBase;


class Myadmin extends Frontend
{

    protected $noNeedLogin = [];
    protected $noNeedRight = '*';
    protected $layout = 'myadmin';

    /**
     * 查找企业
     */
    public function index()
    {
        //$this->view->engine->layout('layout/fdsaf');
        $this->view->assign('title', __('查找企业'));
        $list =   \addons\myadmin\model\Company::with(['user'])->paginate(20);
        $user_id = $this->auth->id;
        $company = UserBase::where('user_id', $user_id)->column('company_id');
        foreach ($list as &$item) {
            $item->isuser = false;
            if (in_array($item['id'], $company)) {
                $item->isuser = true;
            }
        }
        $this->view->assign('list', $list);
        return $this->view->fetch();
    }

    /**
     * 内容列表
     */
    public function content()
    {
        $this->view->assign('title', __('企业资讯'));
        $where = [];
        $mould_id = $this->request->param('mould_id');
        if ($mould_id) {
            $mould = \addons\myadmin\model\WebMould::get($mould_id);
            if ($mould) {
                $this->view->assign('title', __($mould['name']));
            }
            $where['mould_id'] = $mould_id;
        }
        $this->view->assign('mould_id', $mould_id);
        $mouldList = \addons\myadmin\model\WebMould::order('weigh desc')->column('id,icon,name', 'id');
        $this->view->assign('mouldList', $mouldList);
        $list = \addons\myadmin\model\WebContent::where($where)->with(['company'])->paginate(20);
        foreach ($list as &$ov) {
            $ov->url = addon_urls('myadmin/web/content_detail', ['id' => $ov['id'], 'companyappid' => $ov['company']['identifier']]);
        }
        $this->view->assign('list', $list);
        return $this->view->fetch();
    }

    /**
     * 产品列表
     */
    public function product()
    {
        $this->view->assign('title', __('企业产品'));
        $list = \addons\myadmin\model\WebProduct::with(['company'])->paginate(20);
        foreach ($list as &$ov) {
            $ov->url = addon_urls('myadmin/web/product_detail', ['id' => $ov['id'], 'companyappid' => $ov['company']['identifier']]);
        }
        $this->view->assign('list', $list);
        return $this->view->fetch();
    }

    /**
     * 我的会员
     */
    public function user()
    {
        $this->view->assign('title', __('会员中心'));
        $money = \addons\myadmin\model\User::where('user_id', $this->auth->id)->sum('money');
        $score = \addons\myadmin\model\User::where('user_id', $this->auth->id)->sum('score');
        $userlist =   \addons\myadmin\model\User::with(['bindinfo', 'company'])->where('user_id', $this->auth->id)->paginate(20);
        $this->view->assign('userlist', $userlist);
        $this->view->assign('money', $money);
        $this->view->assign('score', $score);
        return $this->view->fetch();
    }

    /**
     * 我的企业
     */
    public function center()
    {
        $this->view->assign('title', __('我的企业'));
        $company = Company::get($this->auth->id);
        // 未开通企业
        if (!$company) {
            $companygroup = CompanyGroup::order('id asc')->select();
            $this->view->assign('group', json_encode($companygroup, JSON_UNESCAPED_UNICODE));
            return $this->view->fetch('register');
        }
        $info = get_addon_config('myadmin');
        $termvalidity = 'off';
        $vipInfo = [];
        if (isset($info['termvalidity']) && $info['termvalidity'] == 'vip') {
            if ($vip = get_addon_info('vip')) {
                if ($vip['state']) {
                    $termvalidity = $info['termvalidity'];
                    $vipInfo = \addons\vip\library\Service::getVipInfo($this->auth->id);
                }
            }
        }
        $this->view->assign('vip', $vipInfo);
        $this->view->assign('termvalidity', $termvalidity);
        $this->view->assign('company', $company);
        return $this->view->fetch();
    }
    /**
     * 注册企业
     */
    public function register()
    {
        // 已经开通企业
        if ($company = Company::get($this->auth->id)) {
            return $this->redirect('index');
        }
        $this->view->assign('title', __('注册企业'));
        if ($this->request->isPost()) {
            $model = new Company;
            $params = $this->request->post("row/a");
            $founder = $this->request->post("founder/a");
            if ($params && $founder) {
                $params['begintime'] = time();
                $params['status'] = 'created';
                $params['id'] = $this->auth->id;
                if ($info =  $model->where('id', $params['id'])->find()) {
                    $this->error('您已经开通：' . $info['name'] . '', $info);
                }
                $companygroup = CompanyGroup::get($params['group_id']);
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    $name = str_replace("\\model\\", "\\validate\\", get_class($model));
                    $model->validateFailException(true)->validate($name . '.add');
                    $company =  $model;
                    $result = $company->allowField(true)->save($params);
                    if ($result) {
                        //超级管理员组
                        $group = AuthGroup::where('pid', 0)->find();
                        if (!$group) {
                            $group_params['pid'] = 0;
                            $group_params['name'] = '超级管理';
                            $group_params['rules'] = '*';
                            $group_params['createtime'] = time();
                            $group_params['status'] = 'normal';
                            $group_params['company_id'] = 0;
                            $group = new AuthGroup;
                            $group->save($group_params);
                        }
                        // 管理员表
                        $founder['salt'] = Random::alnum();
                        $founder['password'] = md5(md5($founder['password']) . $founder['salt']);
                        $founder['avatar'] = isset($params['avatar']) && $params['avatar'] ? $params['avatar'] : '/assets/img/avatar.png'; //设置新管理员默认头像。
                        $founder['company_id'] = $company->id;
                        $founder['is_founder'] = 1; // 设置为创始人
                        $admin = new Admin;
                        $name = str_replace("\\model\\", "\\validate\\", get_class($admin));
                        $adminValidate = $name . '.add';
                        $admin->validateFailException(true)->validate($adminValidate);
                        $admin->allowField(true)->save($founder);
                        //权限关系
                        $access_params['uid'] = $admin->id;
                        $access_params['group_id'] = $group->id;
                        $access_params['company_id'] = $company->id;
                        $access = new AuthGroupAccess;
                        $access->save($access_params);
                        // 初始化配置
                        $config = new ConfigValue;
                        $config_data = ['name' => 'name', 'value' => $params['name'], 'company_id' => $company->id];
                        $config->save($config_data);
                    }
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success('注册企业成功');
                } else {
                    $this->error(__('注册失败，请稍后再试，或联系管理员。'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $companygroup = CompanyGroup::order('id asc')->select();
        $this->view->assign('group', json_encode($companygroup, JSON_UNESCAPED_UNICODE));
        return $this->view->fetch();
    }

    /**
     * 会员申请
     */
    public function apply()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $rs = \addons\myadmin\model\UserBase::apply($params['company_id'], $this->auth->id, $params['name']);
            if ($rs !== true) {
                return  $this->error($rs);
            }
            return $this->success('开通成功');
        }
        $company_id = $this->request->param('company_id');
        $title = '会员开通申请';
        if (!$company = \addons\myadmin\model\Company::get($company_id)) {
            $company['id'] = '';
        }
        $this->view->assign('title', $title);
        $this->view->assign('company', $company);
        return $this->view->fetch();
    }

    /**
     * 更新企业信息
     */
    public function profile()
    {
        $this->view->assign('title', __('企业资料'));
        $row = Company::get($this->auth->id);
        // 未开通企业
        if (!$row) {
            return $this->redirect('index');
        }
        $this->view->assign('company', $row);
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $result = false;
                Db::startTrans();
                try {
                    $name = str_replace("\\model\\", "\\validate\\", get_class(new Company));
                    $companyValidate = \think\Loader::validate($name);
                    $companyValidate->rule(['name' => 'require|regex:[\x7f-\xff]{7,200}|unique:myadmin_company,name,' . $row->id]);
                    $result = $row->validate($name . '.edit')->allowField(true)->save($params);
                    if ($result === false) {
                        exception($row->getError());
                    }
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success('更新企业信息成功');
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }

    /**
     * 余额日志
     * @return string
     */
    public function moneylog()
    {
        $where['user_id'] = $this->auth->id;
        if ($company_id = $this->request->param('company_id')) {
            $where['company_id'] = $company_id;
        }
        $moneyloglist =  \addons\myadmin\model\UserMoneyLog::with(['company'])->where($where)->order('id desc')->paginate(10);
        $this->view->assign('title', __('Balance log'));
        $this->view->assign('moneyloglist', $moneyloglist);
        return $this->view->fetch();
    }
    /**
     * 积分日志
     * @return string
     */
    public function scorelog()
    {
        $where['user_id'] = $this->auth->id;
        if ($company_id = $this->request->param('company_id')) {
            $where['company_id'] = $company_id;
        }
        $scoreloglist =  \addons\myadmin\model\UserScoreLog::with(['company'])->where($where)->order('id desc')->paginate(10);
        $this->view->assign('title', __('Score log'));
        $this->view->assign('scoreloglist', $scoreloglist);
        return $this->view->fetch();
    }
    /**
     * 提现管理
     */
    public function withdrawapply()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $user_id = $this->auth->id;
            $company_id = $params['company_id'];
            $money = $params['money'];
            $type = isset($params['type']) ? $params['type'] : 'bank';
            $name = $params['name'];
            $account = $params['account'];
            $memo = $params['memo'];
            $rs = \addons\myadmin\model\UserWithdraw::apply($company_id, $user_id, $money, $type, $name, $account, $memo);
            if ($rs !== true) {
                return  $this->error($rs);
            }
            return $this->success('申请提现成功');
        }
        $where['user_id'] = $this->auth->id;
        if ($company_id = $this->request->param('company_id')) {
            $where['company_id'] = $company_id;
        }
        $user_list =   \addons\myadmin\model\User::where('user_id', $this->auth->id)->column('company_id,user_id,money,score,handrate,taxerate', 'company_id');
        $company_ids = [];
        foreach ($user_list as $ov) {
            $company_ids[] = $ov['company_id'];
        }

        $company_list =  \addons\myadmin\model\Company::where('id', 'in', $company_ids)->column('id,name,status');
        $company = [];
        $default = [];
        foreach ($company_list as $ov) {
            $userInfo = $user_list[$ov['id']];
            $ov['money'] = $userInfo['money'];
            $ov['score'] = $userInfo['score'];
            $ov['handrate'] = $userInfo['handrate'];
            $ov['taxerate'] = $userInfo['taxerate'];
            $company[] = $ov;
            if ($company_id == $ov['id']) {
                $default = $ov;
            }
        }
        if (!$default) {
            if ($company) {
                $default = $company[0];
            } else {
                $default = [
                    'id' => '',
                    'name' => '',
                    'status' => '',
                    'money' => '',
                    'score' => '',
                    'handrate' => '',
                    'taxerate' => ''
                ];
            }
        }
        if (!$info =  \addons\myadmin\model\UserWithdraw::where('user_id', $this->auth->id)->find()) {
            $info = [
                'account' => '',
                'name' => '',
                'memo' => ''
            ];
        }

        // 类型
        $typeList = (new \addons\myadmin\model\UserWithdraw)->getTypeList();
        $typeList_key = array_keys($typeList);
        $default_type = reset($typeList_key);
        $this->view->assign("default_type", $default_type);
        $this->view->assign("typeList", $typeList);
        $this->assignconfig("typeList", $typeList);

        $this->view->assign('info', $info);
        $this->view->assign('default', $default);
        $this->view->assign('company', json_encode($company, JSON_UNESCAPED_UNICODE));

        $this->view->assign('title', __('Balance log'));
        return $this->view->fetch();
    }
    /**
     * 提现管理
     */
    public function withdraw()
    {
        $where['user_id'] = $this->auth->id;
        if ($company_id = $this->request->param('company_id')) {
            $where['company_id'] = $company_id;
        }
        $this->view->assign('title', __('Balance log'));
        $moneyloglist =  \addons\myadmin\model\UserWithdraw::with(['company'])->where($where)->order('id desc')->paginate(10);
        $this->view->assign('moneyloglist', $moneyloglist);
        return $this->view->fetch();
    }
}
