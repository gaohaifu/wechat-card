<?php


namespace app\common\service;

use addons\myadmin\model\Domain;
use app\admin\model\wwh\Cases;
use app\admin\model\wwh\News;
use app\admin\model\wwh\Product;
use app\admin\model\wwh\Tag;
use app\common\model\Keyword;
use app\common\model\KeywordOnlineLog;
use app\common\model\KeywordPart;
use app\common\model\KeywordSearchLog;
use app\common\model\MyadminKeyword;
use fast\Http;
use fast\Mysitemap;
use think\Db;
use think\Env;

/**
 * 关键词服务
 * @package app\common\service
 */
class KeywordService extends Base
{
    public function __construct()
    {
    }

    public function keywordManage($companyId,$keywordStr,$type,$id){
        $model = model('Keyword');
        $modelRelation = model('KeywordRelation');
        $keywordArr = explode(',',$keywordStr);

        //先全部删除关系再新建
        $where['company_id'] = $companyId;
        $where['type'] = $type;
        $where['relation_id'] = $id;
        $re = $modelRelation->where($where)->delete();

        foreach ($keywordArr as $keyword){
            $keywordId = $model->where(['company_id'=>$companyId,'title'=>$keyword])->value('id');
            if (!$keywordId){
                $keywordId = $model->insertGetId([
                    'title' => $keyword,
                    'company_id' => $companyId,
                    'createtime' => time(),
                ]);
            }
            $reRelation = $modelRelation->insert([
                'keyword_id' => $keywordId,
                'company_id' => $companyId,
                'type' => $type,
                'relation_id' => $id,
                'createtime' => time(),
            ]);
        }

    }

    public function keywordPart($companyId){
        $prefix = $suffix = [];
        $keywordPartList = KeywordPart::where('company_id','in',[0,$companyId])->select();
        foreach ($keywordPartList as $item){
            if ($item->type == 1){
                $prefix[] = $item->title;
            }else{
                $suffix[] = $item->title;
            }
        }
        return [$prefix,$suffix];
    }

    /**
     * 随机获取几个关键词
     * @param $companyId
     * @param $num
     * @param $keyword  自定义主词
     * @return array
     */
    public function randomKeyword($companyId,$type = 3,$num = 5,$keyword=""){
        $keywordArr = [];

        $keywordList = $this->autoKeywordList($companyId,$type,$keyword);
        if (!$keywordList){
            return '';
        }
        $arrKey = array_rand($keywordList,$num);
        foreach ($arrKey as $key) {
            $keywordArr[] = $keywordList[$key];
        }
        $keywordStr = implode(',',$keywordArr);
        return $keywordStr;
    }

    public function autoKeywordList($companyId,$type = 3,$query = ''){
        $keywordList = [];
        [$prefixList,$suffixList] = $this->keywordPart($companyId);
        $companyKeyword = MyadminKeyword::where('company_id',$companyId)->where('type',$type)->select();
        if (!$companyKeyword){
            return [];
        }
        foreach ($companyKeyword as $item) {
            foreach ($prefixList as $prefix) {
                $keywordList[] = $prefix.' '.$item->title;
                foreach ($suffixList as $suffix) {
                    $keywordList[] = $item->title.' '.$suffix;
                    $keywordList[] = $prefix.' '.$item->title.' '.$suffix;
                }
            }
        }
        if ($query){
            $keywordList = searchArr($keywordList,$query);
        }

        return $keywordList;

    }

    public function keywordDescription($companyId){
        //$str = '{company name} supply china high quality {行业词},such as {大类词1},{大类词2},{大类词3} etc.';
        $str = '';
        $wwhConfig = cache('wwh_config:'.$companyId);
        if(!$wwhConfig){
            $wwhConfig = Db::name('wwh_config')->cache('wwh_config:'.$wwhConfig,60)->where('company_id', $companyId)->find();
        }
        $where['company_id'] = $companyId;
        $where['type'] = 1;
        $keywordOne = MyadminKeyword::where($where)->value('title');

        $where['type'] = 2;
        $keywordCategory = MyadminKeyword::where($where)->column('title');
        if (!$keywordCategory){
            return '';
        }
        
        $limit = count($keywordCategory) > 3 ? 3 : count($keywordCategory);
        $keywordArr = [];
        $arrKey = array_rand($keywordCategory,$limit);
        if (!$arrKey){
            return '';
        }
        $str = $wwhConfig['map_mc'].' supply china high quality '.$keywordOne.',such as ';
        foreach ($arrKey as $key) {
            $keywordArr[] = $keywordCategory[$key];
        }
        $str .= implode(',',$keywordArr) . ' etc.';
        return $str;
    }

