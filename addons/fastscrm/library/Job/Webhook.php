<?php

namespace addons\fastscrm\library\Job;

use addons\fastscrm\library\Message;
use fast\Http;
use think\Exception;
use think\Queue;
use think\Queue\Job;
use think\Db;

class Webhook
{
    /**
     * 发送文本消息
     */
    public function sendText(Job $job, $data)
    {
        if ($job->attempts() > 3) {
            $job->delete();
        }
        try {
            $list = Db::name('fastscrm_webhook_send_log')->where('send_id', $data['send_id'])->where('status',
                '0')->select();
            $params['msgtype'] = 'text';
            $params['text']['content'] = $data['json']['text'];
            if ($data['mentioned_type'] == 2) {
                $params['text']['mentioned_list'] = '@all';
            } else {
                $params['text']['mentioned_list'] = Db::name('fastscrm_webhook_send_workers')->where('send_id',
                    $data['send_id'])->column('worker_id');
            }
            $message = new Message();
            foreach ($list as $item) {
                $hook = Db::name('fastscrm_webhook')->where('id', $item['webhook_id'])->find();
                $result = json_decode(Http::post($hook['url'], json_encode($params)), true);
                if ($result['errcode'] !== 0) {
                    $updata['status'] = -1;
                    $updata['message'] = $message->getError($result['errcode']);
                } else {
                    $updata['status'] = 1;
                    $updata['message'] = '发送成功';
                }
                Db::name('fastscrm_webhook_send_log')->where('id', $item['id'])->update($updata);
            }
            $job->delete();
        } catch (\Exception $e) {
            \think\Log::write('queue-' . get_class() . '-sendText' . '：执行失败，错误信息：' . $e->getMessage());
        }

    }

    /**
     * 发送图片消息
     */
    public function sendImage(Job $job, $data)
    {
        if ($job->attempts() > 3) {
            $job->delete();
        }
        try {
            $img_url = $data['json']['picurl'];
            $content = @file_get_contents($img_url);
            $base64Data = base64_encode($content);
            $md5 = md5($content);
            $list = Db::name('fastscrm_webhook_send_log')->where('send_id', $data['send_id'])->where('status',
                '0')->select();
            $params['msgtype'] = 'image';
            $params['image']['base64'] = $base64Data;
            $params['image']['md5'] = $md5;
            $message = new Message();
            foreach ($list as $item) {
                $hook = Db::name('fastscrm_webhook')->where('id', $item['webhook_id'])->find();
                $result = json_decode(Http::post($hook['url'], json_encode($params)), true);
                if ($result['errcode'] !== 0) {
                    $updata['status'] = -1;
                    $updata['message'] = $message->getError($result['errcode']);
                } else {
                    $updata['status'] = 1;
                    $updata['message'] = '发送成功';
                }
                Db::name('fastscrm_webhook_send_log')->where('id', $item['id'])->update($updata);
            }
            $job->delete();
        } catch (\Exception $e) {
            \think\Log::write('queue-' . get_class() . '-sendImage' . '：执行失败，错误信息：' . $e->getMessage());
        }
    }

    /**
     * 发送图文消息
     */
    public function sendNews(Job $job, $data)
    {
        if ($job->attempts() > 3) {
            $job->delete();
        }
        try {
            $list = Db::name('fastscrm_webhook_send_log')->where('send_id', $data['send_id'])->where('status',
                '0')->select();
            $params['msgtype'] = 'news';
            $params['news']['articles'][] = array(
                'title' => $data['json']['title'],
                'description' => $data['json']['description'],
                'url' => $data['json']['url'],
                'picurl' => $data['json']['picurl'],
            );
            $message = new Message();
            foreach ($list as $item) {
                $hook = Db::name('fastscrm_webhook')->where('id', $item['webhook_id'])->find();
                $result = json_decode(Http::post($hook['url'], json_encode($params)), true);
                if ($result['errcode'] !== 0) {
                    $updata['status'] = -1;
                    $updata['message'] = $message->getError($result['errcode']);
                } else {
                    $updata['status'] = 1;
                    $updata['message'] = '发送成功';
                }
                Db::name('fastscrm_webhook_send_log')->where('id', $item['id'])->update($updata);
            }
            $job->delete();
        } catch (\Exception $e) {
            \think\Log::write('queue-' . get_class() . '-sendNews' . '：执行失败，错误信息：' . $e->getMessage());
        }
    }

    /**
     * 发送markdown消息
     */
    public function sendMarkdown(Job $job, $data)
    {
        if ($job->attempts() > 3) {
            $job->delete();
        }
        try {
            $list = Db::name('fastscrm_webhook_send_log')->where('send_id', $data['send_id'])->where('status',
                '0')->select();
            $params['msgtype'] = 'markdown';
            $params['markdown']['content'] = $data['json']['markdown'];
            $message = new Message();
            foreach ($list as $item) {
                $hook = Db::name('fastscrm_webhook')->where('id', $item['webhook_id'])->find();
                $result = json_decode(Http::post($hook['url'], json_encode($params)), true);
                if ($result['errcode'] !== 0) {
                    $updata['status'] = -1;
                    $updata['message'] = $message->getError($result['errcode']);
                } else {
                    $updata['status'] = 1;
                    $updata['message'] = '发送成功';
                }
                Db::name('fastscrm_webhook_send_log')->where('id', $item['id'])->update($updata);
            }
            $job->delete();
        } catch (\Exception $e) {
            \think\Log::write('queue-' . get_class() . '-sendMarkdown' . '：执行失败，错误信息：' . $e->getMessage());
        }
    }

    /**
     * 发送文件消息
     */
    public function sendFile(Job $job, $data)
    {
        if ($job->attempts() > 3) {
            $job->delete();
        }
        try {
            $list = Db::name('fastscrm_webhook_send_log')->where('send_id', $data['send_id'])->where('status',
                '0')->select();
            $params['msgtype'] = 'file';
            $message = new Message();
            foreach ($list as $item) {
                $hook = Db::name('fastscrm_webhook')->where('id', $item['webhook_id'])->find();
                $components = parse_url($hook['url']);
                parse_str($components['query'], $query);
                $media_url = 'https://qyapi.weixin.qq.com/cgi-bin/webhook/upload_media?key=' . $query['key'] . '&type=file';
                $params['file']['media_id'] = $this->upload_media($media_url, $data['json']);
                $result = json_decode(Http::post($hook['url'], json_encode($params)), true);
                if ($result['errcode'] !== 0) {
                    $updata['status'] = -1;
                    $updata['message'] = $message->getError($result['errcode']);
                } else {
                    $updata['status'] = 1;
                    $updata['message'] = '发送成功';
                }
                Db::name('fastscrm_webhook_send_log')->where('id', $item['id'])->update($updata);
            }
            $job->delete();
        } catch (\Exception $e) {
            \think\Log::write('queue-' . get_class() . '-sendFile' . '：执行失败，错误信息：' . $e->getMessage());
        }
    }

    /**
     * 上传文件
     */
    public function upload_media($url, $json)
    {
        $file_name = $json['filename'];
        $ch = curl_init($url);
        $cfile = curl_file_create($json['file'], 'application/octet-stream', $file_name);
        $data = array('file', $cfile);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($res, true);
        if ($res['errcode'] !== 0) {
            $message = new Message();
            throw new Exception($message->getError($res['errcode']));
        } else {
            return $res['media_id'];
        }
    }
}