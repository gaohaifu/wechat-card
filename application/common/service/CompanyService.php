<?php

namespace app\common\service;

use app\admin\model\Xccmsconfig;
use app\admin\model\Xccmsmenuinfo;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use addons\kefu\library\gatewayworker\start;
use addons\myadmin\model\Domain;
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
use think\Env;
use think\Log;

/**
 * 公司相关
 */
class CompanyService extends Base
{
    public function init_data($companyId){
        $where['company_id'] = $companyId;
        $xccmsMenulist = collection(Xccmsmenuinfo::where($where)->select())->toArray();
        if ($xccmsMenulist){
            return true;
        }

        $defalutCompanyId = Env::get('app.defalut_company_id',1);
        $where['company_id'] = $defalutCompanyId;
        /*//网站配置
        $xccmsMenulist = collection(Xccmsconfig::where($where)->select())->toArray();
        foreach ($xccmsMenulist as &$item){
            $item['company_id'] = $companyId;
            unset($item['id']);
        }
        $re = Xccmsconfig::insertAll($xccmsMenulist);*/
        //网站菜单
        $xccmsMenulist = collection(Xccmsmenuinfo::where($where)->select())->toArray();
        foreach ($xccmsMenulist as &$item){
            $item['company_id'] = $companyId;
            unset($item['id']);
        }
        $re = Xccmsmenuinfo::insertAll($xccmsMenulist);

        return true;
    }

    public function getCompanyByHost($server){
        if (!isset($server['HTTP_HOST'])){
             return ['code'=>'0','msg'=>'未获取到域名'];
        }
        
        $domain = get_first_host($server['HTTP_HOST']);
        $domains = Domain::where(['name'=>$domain])->find()->toArray();
            if(!$domains){
                return ['code'=>'0','msg'=>'该企业已被删除'];
            }
        
        return ['code'=>'1','msg'=>'','data'=>$domains];
    }
}

