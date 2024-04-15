<?php

namespace app\common\service;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use addons\kefu\library\gatewayworker\start;
use AlibabaCloud\SDK\Videoenhan\V20200320\Videoenhan;
use AlibabaCloud\Tea\Exception\TeaError;
use AlibabaCloud\Tea\Utils\Utils;

use app\common\model\UserImage;
use Darabonba\OpenApi\Models\Config;
use AlibabaCloud\SDK\Videoenhan\V20200320\Models\MergeVideoFaceRequest;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
use app\common\service\aliyun\ViapiService;
use app\common\service\aliyun\VideoenhanService;
use Exception;
use OSS\OssClient;
use think\Log;

/**
 * 文件
 */
class FileService extends Base
{
    /**
     * 下载到本地
     * @param $video_url
     * @return string
     * @throws \OSS\Core\OssException
     */
    public static function downloadOss($fileUrl,$documentRoot = '')
    {
        if (!$documentRoot){
            $documentRoot = $_SERVER['DOCUMENT_ROOT'];
        }
        $image_name = '';
        //$video_url = 'https://star-change-face.oss-cn-shanghai.aliyuncs.com/uploads/20230403/99c219de403b11d5ed724a258608ef5d.mp4'; // 视频链接
        $path = parse_url($fileUrl, PHP_URL_PATH);

        //$file_name = '/uploads/resources/'.basename($path);; // 保存文件名
        //$file_name = '/uploads/resources/' . time() . rand(1, 100) . '-' . basename($path); // 保存文件名
        $filePath = $documentRoot . $path;
        if (file_exists($filePath)){
            Log::write('文件存在success1:'.$filePath);
            return true;
        }

        $folder = dirname($filePath); // 获取文件夹路径

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true); // 创建文件夹，设置权限为0777，递归创建
        }
        //Log::error('download1:'.$fileUrl);
        //Log::error('download2:'.$filePath);
        // 下载到本地
        $content = @file_get_contents($fileUrl);
        if ($content !== false) {
            file_put_contents($filePath, $content);
            return [$fileUrl, $image_name];
        }else{
            return false;
        }
    }

    public static function uploadOss($filePath,$file_name,$type){

        if ($type == 'video') {
            $image_name = '/uploads/resources/' . time() . rand(1, 100) . '.jpg';
            $imagePath = $_SERVER['DOCUMENT_ROOT'] . $image_name;
            $re = capture_video_image($filePath, $imagePath, 5);
        }

        $config = get_addon_config('alioss');
        $oss = new OssClient($config['accessKeyId'], $config['accessKeySecret'], $config['endpoint']);
        $ret = $oss->uploadFile($config['bucket'], ltrim($file_name, "/"), $filePath);
        if (isset($re) && $re) {
            $ret1 = $oss->uploadFile($config['bucket'], ltrim($image_name, "/"), $imagePath);
        }
    }


    public static function amqplib_push($mq_data){

        $connection = new AMQPStreamConnection('amqp-cn-uax3a6sae002.cn-beijing.amqp-4.net.mq.amqp.aliyuncs.com', 5672, 'MjphbXFwLWNuLXVheDNhNnNhZTAwMjpMVEFJNXQ2WFp3V3RjZXNSSHY0VHdwbWQ=', 'Q0UzNTMzNzRDMUU1QTE5Q0MyQzZCQTAxOUY4MkE2MTg2RDBBNEE3NjoxNjkwMzYwMjIyNDM3','seo_trade_xiamen',false,'AMQPLAIN',null,'en_US',3,3,null,true,0);
        $channel = $connection->channel();

        /*$mq_data = [
            'uid' => 999,
            'params' => [
                'act' => '123',
                'success'   => true
            ],
        ];*/

        $data = json_encode($mq_data,JSON_UNESCAPED_UNICODE);

        $msg = new AMQPMessage($data,array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));

        $channel->basic_publish($msg, 'ex_seo_trade_xiamen');

        echo " [x] Sent ", $data, "\n";

        $channel->close();
        $connection->close();
    }
}
