<?php

namespace app\myadmin\controller\general;

use addons\myadmin\library\Backend;
use app\common\library\Email;
use addons\myadmin\model\Config as ConfigModel;
use addons\myadmin\model\ConfigValue as ConfigValueModel;
use think\Cache;
use think\Db;
use think\Exception;
use think\Validate;

/**
 * 系统配置
 *
 * @icon   fa fa-cogs
 * @remark 配置选项为官方同意配置，暂不支持添加其他选项，如果有特殊需求，可以联系官方给予解决方案。
 */
class Config extends Backend
{

    /**
     * @var \app\store\model\Config
     */
    protected $model = null;
    protected $noNeedRight = ['check', 'rulelist', 'selectpage', 'get_fields_list'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \addons\myadmin\model\Config;
        $this->valueModel = new ConfigValueModel;
        ConfigModel::event('before_write', function ($row) {
            if (isset($row['name']) && $row['name'] == 'name' && preg_match("/fast" . "admin/i", $row['value'])) {
                throw new Exception(__("Site name incorrect"));
            }
        });
    }

    /**
     * 查看
     */
    public function index()
    {
        $siteList = [];
        $groupList = ConfigModel::getGroupList();
        foreach ($groupList as $k => $v) {
            $siteList[$k]['name'] = $k;
            $siteList[$k]['title'] = $v;
            $siteList[$k]['list'] = [];
        }
        $value_array = $this->valueModel->where('company_id', COMPANY_ID)->column('value', 'name');
        foreach ($this->model->all() as $k => $v) {
            if (!isset($siteList[$v['group']])) {
                continue;
            }
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
            $siteList[$v['group']]['list'][] = $value;
        }
        $index = 0;
        foreach ($siteList as $k => &$v) {
            $v['active'] = !$index ? true : false;
            $index++;
        }
        $this->view->assign('siteList', $siteList);
        $this->view->assign('typeList', ConfigModel::getTypeList());
        $this->view->assign('ruleList', ConfigModel::getRegexList());
        $this->view->assign('groupList', ConfigModel::getGroupList());
        return $this->view->fetch();
    }


    /**
     * 编辑
     * @param null $ids
     */
    public function edit($ids = null)
    {
        if ($this->request->isPost()) {
            $this->token();
            $row = $this->request->post("row/a", [], 'trim');
            if ($row) {
                $configList = [];
                $name_array = [];
                foreach ($this->model->all() as $v) {
                    if (isset($row[$v['name']])) {
                        $value = $row[$v['name']];
                        if (is_array($value) && isset($value['field'])) {
                            $value = json_encode(ConfigModel::getArrayData($value), JSON_UNESCAPED_UNICODE);
                        } else {
                            $value = is_array($value) ? implode(',', $value) : $value;
                        }
                        $configList[$v['name']] = [
                            'company_id' => COMPANY_ID,
                            'name' => $v['name'],
                            'value' => $value,
                        ];
                        $name_array[] = $v['name'];
                    }
                }
                // 获取已存在的name
                $values = $this->valueModel->where('name', 'in', $name_array)->where('company_id', COMPANY_ID)->select();
                $hasConfigList = [];
                if ($values) {
                    foreach ($values as $vl) {
                        if (isset($configList[$vl['name']])) {
                            $hasConfigList[] = array_merge($configList[$vl['name']], ['id' => $vl['id']]);
                            unset($configList[$vl['name']]);
                        }
                    }
                }
                try {
                    if ($hasConfigList) {
                        $this->valueModel->allowField(true)->saveAll($hasConfigList);
                    }
                    $this->valueModel->allowField(true)->saveAll($configList);
                } catch (Exception $e) {
                    $this->error($e->getMessage());
                }
                $this->success();
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
    }

    /**
     * 规则列表
     * @internal
     */
    public function rulelist()
    {
        //主键
        $primarykey = $this->request->request("keyField");
        //主键值
        $keyValue = $this->request->request("keyValue", "");

        $keyValueArr = array_filter(explode(',', $keyValue));
        $regexList = \addons\myadmin\model\Config::getRegexList();
        $list = [];
        foreach ($regexList as $k => $v) {
            if ($keyValueArr) {
                if (in_array($k, $keyValueArr)) {
                    $list[] = ['id' => $k, 'name' => $v];
                }
            } else {
                $list[] = ['id' => $k, 'name' => $v];
            }
        }
        return json(['list' => $list]);
    }

    /**
     * 发送测试邮件
     * @internal
     */
    public function emailtest()
    {
        $row = $this->request->post('row/a');
        $receiver = $this->request->post("receiver");
        if ($receiver) {
            if (!Validate::is($receiver, "email")) {
                $this->error(__('Please input correct email'));
            }
            \think\Config::set('site', array_merge(\think\Config::get('site'), $row));
            $email = new Email;
            $result = $email
                ->to($receiver)
                ->subject(__("This is a test mail", config('site.name')))
                ->message('<div style="min-height:550px; padding: 100px 55px 200px;">' . __('This is a test mail content', config('site.name')) . '</div>')
                ->send();
            if ($result) {
                $this->success();
            } else {
                $this->error($email->getError());
            }
        } else {
            $this->error(__('Invalid parameters'));
        }
    }

    public function selectpage()
    {
        $id = $this->request->get("id/d");
        $config = \addons\myadmin\model\Config::get($id);
        if (!$config) {
            $this->error(__('Invalid parameters'));
        }
        $setting = $config['setting'];
        //自定义条件
        $custom = isset($setting['conditions']) ? (array)json_decode($setting['conditions'], true) : [];
        $custom = array_filter($custom);

        $this->request->request(['showField' => $setting['field'], 'keyField' => $setting['primarykey'], 'custom' => $custom, 'searchField' => [$setting['field'], $setting['primarykey']]]);
        $this->model = \think\Db::connect()->setTable($setting['table']);
        return parent::selectpage();
    }

    /**
     * 获取表列表
     * @internal
     */
    public function get_table_list()
    {
        $tableList = [];
        $dbname = \think\Config::get('database.database');
        $tableList = \think\Db::query("SELECT `TABLE_NAME` AS `name`,`TABLE_COMMENT` AS `title` FROM `information_schema`.`TABLES` where `TABLE_SCHEMA` = '{$dbname}';");
        $this->success('', null, ['tableList' => $tableList]);
    }

    /**
     * 获取表字段列表
     * @internal
     */
    public function get_fields_list()
    {
        $table = $this->request->request('table');
        $dbname = \think\Config::get('database.database');
        //从数据库中获取表字段信息
        $sql = "SELECT `COLUMN_NAME` AS `name`,`COLUMN_COMMENT` AS `title`,`DATA_TYPE` AS `type` FROM `information_schema`.`columns` WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? ORDER BY ORDINAL_POSITION";
        //加载主表的列
        $fieldList = Db::query($sql, [$dbname, $table]);
        $this->success("", null, ['fieldList' => $fieldList]);
    }
}