    /**
     * 根据公司抓取数据
     * @param $companyId
     * @return bool
     */
    public function keywordSearch($row,$line = 1){
        $companyId = $row->company_id;
        $url = 'http://47.56.200.118/api/catchKeyword.aspx';
        /*$domain = Db::name('myadmin_domain')->where(['company_id'=>$companyId,'status'=>'normal'])->value('name');
        if (!$domain){
            $row->save(['status' => 3,'updatetime'=>time()]);
            return false;
        }*/

        if (!$row->post_data){
            $detail = $this->createKeywordSearchDetails($row,$line);
            if (!$detail){
                return false;
            }
        }else{
            $detail = $row;
        }
        $employNum = SiteService::setEmployNum($row->domain);

        $data = json_decode($detail->post_data,true);
        $reJson = Http::post($url,$data);
        $re = json_decode($reJson,true);
        
        if (isset($re['code']) && $re['code'] == 200){
            //抓取完成后变更抓取记录信息
            $detail->save(['status'=>1,'code'=>$re['code']??-1,'return_data'=>$reJson]);
            $page_size = 10;
            $onlineData = [];
            $list = $re['succeed_data'];
            foreach ($list as $item){
                $onlineData[] = [
                    'title' => $item['keyword'],
                    'date' => date('Y-m-d'),
                    'page' => $item['rank'] <=0 ? 0 : floor(($item['rank'] - 1) / $page_size) + 1,
                    'position' => $item['rank'] <=0 ? 0 : ($item['rank'] - 1) % $page_size + 1,
                    'company_id' => $companyId,
                    'createtime' => time(),
                    'rank' => $item['rank'],
                ];
            }
            $reInsert = KeywordOnlineLog::insertAll($onlineData);
            if ($reInsert){
                return true;
            }else{
                return false;
            }
            return $re;
        }else{
            //抓取完成后变更抓取记录信息
            $detail->save(['status'=>3]);

            var_dump($url);
            var_dump($data);
            var_dump($reJson);
            var_dump($re);exit();
            return false;
        }
    }

    /**
     * 创建当日抓取记录
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function createKeywordSearch(){
        $currentDay = date('N', time()); // 获取今天是一周中的第几天（1表示星期一，7表示星期日）
        $date = date('Y-m-d',time());
        $count = KeywordSearchLog::where(['date'=>$date])->count();
        if ($count >0){
            return false;
        }
        $where['status'] = 'normal';
        $list = Domain::where($where)->select();
        $data = [];
        foreach ($list as $item){
            $weekOffset = $item->company_id%7 + 1;
            if ($weekOffset == $currentDay){
                $data[] = [
                    'domain' => $item->name,
                    'company_id' => $item->company_id,
                    'date' => $date,
                    'status' => 2,
                    'createtime' => time(),
                ];
            }
        }
        $re = KeywordSearchLog::insertAll($data);
        return $re;
    }

    public function createKeywordSearchDetails($row,$line = 1){
        $companyId = $row->company_id;

        $where['company_id'] = $companyId;
        $keywordList = Keyword::where($where)->column('title');
        if (count($keywordList) < 3){
            var_dump('没有任何关键词');
            $row->save(['status' => 3,'updatetime'=>time()]);
            //没有关键词时 不做抓取
            return false;
        }
        $arr = split_array($keywordList,50);
        foreach ($arr as $key => $item){
            $searchWordArr = $data = [];
            unset($insert);
            foreach ($item as $keyword){
                if ($keyword){
                    $searchWordArr[] = [
                        'keyword' => $keyword,
                        'domain' => $row->domain
                    ];
                }
            }
            $data['line'] = $line;
            $data['key'] = 'AIzaCatch4Tk_3MOPOTjBZFYZA2A1fIJpztD4M';
            $data['search_words'] = json_encode($searchWordArr);
            if ($key == 0){
                $re = $row->save(['post_data'=>json_encode($data)]);
            }else{
                $insert = $row->toArray();
                unset($insert['id'],$insert['status_text']);
                $insert['post_data'] = json_encode($data);
                $re = model('KeywordSearchLog')->allowField(true)->insert($insert);
            }
        }
        return $row;
    }

    public static function getListByCompanyId($companyId,$where=[],$map=[]){
        $map['company_id'] = $companyId;
        $list = KeywordOnlineLog::where($where)
            ->where($map)
            ->field('title,date,page,GROUP_CONCAT(DISTINCT position) as `position`')
            ->group('title,date,page')
            ->order('date desc,page asc,position asc')->select();
        $data = [];

        foreach ($list as $k => $v){
            $title = $v->title;
            $date = $v->date;
            $pos = '第'.$v->page.'页，第'.$v->position.'位';
            // 如果该标题还没有在数组中出现过，则添加一个新的行
            if (!isset($data[$title])) {
                $data[$title] = array();
            }

            $data[$title]['title'] = $title;
            // 将该日期和页数名次的值添加到该标题的行中
            if (isset($data[$title][$date])){
                $data[$title][$date] .= ';'.$pos;
            }else{
                $data[$title][$date] = $pos;
            }
        }
        $data = array_values($data);
        return $data;
    }
}