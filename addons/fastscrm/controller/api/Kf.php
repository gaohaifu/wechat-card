<?php

namespace addons\fastscrm\controller\api;

use addons\fastscrm\library\WeWork;
use app\admin\model\fastscrm\kf\Account;
use think\Db;
use addons\fastscrm\library\callback\WXBizMsgCrypt;

class Kf extends Base
{
    protected $noNeedLogin = ['*'];

    protected $info = '';

    public function _initialize()
    {
        parent::_initialize();

    }

    public function init()
    {
        $config         = get_fastscrm_config_by_server($_SERVER);
        $corpId         = $config['corp_id'];
        $token          = $config['kf_Token'];
        $encodingAesKey = $config['kf_EncodingAESKey'];
        $wxcpt          = new WXBizMsgCrypt($token, $encodingAesKey, $corpId);


        if (isset($_GET['echostr'])) {
            $sVerifyMsgSig    = $_GET['msg_signature'];
            $sVerifyTimeStamp = $_GET['timestamp'];
            $sVerifyNonce     = $_GET['nonce'];
            $sVerifyEchoStr   = $_GET['echostr'];
            // 需要返回的明文
            $sEchoStr = "";
            $errCode  = $wxcpt->VerifyURL($sVerifyMsgSig, $sVerifyTimeStamp, $sVerifyNonce, $sVerifyEchoStr, $sEchoStr);
            if ($errCode == 0) {
                echo $sEchoStr;
            } else {
                print("ERR: " . $errCode . "\n\n");
            }
            exit;
        } else {
            $sReqMsgSig    = $_GET['msg_signature'];
            $sReqTimeStamp = $_GET['timestamp'];
            $sReqNonce     = $_GET['nonce'];
            $sReqData      = file_get_contents('php://input');
            $sMsg          = "";  // 解析之后的明文
            $errCode       = $wxcpt->DecryptMsg($sReqMsgSig, $sReqTimeStamp, $sReqNonce, $sReqData, $sMsg);
            if ($errCode == 0) {
                $string = $sMsg;
                libxml_disable_entity_loader(true);
                if (preg_match('/(\<\!DOCTYPE|\<\!ENTITY)/i', $string)) {
                    return false;
                }
                $string     = preg_replace('/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f\\x7f]/', '', $string);
                $obj        = simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA, '', false);
                $data       = json_decode(json_encode($obj), true);
                $this->info = $data;
                switch ($data['Event']) {
                    case 'kf_account_auth_change':
                        $this->kf_change();
                        break;
                }
            } else {
                print("ERR: " . $errCode . "\n\n");
            }
        }
    }

    /**
     * 客户账号授权变更回调
     */
    public function kf_change()
    {
        $data                 = $this->info;
        $accountModel         = new Account();
        $upDate['updatetime'] = time();
        //新增授权
        if (isset($data['AuthAddOpenKfId'])) {
            if (is_array($data['AuthAddOpenKfId'])) {
                $data['AuthAddOpenKfId'] = implode(',', $data['AuthAddOpenKfId']);
            }
            $upDate['manage_privilege'] = 1;
            $accountModel->where('open_kfid', 'in', $data['AuthAddOpenKfId'])->update($upDate);
        }
        //取消授权
        if (isset($data['AuthDelOpenKfId'])) {
            if (is_array($data['AuthDelOpenKfId'])) {
                $data['AuthDelOpenKfId'] = implode(',', $data['AuthDelOpenKfId']);
            }
            $upDate['manage_privilege'] = '';
            $accountModel->where('open_kfid', 'in', $data['AuthDelOpenKfId'])->update($upDate);
        }
        $this->success('ok', [
            'worker' => 1
        ]);
    }


}