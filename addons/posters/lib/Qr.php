<?php

namespace addons\posters\lib;

require_once __DIR__.'/PhpQrcode.php';

/**
 *
 * Author: zsw zswemail@qq.com
 *
 * @method static self|int level()
 * @method static self|int size()
 * @method static self|int margin()
 * @method static self|string text()
 * @method static self|int|string left() 距离|center 水平居中
 * @method static self|int|string top() 距离|center 垂直居中
 * @method static self|int right()
 * @method static self|int bottom()
 * @method static self|int width()
 * @method static self|int height()
 * @method static self|int opacity()
 *
 */
class Qr extends Collection implements ImageInterface
{
    protected $items
        = [
            'level'   => QR_ECLEVEL_H,
            'size'    => 10,
            'margin'  => 0,
            'marginFill'  => 'rgb(255,255,255, 1)', // 背景填充色 margin大于0有效

            'text'    => '',
            'type'    => 'png',
            'top'     => 0, //上边距
            'left'    => 0, //左边距
            'width'   => 100, //宽
            'height'  => 100, //高
            'radius'  => 0,   // 不支持radius操作
            'opacity' => 100, //透明度
        ];

    protected $readonly = ['type', 'radius', 'height'];

    public function __construct(string $text, array $items = [])
    {
        $this->text = $text;
        parent::__construct($items);
    }

    public function image()
    {
        return imagecreatefromstring($this->show());
    }

    protected function initMargin()
    {
        if ($this->margin > 0) {
            $this->width -= $this->margin * 2;
            $this->height = $this->width;
//            $this->top += $this->margin;
//            $this->left += $this->margin;
        }
    }

    public function show()
    {
        $this->initMargin();
        return \QRcode::pngData($this->text, $this->size, 0);
    }

    /**
     * 二维码图片直接生成其定位配置无效
     *
     * 正确的做法应该是生成指定宽度图片 将其放置
     *
     * @param string $filename
     *
     * @return false|mixed|string
     */
    public function save(string $filename)
    {
        return \QRcode::png($this->text, $filename, $this->level, $this->size, 0);
    }

    protected function setWidthAttr($value)
    {
        $this->items['width'] = $this->items['height'] = $value;
    }

}
