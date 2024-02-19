<?php

namespace app\myadmin\controller\smartcard;

use addons\myadmin\library\Backend;
use app\common\model\User as userc;
use app\admin\model\Admin as Adminc;
use app\admin\model\AuthGroup;
use app\admin\model\AuthGroupAccess;

use fast\Random;
use think\Db;
use think\Validate;

/**
 * 企业列管理
 *
 * @icon fa fa-circle-o
 */
class Company extends Backend
{
    
    /**
     * Company模型对象
     * @var \app\admin\model\smartcard\Company
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\smartcard\Company;

    }


    public function import()
    {
        parent::import();
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

            
            $admin_id = $this->auth->id;
            $company_id = $this->auth->company_id;
            
            //var_dump($group_id);exit;
            $wheres['company.id'] = $company_id;
            $user = new userc();
            
            if($admin_id!=1){
                $list = $this->model
                    ->with(['ckdadministrators'])
                    ->where($where)
                    ->where($wheres)
                    ->order($sort, $order)
                    ->paginate($limit);
            }else{
                $list = $this->model
                    ->with(['ckdadministrators'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);
            }

            addtion($list,[
                [
                    'field'=>'administrators_ids',
                    'display'=>'ckdadministrators.nickname',
                    'primary'=>'id', //关联表主键
                    'column'=>'nickname',//关联表中读取需要显示的字段
                    'model'=> $user,//关联模型
                ]
            ]);
            
            foreach($list as $row) {
                $row->getRelation('ckdadministrators')->visible(['nickname']);
            }
            

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
            if ($params) {
                $params = $this->preExcludeFields($params);
                $params['begintime']=strtotime($params['begintime']);
                $params['endtime']=strtotime($params['endtime']);
                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    $params['latlng'] = $params['lat'].','.$params['lng'];
                    $result = $this->model->allowField(true)->save($params);
                    $company_id = $this->model->id;
                    $data_le=[];
                    //选择负责人时，多个负责人以逗号分割id。
                    $administrators_ids = explode(',',$params['administrators_ids']);
                    if($administrators_ids[0]==0){
                      $administrators_ids=[];  
                    }
                    $user = new userc();
                    //和之前，之后的比较，之前的没有新增
                    $result_ids=[];
                    $result_ids=$administrators_ids;
                    $admin = new Adminc();
                    $user = new userc();

                    if(count($result_ids)>0){
                       $userdata = $user
                        ->where([
                            'id' => ['in',$result_ids],
                        ])
                        ->select();
                        foreach($userdata as $k => $v){
                                $data_le[$k]['mobile'] = $v['mobile'];
                                $data_le[$k]['username'] = $v['username'].'_'.rand(100, 9999);
                                $data_le[$k]['nickname'] = $v['nickname'];
                                if(strlen($v['avatar'])>254){
                                    $data_le[$k]['avatar']='';
                                }else{
                                   $data_le[$k]['avatar'] = $v['avatar']; 
                                }
                                
                                $data_le[$k]['email'] = $v['email'];
                                $data_le[$k]['nickname'] = $v['nickname'];
                                $data_le[$k]['company_id'] = $company_id;
                                $data_le[$k]['administrators_id'] = $v['id'];
                                $data_le[$k]['salt'] = $v['salt'];
                                $data_le[$k]['password'] = $v['password'];
                                $adminadd = $admin->allowField(true)->save($data_le[$k]);
                                if ($adminadd === false) {
                                  exception($admin->getError());
                                }else{
                                    $dataset=[
                                        'uid'=> $admin->id,
                                        'group_id'=>2
                                        ];
                                    $resAdminGroupAccess=model('AuthGroupAccess')->save($dataset);
                                }
                                
                        } 
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
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $latlng = explode(',',$row['latlng']);
        $row['lng'] = end($latlng);
        $row['lat']= reset($latlng);
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                $params['begintime']=strtotime($params['begintime']);
                $params['endtime']=strtotime($params['endtime']);
               
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $admin = new Adminc();
                    //逗号分隔负责人id，目前设定一个人只允许作为一个企业的负责人。
                    //编辑后的ids
                    $administrators_ids = explode(',',$params['administrators_ids']);
                    if($administrators_ids[0]==0){
                      $administrators_ids=[];  
                    }
                    //编辑前的ids
                    $row_ids = explode(',',$row['administrators_ids']);

                    if($row_ids[0]==0){
                      $row_ids=[];  
                    }
                    
                    //找出前后的不同，要么是新增的，要么是删除的，新增的增加，删除的删掉
                    $diff_ids1=array_diff($administrators_ids,$row_ids);
                    $diff_ids2=array_diff($row_ids,$administrators_ids);
                    $diff_ids=array_merge($diff_ids1,$diff_ids2);
                    
                    //和之前，之后的比较，之前的没有新增
                    $result_ids=[];
                    foreach ($diff_ids as $key=>$v){
                        if(in_array($v,$administrators_ids)){
                            $result_ids[]=$v;
                        }else if(in_array($v,$row_ids)){
                            //之后的没有删除
                            $result_ids_d[]=$v;
                            $admindel = $admin->where(['administrators_id'=>$v,'company_id'=>$ids])->delete();
                        }
                        
                    }
                  
                   //dump($result_ids);exit;
                    $user = new userc();
                    if(count($result_ids)>0){
                       $userdata = $user
                        ->where([
                            'id' => ['in',$result_ids],
                        ])
                        ->select();
                        foreach($userdata as $k => $v){
                                $data_le[$k]['mobile'] = $v['mobile'];
                                $data_le[$k]['username'] = $v['username'].'_'.rand(100, 99999);
                                $data_le[$k]['nickname'] = $v['nickname'];
                                if(strlen($v['avatar'])>254){
                                    $data_le[$k]['avatar']='';
                                }else{
                                   $data_le[$k]['avatar'] = $v['avatar']; 
                                }
                                $data_le[$k]['email'] = $v['email'];
                                $data_le[$k]['nickname'] = $v['nickname'];
                                $data_le[$k]['company_id'] = $ids;
                                $data_le[$k]['administrators_id'] = $v['id'];
                                $data_le[$k]['salt'] = $v['salt'];
                                $data_le[$k]['password'] = $v['password'];
                                $adminadd = $admin->allowField(true)->save($data_le[$k]);
                                if ($adminadd === false) {
                                  exception($admin->getError());
                                }else{
                                    $dataset=[
                                        'uid'=> $admin->id,
                                        'group_id'=>2
                                        ];
                                    $resAdminGroupAccess=model('AuthGroupAccess')->save($dataset);
                                }
                                
                        } 
                    }
                    $result = $row->allowField(true)->save($params);
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
        return $this->view->fetch();
    }
    
    

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
     
    
    protected function selectpage()
    {
        //设置过滤方法
        $this->request->filter(['trim', 'strip_tags', 'htmlspecialchars']);

        //搜索关键词,客户端输入以空格分开,这里接收为数组
        $word = (array)$this->request->request("q_word/a");
        //当前页
        $page = $this->request->request("pageNumber");
        //分页大小
        $pagesize = $this->request->request("pageSize");
        //搜索条件
        $andor = $this->request->request("andOr", "and", "strtoupper");
        //排序方式
        $orderby = (array)$this->request->request("orderBy/a");
        //显示的字段
        $field = $this->request->request("showField");
        //主键
        $primarykey = $this->request->request("keyField");
        //主键值
        $primaryvalue = $this->request->request("keyValue");
        //搜索字段
        $searchfield = (array)$this->request->request("searchField/a");
        //自定义搜索条件
        $custom = (array)$this->request->request("custom/a");
        //是否返回树形结构
        $istree = $this->request->request("isTree", 0);
        $ishtml = $this->request->request("isHtml", 0);
        if ($istree) {
            $word = [];
            $pagesize = 999999;
        }
        $order = [];
        foreach ($orderby as $k => $v) {
            $order[$v[0]] = $v[1];
        }
        $field = $field ? $field : 'name';

        //如果有primaryvalue,说明当前是初始化传值
        if ($primaryvalue !== null) {
            $where = [$primarykey => ['in', $primaryvalue]];
            $pagesize = 999999;
        } else {
            $where = function ($query) use ($word, $andor, $field, $searchfield, $custom) {
                $logic = $andor == 'AND' ? '&' : '|';
                $searchfield = is_array($searchfield) ? implode($logic, $searchfield) : $searchfield;
                $searchfield = str_replace(',', $logic, $searchfield);
                $word = array_filter(array_unique($word));
                if (count($word) == 1) {
                    $query->where($searchfield, "like", "%" . reset($word) . "%");
                } else {
                    $query->where(function ($query) use ($word, $searchfield) {
                        foreach ($word as $index => $item) {
                            $query->whereOr(function ($query) use ($item, $searchfield) {
                                $query->where($searchfield, "like", "%{$item}%");
                            });
                        }
                    });
                }
                if ($custom && is_array($custom)) {
                    foreach ($custom as $k => $v) {
                        if (is_array($v) && 2 == count($v)) {
                            $query->where($k, trim($v[0]), $v[1]);
                        } else {
                            $query->where($k, '=', $v);
                        }
                    }
                }
            };
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            $this->model->where($this->dataLimitField, 'in', $adminIds);
        }
        $list = [];
        $total = $this->model->where($where)->count();
        if ($total > 0) {
            if (is_array($adminIds)) {
                $this->model->where($this->dataLimitField, 'in', $adminIds);
            }

            $fields = is_array($this->selectpageFields) ? $this->selectpageFields : ($this->selectpageFields && $this->selectpageFields != '*' ? explode(',', $this->selectpageFields) : []);

            //如果有primaryvalue,说明当前是初始化传值,按照选择顺序排序
            if ($primaryvalue !== null && preg_match("/^[a-z0-9_\-]+$/i", $primarykey)) {
                $primaryvalue = array_unique(is_array($primaryvalue) ? $primaryvalue : explode(',', $primaryvalue));
                //修复自定义data-primary-key为字符串内容时，给排序字段添加上引号
                $primaryvalue = array_map(function ($value) {
                    return '\'' . $value . '\'';
                }, $primaryvalue);

                $primaryvalue = implode(',', $primaryvalue);

                $this->model->orderRaw("FIELD(`{$primarykey}`, {$primaryvalue})");
            } else {
                $this->model->order($order);
            }
            
            $admin_id = $this->auth->id;
            $user_id = $this->auth->administrators_id;
            $company_id = $this->auth->company_id;
            $AuthGroupAccess = new AuthGroupAccess();
            $userdata = $AuthGroupAccess
                ->where('uid',$admin_id)
                ->find();
                
            $wheres['id'] = $company_id;
            
            if($admin_id != 1 && $userdata['group_id'] == 6){
                $datalist = $this->model->where($where)
                    ->where($wheres)
                    ->page($page, $pagesize)
                    ->select();
            }else{
                $datalist = $this->model->where($where)
                    ->page($page, $pagesize)
                    ->select();
            }
           

            foreach ($datalist as $index => $item) {
                unset($item['password'], $item['salt']);
                if ($this->selectpageFields == '*') {
                    $result = [
                        $primarykey => isset($item[$primarykey]) ? $item[$primarykey] : '',
                        $field      => isset($item[$field]) ? $item[$field] : '',
                    ];
                } else {
                    $result = array_intersect_key(($item instanceof Model ? $item->toArray() : (array)$item), array_flip($fields));
                }
                $result['pid'] = isset($item['pid']) ? $item['pid'] : (isset($item['parent_id']) ? $item['parent_id'] : 0);
                $list[] = $result;
            }
            if ($istree && !$primaryvalue) {
                $tree = Tree::instance();
                $tree->init(collection($list)->toArray(), 'pid');
                $list = $tree->getTreeList($tree->getTreeArray(0), $field);
                if (!$ishtml) {
                    foreach ($list as &$item) {
                        $item = str_replace('&nbsp;', ' ', $item);
                    }
                    unset($item);
                }
            }
        }
        //这里一定要返回有list这个字段,total是可选的,如果total<=list的数量,则会隐藏分页按钮
        return json(['list' => $list, 'total' => $total]);
    }

}