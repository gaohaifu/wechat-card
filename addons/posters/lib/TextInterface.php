<?php

namespace addons\posters\lib;

interface TextInterface
{

    /**
     * 获取文本
     *
     * @param $returnLines
     *
     * @return int|string
     */
    public function getText($returnLines = false);

}
