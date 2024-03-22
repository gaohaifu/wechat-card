<?php

namespace addons\posters;

use addons\posters\lib\Helper;
use app\common\library\Menu;
use think\Addons;

/**
 * 插件
 */
class Posters extends Addons
{

    private $name = 'posters';

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name'    => $this->name,
                'title'   => '自定义海报',
                'icon'    => 'fa fa-table',
                'sublist' => [
                    ['name' => $this->name . '/index', 'title' => '查看列表'],
                    ['name' => $this->name . '/add', 'title' => '新增'],
                    ['name' => $this->name . '/detail', 'title' => '详情'],
                    ['name' => $this->name . '/del', 'title' => '删除'],
                    ['name' => $this->name . '/edit', 'title' => '更新'],
                    ['name' => $this->name . '/records', 'title' => '海报记录'],
                    ['name' => $this->name . '/recordadd', 'title' => '修改记录'],
                    ['name' => $this->name . '/recorddel', 'title' => '删除记录'],
                ]
            ]
        ];
        Menu::create($menu);
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return Menu::delete($this->name);
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        return Menu::enable($this->name);
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        return Menu::disable($this->name);
    }

    /**
     * @param array $config
     *
     * @return bool|false|string|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function posters(array $config)
    {
        $model = new \app\admin\model\Posters();

        $where = [];
        if (isset($config['id'])) {
            $where['id'] = $config['id'];
        } else if (isset($config['title'])) {
            $where['title'] = $config['title'];
        }

        if (!count($where) || !$configModel = $model->where($where)->find()) {
            return false;
        }

        return Helper::generate($configModel->config, $config['params'] ?? [], $config['output'] ?? false, $config['size'] ?? 1);
    }

}
