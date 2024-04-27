<?php

namespace app\admin\controller\myadmin\company;

use app\common\controller\Backend;
use app\common\service\CompanyService;
use think\Db;
use Exception;
use think\exception\PDOException;
use think\exception\ValidateException;
use fast\Random;

use addons\myadmin\model\AuthGroup;
use addons\myadmin\model\Admin;
use addons\myadmin\model\AuthGroupAccess;
use addons\myadmin\model\ConfigValue;
use addons\myadmin\model\AuthPlayer;
use app\common\model\User;
use addons\myadmin\library\Service;



/**
 * 企业管理
 */
class Index extends Backend
{

    protected $noNeedRight = ['selectpage'];
    protected $selectpageFields = 'id,name,money,handrate,taxerate,address_area,address_code,status';
    protected $searchFields = 'id,name,identifier';
    protected $GroupModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \addons\myadmin\model\Company;
        $this->GroupModel = new \addons\myadmin\model\CompanyGroup;
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("auditList", $this->model->getAuditList());

        $playerList = AuthPlayer::column('name,label', 'label');
        $this->view->assign("playerList", $playerList);
        $this->assignconfig("playerList", $playerList);

        // 类型
        $myadmin_config = get_addon_config('myadmin');
        $typeList = isset($myadmin_config['companytype']) ? $myadmin_config['companytype'] : [];
        $typeList_key = array_keys($typeList);
        $default_type = reset($typeList_key);
        $this->view->assign("default_type", $default_type);
        $this->view->assign("typeList", $typeList);
        $this->assignconfig("typeList", $typeList);



        $param_key = ['group_id'];
        foreach ($param_key as $ov) {
            $this->view->assign("{$ov}", $this->request->param($ov));
            $this->assignconfig("{$ov}", $this->request->param($ov));
        }
    }

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = $this->model->where($where)
                ->with(['founder', 'user', 'group'])
                ->order($sort, $order)
                ->paginate($limit);
            $result = array("total" => $list->total(), "rows" => $list->items());
            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $founder = $this->request->post("founder/a");
            if ($params && $founder) {
                if (!$group =  $this->GroupModel->get($params['group_id'])) {
                    $this->error('请选择企业分组');
                }
                if ($params['id'] && $info = $this->model->where('id', $params['id'])->find()) {
                    $user = User::get($params['id']);
                    $this->error('' . $user['username'] . '已经拥有企业[' . $info['name'] . ']', $info);
                }

                $params = $this->preExcludeFields($params);
                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $this->modelSceneValidate = true;
                $this->modelValidate = true;
                $result = false;
                Db::startTrans();
                try {
                    // 平台会员
                    if (!$params['id']) {
                        $user_data['username'] = $founder['username'];
                        $user_data['nickname'] = $founder['nickname'];
                        $user_data['status'] = 'normal';
                        $user_data['salt'] = Random::alnum();
                        $user_data['password'] = md5(md5($founder['password']) . $user_data['salt']);
                        $user_data['avatar'] = '/assets/img/avatar.png';
                        $user = new \app\admin\model\User;
                        $UserValidate = 'addons\myadmin\validate\IndexUser.add';
                        $user->validateFailException(true)->validate($UserValidate);
                        $user->allowField(true)->save($user_data);
                        $params['id'] = $user->id;
                    }

                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    $company = $this->model;
                    $result = $company->allowField(true)->save($params);
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
                    $founder['avatar'] = '/assets/img/avatar.png'; //设置新管理员默认头像。
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
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids, ['founder']);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $group = \addons\myadmin\model\CompanyGroup::get($row['group_id']);
        $label = isset($group['label']) ? $group['label'] : '';
        $this->view->assign("label", $label);
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $founder = $this->request->post("founder/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                $result = false;
                Db::startTrans();
                try {
                    //审核通过的时候直接修改状态
                    if ($row->status == 'created' && $params['is_authentication'] == 2){
                        $params['status'] = 'normal';
                    }
                    //是否采用模型验证
                    /*$name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                    $companyValidate = \think\Loader::validate($name);
                    $companyValidate->rule(['name' => 'require|regex:[\x7f-\xff]{7,200}|unique:myadmin_company,name,' . $row->id]);
                    $result = $row->validate($name . '.edit')->allowField(true)->save($params);*/
                    $result = $row->allowField(true)->save($params);
                    if ($result === false) {
                        exception($row->getError());
                    }
                    if ($params['is_authentication'] == 2){
                        //初始化公司数据
                        $companyService = new CompanyService();
                        $companyService->init_data($ids);
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
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        if (input('type') == 'audit'){
            return $this->view->fetch('audit');
        }else{
            return $this->view->fetch();
        }
    }

    /**
     * Selectpage搜索
     *
     * @internal
     */
    public function selectpage()
    {
        return parent::selectpage();
    }
}
