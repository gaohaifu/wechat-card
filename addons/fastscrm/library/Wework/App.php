<?php

namespace addons\fastscrm\library\wework;

use addons\fastscrm\library\Message;
use fast\Http;
use think\Db;
use think\Exception;

class App
{
    protected $url = 'https://qyapi.weixin.qq.com/cgi-bin/';
    protected $access_token = '';

    public function __construct($params)
    {
        $token = Db::name('fastscrm_access_token')->where('corpid', $params['corpid'])->where('corpsecret',
            $params['corpsecret'])->find();
        if (empty($token)) {
            $this->access_token = $this->getToken($params);
        } else {
            if ($token['expiretime'] < time()) {
                $this->access_token = $this->refreshToken($params);
            } else {
                $this->access_token = $token['access_token'];

            }
        }
    }


    public function get($method, $params, $refresh = 0)
    {
        $result = json_decode(Http::get($this->url . $method . '?access_token=' . $this->access_token, $params), true);
        if ($result['errcode'] == 42001 && $refresh < 3) {
            $refresh++;
            $this->get($method, $params, $refresh);
        } else {
            return $result;
        }
    }

    public function post($method, $params, $refresh = 0)
    {
        $postJson = Http::post($this->url . $method . '?access_token=' . $this->access_token,
            json_encode($params));
        $postJson = mb_convert_encoding($postJson, 'UTF-8', 'auto');
        $result = json_decode($postJson,true);
        if ($result['errcode'] == 42001 && $refresh < 3) {
            $refresh++;
            $this->post($method, $params, $refresh);
        } else {
            return $result;
        }
    }

    public function postFile($method, $params, $refresh = 0)
    {
        if($method=='media/upload_attachment'){
            $url       = $this->url . $method . '?access_token=' . $this->access_token . '&media_type=' . $params['type'].'&attachment_type=1';
        }else{
            $url       = $this->url . $method . '?access_token=' . $this->access_token . '&type=' . $params['type'];
        }
        $file_name = Db::name('attachment')->where('url', $params['path'])->value('filename');
        $file      = ROOT_PATH . DS . 'public' . DS . $params['path'];
        $ch        = curl_init($url);
        $cfile     = curl_file_create($file, 'application/octet-stream', $file_name);
        $data      = array('file', $cfile);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result, true);
        if ($result['errcode'] == 42001 && $refresh < 3) {
            $refresh++;
            $this->postFile($method, $params, $refresh);
        } else {
            return $result;
        }
    }

    /**
     * 获取access_token
     */
    public function getToken($params)
    {
        $data['corpid']     = $params['corpid'];
        $data['corpsecret'] = $params['corpsecret'];
        $result             = json_decode(Http::get($this->url . 'gettoken', $data), true);
        if ($result['errcode'] !== 0) {
            throw new Exception($result['errmsg']);
        } else {
            Db::name('fastscrm_access_token')->insert(array(
                'corpid' => $data['corpid'],
                'corpsecret' => $data['corpsecret'],
                'access_token' => $result['access_token'],
                'createtime' => time(),
                'expiretime' => time() + $result['expires_in'] - 200
            ));
            return $result['access_token'];
        }
    }

    /**
     * 刷新access_token
     */
    public function refreshToken($params)
    {
        $data['corpid']     = $params['corpid'];
        $data['corpsecret'] = $params['corpsecret'];
        $result             = json_decode(Http::get($this->url . 'gettoken', $data), true);
        if ($result['errcode'] !== 0) {
            throw new Exception($result['errmsg']);
        } else {
            Db::name('fastscrm_access_token')
                ->where('corpid', $data['corpid'])
                ->where('corpsecret', $data['corpsecret'])
                ->update(array(
                    'access_token' => $result['access_token'],
                    'expiretime' => time() + $result['expires_in'] - 200
                ));
            return $result['access_token'];
        }
    }

    /**
     * 获取应用sdkConfig
     */
    public function getAgentConfig($params)
    {
        $appId = $params['corp_id'];

        $agentId = $params['agent_id'];

        $timestamp = time();

        $nonceStr = $this->getNonceStr();

        $signature = sha1("jsapi_ticket={$this->getAgentTicket('ticket/get')}&noncestr={$nonceStr}&timestamp={$timestamp}&url={$params['url']}");

        return compact('appId', 'agentId', 'timestamp', 'nonceStr', 'signature');
    }

    /**
     * 获取应用的jsapi_ticket
     */
    public function getAgentTicket($method)
    {
        $ticket = Db::name('fastscrm_access_token')
            ->where('access_token', $this->access_token)
            ->find();
        if(empty($ticket) || $ticket['tickettime']< time()){
            $url    = $this->url . $method . '?access_token=' . $this->access_token . '&type=agent_config';
            $result = json_decode(Http::get($url), true);
            if ($result['errcode'] !== 0) {
                throw new Exception($result['errmsg']);
            } else {
                Db::name('fastscrm_access_token')
                    ->where('access_token', $this->access_token)
                    ->update(array(
                        'ticket' => $result['ticket'],
                        'tickettime' => time() + $result['expires_in'] - 200
                    ));
                return $result['ticket'];
            }
        }else{
            return $ticket['ticket'];
        }

    }

    /**
     * 生成随机字符串
     */
    public function getNonceStr()
    {
        $str     = '';
        $str_pol = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyl';
        $max     = strlen($str_pol) - 1;
        for ($i = 0; $i < 16; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }
        return $str;
    }

}