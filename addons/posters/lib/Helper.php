<?php

namespace addons\posters\lib;

use app\admin\model\Posters;

/**
 * Class Helper
 *
 * @package addons\posters\lib
 * Author: zsw zswemail@qq.com
 */
class Helper
{

    public static function rgbToArray( string $rgb ): array
    {
        preg_match("/(\d+),\s*(\d+),\s*(\d+),*\s*([0-9]{1,}[.][0-9]*)?/", $rgb, $data);
        return [$data[1] ?? 0, $data[2] ?? 0, $data[3] ?? 0, $data[4] ?? 1 ];
    }

    /**
     *
     * @param array $config
     * @param array $params
     * @param bool  $output 保存路径 | true 直接打印输出 | false 直接返回内容
     * @param float  $size 图片默认尺寸为 1 可以进行等比放大缩小
     *
     * @throws \Exception
     */
    public static function generate(array $config, array $params = [], $output = false, $size = 1.0)
    {
        $bg = $config['bg'];
        $materials = $config['materials'];
        $bg['width'] *= $size;
        $bg['height'] *= $size;
        foreach ($materials as $k => $v) {
            $c = $v['config'];
            foreach (['left', 'top', 'width', 'height', 'radius', 'fontSize', 'lineHeight'] as $n) {
                if (isset($c[$n])) {
                    $c[$n] *= $size;
                }
            }
            $key = $v['type'] . '_' . $k;
            $generate = $v['generate'];
            if ($generate && !isset($params[$key])) {
                throw new PosterException('缺少素材['.$key.']');
            }
            switch ($v['type']) {
                case Posters::IMAGE:
                    $image = $generate ? $params[$key] : $c['image'];
                    unset($c['image']);
                    if (strpos($image, ROOT_PATH) !== 0 && is_file(ROOT_PATH . 'public' . $image)) {
                        $image = ROOT_PATH . 'public' . $image;
                    } elseif( 0 !== strrpos($image, 'http')) {
                        throw new PosterException('素材图片不存在['.$key.']：' . ROOT_PATH . 'public' . $image);
                    }
                    $model = new Image($image, $c);
                    break;
                case Posters::TEXT:
                case Posters::QR:
                    $text = $c['text'];
                    if ($generate) {
                        $param = $params[$key];
                        if (is_array($param)) {
                            $replace = array_keys($param);
                            foreach ($replace as &$x) {
                                $x = "{:{$x}}";
                            }
                            $text = str_replace($replace, $param, $c['text']);
                        }else{
                            $text = $param;
                        }
                    }
                    unset($c['text']);
                    if ($v['type'] === Posters::TEXT) {
                        $c['color'] = self::rgbToArray($c['color']);
                        $model = new Text($text, $c);
                    }else{
                        $model = new Qr($text, $c);
                    }
                    break;
            }
            $materials[$k]['model'] = $model;
        }

        // 处理图层层级
        $zIndex = array_column($materials,'zIndex');
        array_multisort($zIndex, SORT_ASC, $materials);

        $bgRgb = self::rgbToArray($bg['color']);
        $bgModel = new Image([$bg['width'], $bg['height']]);
        $bgModel->fill($bgRgb[0], $bgRgb[1], $bgRgb[2]);

        foreach ($materials as $k => $v) {
            if ($v['type'] === Posters::TEXT) {
                $bgModel->addText($v['model']);
            }else{
                $bgModel->addImage($v['model']);
            }
        }

        if (true === $output) {
            $bgModel->output();return die(0);
        }elseif (false === $output) {
            return $bgModel->show();
        }

        $bgModel->save($output);
        return true;
    }

    /**
     * 生成圆角图片
     *
     * @param resource $img
     * @param          $w
     * @param          $h
     * @param int      $radius
     *
     * @return false|resource
     */
    public static function generateRadius($img, $w, $h, $radius = 10)
    {
        $resWidth = imagesx($img);
        $resHeight = imagesy($img);

        $image = imagecreatetruecolor($w, $h);
        $bg = imagecolorallocatealpha($image, 255, 255, 255, 127);
        //指定颜色为透明
        imagecolortransparent($image, $bg);
        //保留透明颜色
        imagesavealpha($image, true);
        //填充图片颜色
        imagefill($image, 0, 0, $bg);

        imagecopyresampled($img, $img, 0, 0, 0, 0, $w, $h, $resWidth, $resHeight);

        $r = $radius; //圆 角半径
        for ($x = 0; $x < $w; $x++)
        {
            for ($y = 0; $y < $h; $y++)
            {
                $rgbColor = imagecolorat($img, $x, $y);
                if (($x >= $radius && $x <= ($w - $radius)) || ($y >= $radius && $y <= ($h - $radius)))
                {
                    //不在四角的范围内,直接画
                    imagesetpixel($image, $x, $y, $rgbColor);
                } else
                {
                    //在四角的范围内选择画
                    //上左
                    $yx1 = $r; //圆心X坐标
                    $yy1 = $r; //圆心Y坐标
                    if (((($x - $yx1) * ($x - $yx1) + ($y - $yy1) * ($y - $yy1)) <= ($r * $r)))
                    {
                        imagesetpixel($image, $x, $y, $rgbColor);
                    }
                    //上右
                    $yx2 = $w - $r; //圆心X坐标
                    $yy2 = $r; //圆心Y坐标
                    if (((($x - $yx2) * ($x - $yx2) + ($y - $yy2) * ($y - $yy2)) <= ($r * $r)))
                    {
                        imagesetpixel($image, $x, $y, $rgbColor);
                    }
                    //下左
                    $yx3 = $r; //圆心X坐标
                    $yy3 = $h - $r; //圆心Y坐标
                    if (((($x - $yx3) * ($x - $yx3) + ($y - $yy3) * ($y - $yy3)) <= ($r * $r)))
                    {
                        imagesetpixel($image, $x, $y, $rgbColor);
                    }
                    //下右
                    $yx4 = $w - $r; //圆心X坐标
                    $yy4 = $h - $r; //圆心Y坐标
                    if (((($x - $yx4) * ($x - $yx4) + ($y - $yy4) * ($y - $yy4)) <= ($r * $r)))
                    {
                        imagesetpixel($image, $x, $y, $rgbColor);
                    }
                }
            }
        }

        return $image;
    }

}
