<?php

namespace addons\posters\lib;

/**
 *
 * Author: zsw zswemail@qq.com
 *
 * @method static self|string text()
 * @method static self|string overflow()
 * @method static self|int|string left() 距离|center 水平居中
 * @method static self|int|string top() 距离|center 垂直居中
 * @method static self|int width()
 * @method static self|int overflow_text()
 * @method static self|int fontSize()
 * @method static self|int lineHeight()
 * @method static self|string fontPath()
 * @method static self|int angle()
 *
 */
class Text extends Collection implements TextInterface
{
    const OVERFLOW_SPACE = 'space'; // 换行

    const OVERFLOW_ELLIPSIS = 'ellipsis'; // 省略

    protected $items
        = [
            'text'     => '',
            'left'     => 0,
            'top'      => 0,
            'width'    => 0,
            'fontSize' => 20,
            'textAlign' => 'left',
            'lineHeight' => 20,
            'overflow' => self::OVERFLOW_SPACE,
            'overflow_text' => '...',
            'font'     => '',
            'color'    => [0,0,0,0],
            'angle'    => 0,
        ];

    protected $readonly = ['text'];

    public function __construct(string $text, array $items = [])
    {
        $this->items['text'] = $text;
        parent::__construct($items);
        if (!$this->font) {
            $this->font = ROOT_PATH . 'public/assets/addons/posters/WenQuanDengKuanWeiMiHei.ttf';
        }
    }

    public function color($R, $G, $B, $a = 0)
    {
        return $this->set('color', [$R, $G, $B, $a]);
    }

    public function getText($returnLines = false)
    {
        $count = mb_strlen($this->text, 'UTF-8');
        if ($this->width <= 0) {
            return $returnLines ? $count : $this->text;
        }
        $arr = array();
        $newStr = '';
        $counts = 1;
        $lineEnd = false;

        for ($i = 0; $i < $count; $i++) {
            $str = mb_substr($this->text, $i, 1);
            if ($this->isEOF($str)) {
                $arr[] = $str;
                $counts += 1;
                $newStr = '';
                $lineEnd = false;
                continue;
            }
            if ($lineEnd) {
                continue;
            }
            $newStr .= $str;
            if (!is_file($this->font)) {
                throw new PosterException("字体文件不存在: {$this->font}");
            }
            $size = $this->fontSize / 96 * 72 ;// px转成榜
            $box = imagettfbbox($size, $this->angle, $this->font, $newStr);
            if ($box[2] > $this->width) {
                $counts += 1;
                $newStr = '';
                switch ($this->overflow) {
                    case self::OVERFLOW_SPACE:
                        // 超出换行 如果后面是换行符号 将不会自动换行
                        // if (!$this->isEOF(mb_substr($this->text, $i + 1, 1))) {
                        //   $arr[] = PHP_EOL;
                        // }
                        $arr[] = PHP_EOL;
                        $arr[] = $str;
                        break;
                    case self::OVERFLOW_ELLIPSIS:
                        $arr[] = $str;
                        $arr[] = $this->overflow_text;
                        $lineEnd = true;
                        break;
                }
            }else{
                $arr[] = $str;
            }
        }

        return $returnLines ? $counts : trim(implode('', $arr), PHP_EOL);
    }

    private function isEOF($val)
    {
        return in_array($val, ["\n", PHP_EOL, "\r\n"]);
    }

}
