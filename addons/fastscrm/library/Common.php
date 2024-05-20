<?php

namespace addons\fastscrm\library;

use think\Controller;
use think\Db;

/**
 * 公共类
 */
class Common extends Controller
{
    /**
     * 排序
     */
    public function multi_array_sort($multi_array, $sort_key, $sort = SORT_ASC)
    {
        if (is_array($multi_array)) {
            foreach ($multi_array as $row_array) {
                if (is_array($row_array)) {
                    $key_array[] = $row_array[$sort_key];
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }

        array_multisort($key_array, $sort, $multi_array);

        return $multi_array;
    }

    /**
     * 获取父级的企微端ID
     */
    public function getPid($id)
    {
        $depart_id = Db::name('fastscrm_depart')
            ->where('id', $id)
            ->value('depart_id');
        return $depart_id;
    }

    /**
     * 数据比较
     * 本地数据如果有废数据,则进行删除记表
     */
    public function difCpr($tablename, $pinfo, $field, $data)
    {
        $fa =  Db::connect([], true)->getConfig('prefix');
        $list = Db::name($tablename)
            ->where($pinfo)
            ->select();
        foreach ($list as $item) {
            $find = 0;
            foreach ($data as $datum) {
                if ($datum[$field] == $item[$field]) {
                    $find = 1;
                }
                if ($find == 1) {
                    break;
                }
            }
            if ($find == 0) {
                Db::name($tablename)
                    ->where('id', $item['id'])
                    ->delete();
                $tabel = "'".$fa.$tablename."_lose'";
                $isTable =  DB::query('SHOW TABLES LIKE ' .$tabel);
                if ($isTable) {
                    unset($item['id']);
                    $item['createtime'] = time();
                    Db::name($tablename . '_lose')
                        ->insert($item, false, true);
                }
            }
        }
    }


    /**'
     * 传入部门字符串 ，调用递归整合
     */

    public function dealDepart($depart_id)
    {

        $departs = explode(',', $depart_id);
        $ids = array();
        foreach ($departs as $depart) {
            $ids[] = intval($depart);
            $res = $this->getDowns($depart);
            $ids = array_merge($ids, $res['ids']);
        }
        $ids = array_values(array_unique($ids));

        return $ids;


    }

    /**
     * 递归 获取本部门所有下级
     */
    public function getDowns($id)
    {

        $depart = Db::name('fastscrm_depart')->where('depart_id', $id)->find();

        $downs = Db::name('fastscrm_depart')->where('parentid', $depart['id'])->select();

        $ids = array();
        foreach ($downs as $val) {
            $ids[] = $val['depart_id'];
            $arr = $this->getdowns($val['depart_id']);
            if ($arr) {
                $ids = array_merge($ids, $arr['ids']);
            }

        }
        return array('ids' => $ids);
    }

    /**
     * 获取企微员工信息
     */
    public function getWorker($user_id)
    {
        return Db::name('fastscrm_worker')->where('fauser_id', $user_id)->find();
    }

    /**
     * 移除昵称表情
     */
    public function removeEmoji($text)
    {
        if ($text == '') return '';

        $clean_text = "";
        // Match Emoticons
        $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clean_text = preg_replace($regexEmoticons, '', $text);
        // Match Miscellaneous Symbols and Pictographs
        $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clean_text = preg_replace($regexSymbols, '', $clean_text);
        // Match Transport And Map Symbols
        $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clean_text = preg_replace($regexTransport, '', $clean_text);
        // Match Miscellaneous Symbols
        $regexMisc = '/[\x{2600}-\x{26FF}]/u';
        $clean_text = preg_replace($regexMisc, '', $clean_text);
        // Match Dingbats
        $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
        $clean_text = preg_replace($regexDingbats, '', $clean_text);

        $message = json_encode($clean_text);
        return json_decode(preg_replace("#(\\\ud[0-9a-f]{3})#i", "", $message), true);
        //return $clean_text;
    }
}