<?php

namespace app\admin\controller\myadmin\company;

use addons\myadmin\model\Company;
use app\common\controller\Backend;

use think\Db;
use Exception;
use think\exception\PDOException;
use think\exception\ValidateException;
use addons\myadmin\model\CompanyPlayer;
use app\common\service\DifyService;

/**
 * 协议管理
 *
 * @icon fa fa-circle-o
 */
class Agreement extends Backend
{
    protected $model = null;
    protected $selectpageFields = 'id,name,title,company_id';
    protected $searchFields = 'id,name,title';
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \addons\myadmin\model\CompanyAgreement;
        $statusList = $this->model->getStatusList();
        $this->view->assign("statusList", $statusList);
        $this->assignconfig("statusList", $statusList);

        $param_key = ['player_id', 'company_id'];
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

            $list = $this->model
                ->where($where)
                ->with(['player', 'company'])
                ->order($sort, $order)
                ->paginate($limit);
            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 详情
     */
    public function detail($ids = null){
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
    
    /**
     * 审核
     *
     * @internal
     */
    public function grant($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
        }
        if (false === $this->request->isPost()) {
            $this->view->assign('row', $row);
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $result = false;
        Db::startTrans();
        try {
            if ($params['status'] == 'normal' && $row['status'] != 'normal') {
                $player_data = [];
                if (!$player = CompanyPlayer::where('player_id', $row['player_id'])->where('company_id', $row['company_id'])->find()) {
                    $player = (new CompanyPlayer);
                    $player_data['player_id'] = $row['player_id'];
                    $player_data['company_id'] = $row['company_id'];
                    $player_data['status'] = 'normal';
                }
                $player_data['expiredtime'] = $row['expiredtime'];
                $player->save($player_data);
            }
            $result = $row->allowField(true)->save($params);
            
            if($row->player_id == 4){
                $re = DifyService::createKnowledgeBase($row->company_id);
            }
            Db::commit();
        } catch (ValidateException | PDOException | Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if (false === $result) {
            $this->error(__('No rows were updated'));
        }
        $this->success();
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
