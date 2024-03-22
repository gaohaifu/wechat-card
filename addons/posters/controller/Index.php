<?php

namespace addons\posters\controller;

use app\admin\model\Posters;
use app\admin\model\PostersRecord;
use think\addons\Controller;
use think\Hook;

class Index extends Controller
{

    /**
     * 根据参数预览海报
     *
     * @return array|int|mixed
     * Author: zsw zswemail@qq.com
     */
    public function index()
    {
        if (
            !($id = $this->request->get('id/d'))
            || !($record = (new PostersRecord)->with('posters')->find($id))
            || !$record->posters
        ) {
            $this->error("海报不存在");
        }

        $params = $record->params;
        foreach ($params as $k => $v)
        {
            $type = explode('_', $k, 2)[0];
            switch (strtolower($type))
            {
                case Posters::IMAGE:
                    break;
                case Posters::QR:
                case Posters::TEXT:
                    // 格式 Id=5\nName=啦啦啦
                    $r = preg_split('/[;\r\n]+/s', $v);
                    if (count($r) != 1 || strpos($r[0], '=')) {
                        $val = [];
                        foreach ($r as $vv)
                        {
                            if ( ! strpos($vv, '='))
                            {
                                $this->error("[{$k}]参数错误");
                            }
                            $res = explode('=', $vv, 2);
                            $val[$res[0]] = $res[1];
                        }
                        $params[$k] = $val;
                    }
                    break;
            }
        }

        $content = [
            'id'     => $record->posters->id,
            'params' => $params,
            'output' => true,
            'size'   => $record->size,
        ];

        try{
            Hook::listen('posters', $content, null, true);
            throw new \Exception();
        } catch (\Exception $e){
            var_dump($e->getMessage());
//            $this->error("海报生成失败");
        }
    }

}
