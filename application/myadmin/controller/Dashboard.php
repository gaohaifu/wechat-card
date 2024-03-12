<?php

namespace app\myadmin\controller;


use addons\myadmin\library\Backend;
use addons\myadmin\model\Admin;
use addons\myadmin\model\User;
use addons\myadmin\model\Attachment;
use addons\myadmin\model\Company;
use fast\Date;
use think\Db;

/**
 * 控制台
 *
 * @icon   fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */

class Dashboard extends Backend
{

    /**
     * 查看
     */
    public function index()
    {
        try {
            \think\Db::execute("SET @@sql_mode='';");
        } catch (\Exception $e) {
        }
        $column = [];
        $starttime = Date::unixtime('day', -6);
        $endtime = Date::unixtime('day', 0, 'end');
        $joinlist = Db("myadmin_user")->where('company_id', COMPANY_ID)->where('jointime', 'between time', [$starttime, $endtime])
            ->field('jointime, status, COUNT(*) AS nums, DATE_FORMAT(FROM_UNIXTIME(jointime), "%Y-%m-%d") AS join_date')
            ->group('join_date')
            ->select();
        for ($time = $starttime; $time <= $endtime;) {
            $column[] = date("Y-m-d", $time);
            $time += 86400;
        }
        $userlist = array_fill_keys($column, 0);
        foreach ($joinlist as $k => $v) {
            $userlist[$v['join_date']] = $v['nums'];
        }

        $dbTableList = Db::query("SHOW TABLE STATUS");
        $totalmoney = Company::where('id', COMPANY_ID)->value('money');
        $this->view->assign([
            'totalmoney'      => number_format($totalmoney,2),
            'totalscore'      => Company::where('id', COMPANY_ID)->value('score'),
            'totaluser'       => User::where('company_id', COMPANY_ID)->count(),
            'totalusermoney'  => User::where('company_id', COMPANY_ID)->sum('money'),
            'totaluserscore'  => User::where('company_id', COMPANY_ID)->sum('score'),
            'totaladdon'      => count(get_addon_list()),
            'totaladmin'      => Admin::where('company_id', COMPANY_ID)->where('status', 'in', ['normal', 'hidden'])->count(),
            'totalcategory'   => \app\common\model\Category::count(),
            'todayusersignup' => User::where('company_id', COMPANY_ID)->whereTime('jointime', 'today')->count(),
            'todayuserlogin'  => 0, //User::where('company_id', COMPANY_ID)->whereTime('logintime', 'today')->count(),
            'sevendau'        => User::where('company_id', COMPANY_ID)->whereTime('jointime', '-7 days')->count(),
            'thirtydau'       => User::where('company_id', COMPANY_ID)->whereTime('jointime', '-30 days')->count(),
            'threednu'        => User::where('company_id', COMPANY_ID)->whereTime('jointime', '-3 days')->count(),
            'sevendnu'        => User::where('company_id', COMPANY_ID)->whereTime('jointime', '-7 days')->count(),
            'dbtablenums'     => count($dbTableList),
            'dbsize'          => array_sum(array_map(function ($item) {
                return $item['Data_length'] + $item['Index_length'];
            }, $dbTableList)),
            'attachmentnums'  => Attachment::where('company_id', COMPANY_ID)->count(),
            'attachmentsize'  => Attachment::where('company_id', COMPANY_ID)->sum('filesize'),
            'picturenums'     => Attachment::where('company_id', COMPANY_ID)->where('mimetype', 'like', 'image/%')->count(),
            'picturesize'     => Attachment::where('company_id', COMPANY_ID)->where('mimetype', 'like', 'image/%')->sum('filesize'),
        ]);

        $this->assignconfig('column', array_keys($userlist));
        $this->assignconfig('userdata', array_values($userlist));

        return $this->view->fetch();
    }
}
