<?php

namespace app\common\service;

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
 * 队列
 */
class QueueService extends Base
{
    public static function image_download($url,$root){
        // 当前任务将由哪个类来负责处理
        // 当轮到该任务时，系统将生成一个该类的实例，并调用其fire方法
        $jobHandlerClass = 'app\common\job\Image';
        // 当前任务归属的队列名称，如果为新队列，会自动创建
        $jobQueueName = 'image';

        // 当前任务所需的业务数据，不能为 resource 类型，其他类型最终将转化为 json 形式的字符串
        $jobData = ['url' => $url,'document_root'=>$root];
        $isPushed = \think\Queue::push($jobHandlerClass, $jobData, $jobQueueName);
        if ($isPushed !== false) {
            return '添加队列成功';
        } else {
            return '添加列表失败';
        }
    }
}
