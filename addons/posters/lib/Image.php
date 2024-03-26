<?php

namespace addons\posters\lib;

/**
 *
 * Class Image
 *
 * Author: zsw zswemail@qq.com
 *
 * @method static self|int|string left() 距离|center 水平居中
 * @method static self|int|string top() 距离|center 垂直居中
 * @method static self|int right()
 * @method static self|int bottom()
 * @method static self|int width()
 * @method static self|int height()
 * @method static self|int radius()
 * @method static self|int opacity()
 *
 */
class Image extends Collection implements ImageInterface
{

    protected $items
        = [
            'image'   => null,
            'type'    => 'png',
            'left'    => 0, //左边距
            'top'     => 0, //上边距
            'width'   => 100, //宽  宽高非真实图片尺寸
            'height'  => 100, //高
            'radius'  => 0, //圆角度数，最大值为显示宽度的一半
            'opacity' => 100, //透明度
        ];

    protected $readonly = ['image', 'type'];

    /**
     * @param  string | array  $image  图片Url | 本地图片地址 | [宽度,高度]
     *
     * @param  array  $items
     *
     * @throws \Exception
     */
    public function __construct($image, array $items = [])
    {
        parent::__construct($items);
        $this->init($image);
    }

    /**
     * @param  string | array  $image  图片Url | 本地图片地址 | [宽度,高度]
     */
    private function init($image)
    {
        if (is_string($image) && (strpos($image, 'http') === 0 || is_file($image))) {
            $imageInfo = getimagesize($image);
            $this->items['type'] = image_type_to_extension($imageInfo[2], false);
            $imageData = call_user_func('imagecreatefrom'.$this->type, $image);
            $imagesX = imagesx($imageData);
            $imagesY = imagesy($imageData);
            $this->init([$this->items['width'], $this->items['height']]);
            imagealphablending($this->image, false);
            imagecopyresampled($this->image, $imageData, 0, 0, 0, 0, $this->items['width'], $this->items['height'], $imagesX, $imagesY);
        } elseif (is_array($image) && count($image) === 2) {
            $this->width = $image[0];
            $this->height = $image[1];
            $this->items['image'] = imageCreatetruecolor($this->width, $this->height);
            // 设置透明背景
            imagecolortransparent($this->image, imagecolorallocatealpha($this->image, 255, 255, 255, 127));
            imagesavealpha($this->image, true);
        } else {
            throw new PosterException("图片[{$image}]不存在！");
        }
    }

    /**
     * 背景填充
     *
     * 图片背景下不能填充
     *
     * @param  int  $red
     * @param  int  $green
     * @param  int  $blue
     * @param  int  $alpha
     *
     * @return $this
     */
    public function fill($red = 255, $green = 255, $blue = 255, $alpha = 0): self
    {
        $transparent = imagecolorallocatealpha($this->image, $red, $green, $blue, $alpha);
        imagefill($this->image, 0, 0, $transparent);

        return $this;
    }

    public function __destruct()
    {
        is_resource($this->image) && ImageDestroy($this->image);
    }

