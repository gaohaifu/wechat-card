<?php

namespace addons\myadmin\library\traits;

use think\Config;

/**
 * 通用数据处理模型
 */
trait AttrModel
{

    /**
     * 封面
     */
    public function getIconAttr($value, $data)
    {
        return $this->AothUrl($value);
    }

    /**
     * 封面
     */
    public function getCoverAttr($value, $data)
    {
        return $this->AothUrl($value);
    }

    /**
     * 图片
     */
    public function getImageAttr($value, $data)
    {
        return $this->AothUrl($value);
    }
    public function getImagesAttr($value, $data)
    {
        return $this->AothUrl($value, true);
    }

    /**
     * 视频
     */
    public function getVideoAttr($value, $data)
    {
        return $this->AothUrl($value);
    }
    public function getVideosAttr($value, $data)
    {
        return $this->AothUrl($value,true);
    }

    /**
     * 音频
     */
    public function getAudioAttr($value, $data)
    {
        return $this->AothUrl($value);
    }
    public function getAudiosAttr($value, $data)
    {
        return $this->AothUrl($value,true);
    }

    /**
     * 内容解析地址替换
     */
    public function getContentAttr($value, $data)
    {
        if ($value) {
            $mimetype = str_replace(',', '|.', Config::get('upload.mimetype'));
            preg_match_all("/src=['|\"](.*?(?:[" . $mimetype . "]))['|\"].*?[\/]?>/", $value, $match);
            foreach ($match[1] as $img) {
                $value = str_replace($img, cdnurl($img, true), $value);
            }
        }
        return $value;
    }

    public function StrToArr($value)
    {
        if ($value) {
            return explode(',', $value);
        }
        return [];
    }

    /**
     * 自动处理文件url
     */
    public function AothUrl($value, $arr = false)
    {
        if ($value) {
            $list = [];
            $value_list = explode(',', $value);
            if ($arr && count($value_list) > 0) {
                foreach ($value_list as $ov) {
                    $list[] = cdnurl($ov, true);
                }
                return $list;
            }
            return cdnurl($value, true);
        }
        return $arr ? [] : '';
    }
}
