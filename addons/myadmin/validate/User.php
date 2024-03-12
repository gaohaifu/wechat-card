<?php

namespace addons\myadmin\validate;

use think\Validate;

class User extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        //'username' => 'require|regex:\w{3,32}|unique:user',
        //'nickname' => 'require|unique:user',
    ];

    /**
     * 字段描述
     */
    protected $field = [];
    /**
     * 提示消息
     */
    protected $message = [];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => [],
        'edit' => [],
    ];

    public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->field = [
            //'username' => __('Username'),
        ];
        parent::__construct($rules, $message, $field);
    }
}
