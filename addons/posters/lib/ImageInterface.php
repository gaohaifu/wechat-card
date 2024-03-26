<?php

namespace addons\posters\lib;

interface ImageInterface
{

    /**
     * 打印图片
     *
     * @return string|bool
     */
    public function show();

    /**
     * 保存图片
     *
     * @param string $filename
     *
     * @return mixed
     */
    public function save(string $filename);


}
