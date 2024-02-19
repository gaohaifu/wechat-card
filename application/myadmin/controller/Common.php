<?php

namespace app\myadmin\controller;

use addons\myadmin\library\Backend;

/**
 * 公共搜索
 *
 */

class Common extends Backend
{
    protected $noNeedLogin = ['token','company'];
    protected $noNeedRight = ['*'];
    protected $model = null;

    /**
     * 初始化
     */
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 表单令牌
     */
    public function token()
    {
        $token = $this->request->token('__token__', 'md5');
        $this->success(__('Login successful'), '', $token);
        return $token;
    }

    /**
     * 主用戶搜索
     */
    public function user()
    {
        $this->selectpageFields = 'id,username,nickname,mobile';
        $this->searchFields = 'id,username,nickname,mobile';
        $this->model = new \app\common\model\User;
        config('auto_record_log', false);
        return parent::selectpage();
    }

    /**
     * 角色下拉数据
     */
    public function player()
    {
        $this->selectpageFields = 'id,name';
        $this->searchFields = 'id,name';
        $this->model = new \addons\myadmin\model\AuthPlayer;
        config('auto_record_log', false);
        return parent::selectpage();
    }


    /**
     * 角色下拉数据
     */
    public function companylayer()
    {
        $this->selectpageFields = 'id,name';
        $this->searchFields = 'id,name';
        $player_ids = (new \addons\myadmin\model\CompanyPlayer)->where('company_id', COMPANY_ID)->column('player_id');
        $this->model = new \addons\myadmin\model\AuthPlayer;
        $this->modelWhere = ['id'=>['in', $player_ids]];
        config('auto_record_log', false);
        return parent::selectpage();
    }

    /**
     * 企业下拉数据
     */
    public function company()
    {
        $this->selectpageFields = 'id,name';
        $this->searchFields = 'id,name';
        $this->model = new \addons\myadmin\model\Company;
        config('auto_record_log', false);
        return parent::selectpage();
    }

    
    /**
     * 管理员下拉数据
     */
    public function admin()
    {
        $this->selectpageFields = 'id,username,nickname';
        $this->searchFields = 'id,username,nickname';
        $this->model = new \addons\myadmin\model\Admin;
        config('auto_record_log', false);
        return parent::selectpage();
    }
}
