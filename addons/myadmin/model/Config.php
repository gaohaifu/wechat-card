<?php

namespace addons\myadmin\model;

use think\Model;
use addons\myadmin\model\ConfigValue;

/**
 * 配置模型
 */
class Config extends Model
{

    // 表名,不含前缀
    protected $name = 'myadmin_config';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;
    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    // 追加属性
    protected $append = [
        'extend_html'
    ];
    protected $type = [
        'setting' => 'json',
    ];

    /**
     * 读取配置类型
     * @return array
     */
    public static function getTypeList()
    {
        $typeList = [
            'string'        => __('String'),
            'password'      => __('Password'),
            'text'          => __('Text'),
            'editor'        => __('Editor'),
            'number'        => __('Number'),
            'date'          => __('Date'),
            'time'          => __('Time'),
            'datetime'      => __('Datetime'),
            'datetimerange' => __('Datetimerange'),
            'select'        => __('Select'),
            'selects'       => __('Selects'),
            'image'         => __('Image'),
            'images'        => __('Images'),
            'file'          => __('File'),
            'files'         => __('Files'),
            'switch'        => __('Switch'),
            'checkbox'      => __('Checkbox'),
            'radio'         => __('Radio'),
            'city'          => __('City'),
            'selectpage'    => __('Selectpage'),
            'selectpages'   => __('Selectpages'),
            'array'         => __('Array'),
            'custom'        => __('Custom'),
        ];
        return $typeList;
    }

    public static function getRegexList()
    {
        $regexList = [
            'required' => '必选',
            'digits'   => '数字',
            'letters'  => '字母',
            'date'     => '日期',
            'time'     => '时间',
            'email'    => '邮箱',
            'url'      => '网址',
            'qq'       => 'QQ号',
            'IDcard'   => '身份证',
            'tel'      => '座机电话',
            'mobile'   => '手机号',
            'zipcode'  => '邮编',
            'chinese'  => '中文',
            'username' => '用户名',
            'password' => '密码'
        ];
        return $regexList;
    }

    public function getExtendHtmlAttr($value, $data)
    {
        $result = preg_replace_callback("/\{([a-zA-Z]+)\}/", function ($matches) use ($data) {
            if (isset($data[$matches[1]])) {
                return $data[$matches[1]];
            }
        }, $data['extend']);
        return $result;
    }

    /**
     * 读取分类分组列表
     * @return array
     */
    public static function getGroupList()
    {
        $config = get_addon_config('myadmin'); 
        $groupList = isset($config['configgroup'])?$config['configgroup']:[];
        foreach ($groupList as $k => &$v) {
            $v = __($v);
        }
        return $groupList;
    }

    public static function getArrayData($data)
    {
        if (!isset($data['value'])) {
            $result = [];
            foreach ($data as $index => $datum) {
                $result['field'][$index] = $datum['key'];
                $result['value'][$index] = $datum['value'];
            }
            $data = $result;
        }
        $fieldarr = $valuearr = [];
        $field = isset($data['field']) ? $data['field'] : (isset($data['key']) ? $data['key'] : []);
        $value = isset($data['value']) ? $data['value'] : [];
        foreach ($field as $m => $n) {
            if ($n != '') {
                $fieldarr[] = $field[$m];
                $valuearr[] = $value[$m];
            }
        }
        return $fieldarr ? array_combine($fieldarr, $valuearr) : [];
    }

    /**
     * 将字符串解析成键值数组
     * @param string $text
     * @return array
     */
    public static function decode($text, $split = "\r\n")
    {
        $content = explode($split, $text);
        $arr = [];
        foreach ($content as $k => $v) {
            if (stripos($v, "|") !== false) {
                $item = explode('|', $v);
                $arr[$item[0]] = $item[1];
            }
        }
        return $arr;
    }

    /**
     * 将键值数组转换为字符串
     * @param array $array
     * @return string
     */
    public static function encode($array, $split = "\r\n")
    {
        $content = '';
        if ($array && is_array($array)) {
            $arr = [];
            foreach ($array as $k => $v) {
                $arr[] = "{$k}|{$v}";
            }
            $content = implode($split, $arr);
        }
        return $content;
    }

    /**
     * 本地上传配置信息
     * @return array
     */
    public static function upload()
    {
        $uploadcfg = config('upload');

        $uploadurl = request()->module() ? $uploadcfg['uploadurl'] : ($uploadcfg['uploadurl'] === 'ajax/upload' ? 'index/' . $uploadcfg['uploadurl'] : $uploadcfg['uploadurl']);

        if (!preg_match("/^((?:[a-z]+:)?\/\/)(.*)/i", $uploadurl) && substr($uploadurl, 0, 1) !== '/') {
            $uploadurl = url($uploadurl, '', false);
        }
        $uploadcfg['fullmode'] = isset($uploadcfg['fullmode']) && $uploadcfg['fullmode'] ? true : false;
        $uploadcfg['thumbstyle'] = $uploadcfg['thumbstyle'] ?? '';

        $upload = [
            'cdnurl'     => $uploadcfg['cdnurl'],
            'uploadurl'  => $uploadurl,
            'bucket'     => 'local',
            'maxsize'    => $uploadcfg['maxsize'],
            'mimetype'   => $uploadcfg['mimetype'],
            'chunking'   => $uploadcfg['chunking'],
            'chunksize'  => $uploadcfg['chunksize'],
            'savekey'    => $uploadcfg['savekey'],
            'multipart'  => [],
            'multiple'   => $uploadcfg['multiple'],
            'fullmode'   => $uploadcfg['fullmode'],
            'thumbstyle' => $uploadcfg['thumbstyle'],
            'storage'    => 'local'
        ];
        return $upload;
    }

    /**
     * 刷新配置文件
     */
    public static function refreshFile()
    {
        //如果没有配置权限无法进行修改
        if (!\addons\myadmin\library\Auth::instance()->check('general/config/edit')) {
            return false;
        }
        return true;
        $config = [];
        $configList = self::all();
        foreach ($configList as $k => $v) {
            $value = $v->toArray();
            if (in_array($value['type'], ['selects', 'checkbox', 'images', 'files'])) {
                $value['value'] = explode(',', $value['value']);
            }
            if ($value['type'] == 'array') {
                $value['value'] = (array)json_decode($value['value'], true);
            }
            $config[$value['name']] = $value['value'];
        }
        file_put_contents(
            CONF_PATH . 'extra' . DS . 'company.php',
            '<?php' . "\n\nreturn " . var_export_short($config) . ";\n"
        );
    }

    //获取配置信息
    public static function Config($id = null)
    {
        $value_array = ConfigValue::where('company_id', $id)->column('value', 'name');
        $config = Config::all();
        $siteList = [];
        foreach ($config as $k => $v) {
            $v->value = isset($value_array[$v['name']]) ? $value_array[$v['name']] : (isset($v->value) ? $v->value : '');
            $value = $v->toArray();
            $value['title'] = __($value['title']);
            if (in_array($value['type'], ['select', 'selects', 'checkbox', 'radio'])) {
                $value['value'] = explode(',', $value['value']);
            }
            $value['content'] = json_decode($value['content'], true);
            if (in_array($value['name'], ['categorytype', 'configgroup', 'attachmentcategory'])) {
                $dictValue = (array)json_decode($value['value'], true);
                foreach ($dictValue as $index => &$item) {
                    $item = __($item);
                }
                unset($item);
                $value['value'] = json_encode($dictValue, JSON_UNESCAPED_UNICODE);
            }
            $value['tip'] = htmlspecialchars($value['tip']);
            $siteList[$value['name']] = $value['value'];
        }
        return $siteList;
    }
}
