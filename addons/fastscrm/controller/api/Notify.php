<?php

namespace addons\fastscrm\controller\api;

use addons\fastscrm\library\WeWork;
use think\Db;
use addons\fastscrm\library\callback\WXBizMsgCrypt;

class Notify extends Base
{
    protected $noNeedLogin = ['*'];

    protected $info = '';

    public function _initialize()
    {
        parent::_initialize();

    }

    public function init()
    {
        $config = get_fastscrm_config_by_server($_SERVER);
        $corpId = $config['corp_id'];
        $token = $config['address_Token'];
        $encodingAesKey = $config['address_EncodingAESKey'];
        $wxcpt = new WXBizMsgCrypt($token, $encodingAesKey, $corpId);




        if (isset($_GET['echostr'])) {
            $sVerifyMsgSig = $_GET['msg_signature'];
            $sVerifyTimeStamp = $_GET['timestamp'];
            $sVerifyNonce = $_GET['nonce'];
            $sVerifyEchoStr = $_GET['echostr'];
            // 需要返回的明文
            $sEchoStr = "";
            $errCode = $wxcpt->VerifyURL($sVerifyMsgSig, $sVerifyTimeStamp, $sVerifyNonce, $sVerifyEchoStr, $sEchoStr);
            if ($errCode == 0) {
                echo $sEchoStr;
            } else {
                print("ERR: " . $errCode . "\n\n");
            }
            exit;
        } else {
            $sReqMsgSig = $_GET['msg_signature'];
            $sReqTimeStamp = $_GET['timestamp'];
            $sReqNonce = $_GET['nonce'];
            $sReqData = file_get_contents('php://input');
            $sMsg = "";  // 解析之后的明文
            $errCode = $wxcpt->DecryptMsg($sReqMsgSig, $sReqTimeStamp, $sReqNonce, $sReqData, $sMsg);
            if ($errCode == 0) {
                $string = $sMsg;
                libxml_disable_entity_loader(true);
                if (preg_match('/(\<\!DOCTYPE|\<\!ENTITY)/i', $string)) {
                    return false;
                }
                $string = preg_replace('/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f\\x7f]/', '', $string);
                $obj = simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA, '', false);
                $data = json_decode(json_encode($obj), true);
                $this->info = $data;
                switch ($data['ChangeType']) {
                    case 'create_user':
                        $this->workerAdd();
                        break;
                    case 'update_user':
                        $this->workerUpdate();
                        break;
                    case 'delete_user':
                        $this->workerDelete();
                        break;
                    case 'create_party':
                        $this->departAdd();
                        break;
                    case 'update_party':
                        $this->departUpdate();
                        break;
                    case 'delete_party':
                        $this->departDelete();
                        break;
                    case 'update_tag':
                        $this->tagUpdate();
                        break;
                }
            } else {
                print("ERR: " . $errCode . "\n\n");
            }
        }
    }

    /**
     * 新增成员事件
     */
    public function workerAdd()
    {

        $data = $this->info;

        $item = [
            'userid' => $data['UserID'],
            'updatetime' => time()
        ];
        $work = new WeWork('App');
        $result = $work->getWorker($data['UserID']);
        isset($result['name']) ? $item['name'] = $result['name'] : '';
        isset($result['department']) ? $item['department'] = implode(',',$result['department']) : '';
        isset($result['mobile']) ? $item['mobile'] = $result['mobile'] : '';
        isset($result['position']) ? $item['position'] = $result['position'] : '';
        isset($result['gender']) ? $item['gender'] = $result['gender'] : '';
        isset($result['email']) ? $item['email'] = $result['email'] : '';
        isset($result['biz_mail']) ? $item['biz_mail'] = $result['biz_mail'] : '';
        isset($result['direct_leader']) ? $item['direct_leader'] = implode(',',$result['direct_leader']): '';
        isset($result['is_leader_in_dept']) ? $item['is_leader_in_dept'] = implode(',',$result['is_leader_in_dept']) : '';
        isset($result['avatar']) ? $item['avatar'] = $result['avatar'] : '';
        isset($result['telephone']) ? $item['telephone'] = $result['telephone'] : '';
        isset($result['alias']) ? $item['alias'] = $result['alias'] : '';
        isset($result['extattr']) ? $item['extattr'] = json_encode($result['extattr']) : '';
        isset($result['status']) ? $item['status'] = $result['status'] : '';
        isset($result['address']) ? $item['address'] = $result['address'] : '';
        isset($result['main_department']) ? $item['main_department'] = $result['main_department'] : '';
        isset($data['CreateTime']) ? $item['createtime'] = $data['CreateTime'] : '';
        Db::name('fastscrm_worker')
            ->insert($item, false, true);
        $this->success('ok', [
            'worker' => 1
        ]);
    }

    /**
     * 更新成员事件
     */
    public function workerUpdate()
    {

        $data = $this->info;
        $item = [
            'userid' => $data['UserID'],
            'updatetime' => time()
        ];
        $work = new WeWork('App');
        $result = $work->getWorker($data['UserID']);
        isset($result['name']) ? $item['name'] = $result['name'] : '';
        isset($result['department']) ? $item['department'] = implode(',',$result['department']) : '';
        isset($result['mobile']) ? $item['mobile'] = $result['mobile'] : '';
        isset($result['position']) ? $item['position'] = $result['position'] : '';
        isset($result['gender']) ? $item['gender'] = $result['gender'] : '';
        isset($result['email']) ? $item['email'] = $result['email'] : '';
        isset($result['biz_mail']) ? $item['biz_mail'] = $result['biz_mail'] : '';
        isset($result['direct_leader']) ? $item['direct_leader'] = implode(',',$result['direct_leader']): '';
        isset($result['is_leader_in_dept']) ? $item['is_leader_in_dept'] = implode(',',$result['is_leader_in_dept']) : '';
        isset($result['avatar']) ? $item['avatar'] = $result['avatar'] : '';
        isset($result['telephone']) ? $item['telephone'] = $result['telephone'] : '';
        isset($result['alias']) ? $item['alias'] = $result['alias'] : '';
        isset($result['extattr']) ? $item['extattr'] = json_encode($result['extattr']) : '';
        isset($result['status']) ? $item['status'] = $result['status'] : '';
        isset($result['address']) ? $item['address'] = $result['address'] : '';
        isset($result['main_department']) ? $item['main_department'] = $result['main_department'] : '';


        Db::name('fastscrm_worker')
            ->where('userid', $data['UserID'])
            ->update($item);

        $this->success('ok', [
            'worker' => 1
        ]);
    }

    /**
     * 删除成员事件
     */
    public function workerDelete()
    {

        $data = $this->info;
        $worker =Db::name('fastscrm_worker')
            ->where('userid', $data['UserID'])
            ->find();
        if(!empty($worker)){
            $worker['createtime'] = time();
            unset($worker['updatetime']);
            unset($worker['id']);
            Db::name('fastscrm_worker_delete')
                ->insert($worker);
        }
        Db::name('fastscrm_worker')
            ->where('userid', $data['UserID'])
            ->delete();
        $this->success('ok', [
            'worker' => 1
        ]);
    }

    /**
     * 新增部门事件
     */
    public function departAdd()
    {
        $data = $this->info;
        $item = [
            'depart_id' => $data['Id'],
            'updatetime' => time()
        ];
        $work = new WeWork('App');
        $result = $work->getOneDepart($data['Id']);
        $result = $result['department'];
        isset($result['name']) ? $item['name'] = $result['name'] : '';
        isset($result['order']) ? $item['order'] = $result['order'] : '';
        isset($data['CreateTime']) ? $item['createtime'] = $data['CreateTime'] : '';
        if ($result['parentid'] > 0) {
            $parent = Db::name('fastscrm_depart')
                ->where('depart_id', $result['parentid'])
                ->field('id')
                ->find();
            $item['parentid'] = $parent['id'];
        } else {
            $item['parentid'] = $result['parentid'];
        }
        Db::name('fastscrm_depart')
            ->insert($item, false, true);

        $this->success('ok', [
            'worker' => 1
        ]);
    }

    /**
     * 更新部门事件
     */
    public function departUpdate()
    {
        $data = $this->info;
        $item = [
            'depart_id' => $data['Id'],
            'updatetime' => time()
        ];
        $work = new WeWork('App');
        $result = $work->getOneDepart($data['Id']);
        $result = $result['department'];
        isset($result['name']) ? $item['name'] = $result['name'] : '';
        isset($result['order']) ? $item['order'] = $result['order'] : '';
        if ($result['parentid'] > 0) {
            $parent = Db::name('fastscrm_depart')
                ->where('depart_id', $result['parentid'])
                ->field('id')
                ->find();
            $item['parentid'] = $parent['id'];
        } else {
            $item['parentid'] = $result['parentid'];
        }


        Db::name('fastscrm_depart')
            ->where('depart_id', $item['depart_id'])
            ->update($item);
        $this->success('ok', [
            'worker' => 1
        ]);
    }

    /**
     * 删除部门事件
     */
    public function departDelete()
    {
        $data = $this->info;
        Db::name('fastscrm_depart')
            ->where('depart_id', $data['Id'])
            ->delete();
        $this->success('ok', [
            'worker' => 1
        ]);
    }


    /**
     * 更新标签事件
     */
    public function tagUpdate()
    {

        $this->success('ok', [
            'worker' => 1
        ]);
    }


}