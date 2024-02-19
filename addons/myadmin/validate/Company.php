<?php

namespace addons\myadmin\validate;

use think\Validate;

class Company extends Validate
{

    /**
     * 验证规则
     */
    protected $rule = [
        'name' => 'require|unique:myadmin_company,name'
    ];

    /**
     * 提示消息
     */
    protected $message = [];

    /**
     * 字段描述
     */
    protected $field = [];

    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['name'],
        'edit' => ['name'],
    ];

    public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->field = [
            'name' => __('CompanyName')
        ];
        $this->message = array_merge($this->message, [            
            'name' => __('Company name is error'),
            'name.unique' => __('Company name is not'),
            'name.regex' => __('Company name is error')
        ]);
        parent::__construct($rules, $message, $field);
    }
}