    /**
     * 返回二进制图片
     */
    public function show()
    {
        ob_start();
        call_user_func('Image'.$this->type, $this->image);
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    /**
     * 打印图片
     */
    public function output()
    {
        ob_end_clean();
        header("content-type:image/png");
        echo $this->show();
        die;
    }


    public function save(string $filename)
    {
        $filename = str_replace('\\', '/', $filename);
        $dir = mb_substr($filename, 0, strrpos($filename, '/'));
        if ( ! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        call_user_func('Image'.$this->type, $this->image, $filename);

        return $this;
    }

    /**
     * 添加图片
     *
     * @param  ImageInterface  $image
     *
     * @return $this
     */
    public function addImage(ImageInterface $image): self
    {
        $resource = $image->image();
        imagesavealpha($resource, true);
        $iWidth = imagesx($resource);
        $iHeight = imagesy($resource);

        $margin = $image->margin ?: 0;
        $imageW = 2 * $margin + $image->width;
        $imageH = 2 * $margin + $image->height;
        if ($image->radius > 0) {
            if ($image->width > $iWidth) {
                $image->width = $iWidth;
            }

            if ($image->height > $iHeight) {
                $image->height = $iHeight;
            }

            // 最大圆角度数不能超过50%
            if ($image->radius > round($image->width / 2)) {
                $image->radius = round($image->width / 2);
            }

            $canvas = Helper::generateRadius($resource, $image->width, $image->height, $image->radius);
        } else {
            $canvas = imagecreatetruecolor($imageW, $imageH);
            //创建透明背景色，主要127参数，其他可以0-255，因为任何颜色的透明都是透明

            $color = [255, 255, 255, 0];
            if ($margin) {
                $color = Helper::rgbToArray($image->marginFill);
            }

            $transparent = imagecolorallocatealpha($canvas, $color[0], $color[1], $color[2], 127 - ($color[3] * 127));

            if ($color[3] === 0) {
                //指定颜色为透明
                imagecolortransparent($canvas, $transparent);
                //保留透明颜色
                imagesavealpha($canvas, true);
            }

            //填充图片颜色
            imagefill($canvas, 0, 0, $transparent);
            //关键函数，参数（目标资源，源，目标资源的开始坐标x,y, 源资源的开始坐标x,y,目标资源的宽高w,h,源资源的宽高w,h）
            imagecopyresampled($canvas, $resource, $margin, $margin, 0, 0, $image->width, $image->height, $iWidth, $iHeight);
        }

        $left = $image->left;
        if ($image->left === 'center') {
            $left = ($this->width - $imageW) / 2;
        }

        $top = $image->top;
        if ($image->top === 'center') {
            $top = ($this->height - $imageW) / 2;
        }

        //放置图像
        imagecopymerge($this->image, $canvas, $left, $top, 0, 0, $imageW, $imageH, $image->opacity); //左，上，右，下，宽度，高度，透明度

        return $this;
    }

    /**
     * 添加文本
     *
     * @param  TextInterface  $text
     *
     * @return $this
     */
    public function addText(TextInterface $text): self
    {
        mb_internal_encoding("UTF-8");
        $font = $text->font;
        if ( ! is_file($font)) {
            throw new PosterException("字体文件不存在: {$this->font}");
        }
        $content = $text->getText();
        list($R, $G, $B, $A) = $text->color;
        $fontColor = imagecolorallocatealpha($this->image, $R, $G, $B, $A <= 1 ? (127 - $A * 127) : $A);

        $textAlign = strtolower($text->textAlign);
        $size = $text->fontSize / 96 * 72;// px转成榜
        $fontBox = imagettfbbox($size, 0, $font, $content);
        $fontWidth = $fontBox[2] - $fontBox[0];// 文字宽度
        switch ($textAlign){
            case 'center':
                $text->left += ceil(($text->width - $fontWidth)  / 2);
                break;
            case 'right':
                $text->left += $text->width - $fontWidth;
                break;
            case 'left':
            default:
                break;
        }

        $text->left = $text->left < 0 ? $this->width - abs($text->left) : $text->left;
        $text->top = $text->top < 0 ? $this->height - abs($text->top) : $text->top;

        // 处理行高偏移
        $topOffset = $text->fontSize + ($text->lineHeight - $text->fontSize) / 2 - 3;
        $lineHeightOffset = 0.83;
        $fontSizeOffset = $text->fontSize / 4;
        imagefttext(
            $this->image,
            $text->fontSize - $fontSizeOffset,
            $text->angle,
            $text->left,
            $text->top + $topOffset,
            $fontColor,
            $font,
            $content,
            [
                'linespacing' => ($text->lineHeight / $text->fontSize) * $lineHeightOffset,
            ]
        );

        return $this;
    }

}
