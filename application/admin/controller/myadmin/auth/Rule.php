<?php

namespace app\admin\controller\myadmin\auth;

use addons\myadmin\model\AuthRule;
use app\common\controller\Backend;
use fast\Tree;
use think\Cache;
use think\Config;
use think\Loader;

/**
 * 规则管理
 *
 * @icon   fa fa-list
 * @remark 规则通常对应一个控制器的方法,同时左侧的菜单栏数据也从规则中体现,通常建议通过控制台进行生成规则节点
 */
class Rule extends Backend
{

    /**
     * @var \addons\myadmin\model\AuthRule
     */
    protected $model = null;
    protected $rulelist = [];
    protected $multiFields = 'ismenu,status';

    public function _initialize()
    {
        parent::_initialize();
        if (!$this->auth->isSuperAdmin()) {
            $this->error(__('Access is allowed only to the super management group'));
        }
        $this->model =  new AuthRule;
        // 必须将结果集转换为数组
        $ruleList = $this->model->field('type,condition,remark,createtime,updatetime', true)->order('weigh DESC,id ASC')->select();
        foreach ($ruleList as $k => &$v) {
            $v['title'] = __($v['title']);
        }
        unset($v);
        Tree::instance()->init($ruleList);
        $this->rulelist = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'title');
        $ruledata = [0 => __('None')];
        foreach ($this->rulelist as $k => &$v) {
            if (!$v['ismenu']) {
                continue;
            }
            $ruledata[$v['id']] = $v['title'];
            unset($v['spacer']);
        }
        unset($v);
        $this->view->assign('ruledata', $ruledata);
        $this->view->assign("menutypeList", $this->model->getMenutypeList());
    }

    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $list = $this->rulelist;
            $total = count($this->rulelist);
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $this->token();
            $params = $this->request->post("row/a", [], 'strip_tags');
            if ($params) {
                if (!$params['ismenu'] && !$params['pid']) {
                    $this->error(__('The non-menu rule must have parent'));
                }
                //这里需要针对name做唯一验证
                $name = 'addons\myadmin\validate\AuthRule';
                $result = $this->model->validate($name)->save($params);
                if ($result === false) {
                    $this->error($this->model->getError());
                }
                Cache::rm('__store_menu__');
                $this->success();
            }
            $this->error();
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isPost()) {
            $this->token();
            $params = $this->request->post("row/a", [], 'strip_tags');
            if ($params) {
                if (!$params['ismenu'] && !$params['pid']) {
                    $this->error(__('The non-menu rule must have parent'));
                }
                if ($params['pid'] == $row['id']) {
                    $this->error(__('Can not change the parent to self'));
                }
                if ($params['pid'] != $row['pid']) {
                    $childrenIds = Tree::instance()->init(collection(AuthRule::select())->toArray())->getChildrenIds($row['id']);
                    if (in_array($params['pid'], $childrenIds)) {
                        $this->error(__('Can not change the parent to child'));
                    }
                }
                //这里需要针对name做唯一验证
                $name = 'addons\myadmin\validate\AuthRule';
                $ruleValidate = \think\Loader::validate($name);
                $ruleValidate->rule([
                    'name' => 'require|format|unique:MyadminAuthRule,name,' . $row->id,
                ]);
                $result = $row->validate($name)->save($params);
                if ($result === false) {
                    $this->error($row->getError());
                }
                Cache::rm('__store_menu__');
                $this->success();
            }
            $this->error();
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if (!$this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ? $ids : $this->request->post("ids");
        if ($ids) {
            $delIds = [];
            foreach (explode(',', $ids) as $k => $v) {
                $delIds = array_merge($delIds, Tree::instance()->getChildrenIds($v, true));
            }
            $delIds = array_unique($delIds);
            $count = $this->model->where('id', 'in', $delIds)->delete();
            if ($count) {
                Cache::rm('__store_menu__');
                $this->success();
            }
        }
        $this->error();
    }



    /**
     * 一键生成菜单
     * @return string|void
     * @throws \think\Exception
     */
    public function import()
    {
        if ($this->request->isPost()) {
            $this->execute();
        }
        return $this->view->fetch('');
    }

    /**
     *
     */
    protected function execute()
    {
        //控制器名
        $controller = $this->request->param('controller') ?: '';
        if (!$controller) {

            //如果不存在就是批量生成
            $adminPath = ROOT_PATH . 'application' . DS . 'myadmin' . DS . 'controller' . DS;
            $filerList = [];
            if ($dh = opendir($adminPath)) {
                while (($file = readdir($dh)) !== false) {
                    if (is_dir($adminPath . $file) && $file != '.' && $file != '..') {
                        $filerList[] = ($adminPath . $file);
                    }
                }
                closedir($dh);
            }

            $controller = [];
            foreach ($filerList as $filer) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($filer),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );
                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $controller[] = str_replace(array(dirname($adminPath) . DS . 'controller' . DS, "\\", '.php'), array("", "/", ''), $file->getRealPath());
                    }
                }
            }
        } else {
            $controller = explode('|', $controller); //分割成数组
        }
        if (!$controller) {
            $this->error("当前插件没有相应的控制器");
        }
        foreach ($controller as $index => $item) {
            if (stripos($item, '_') !== false) {
                $item = Loader::parseName($item, 1);
            }
            if (stripos($item, '/') !== false) {
                $controllerArr = explode('/', $item);
                end($controllerArr);
                $key = key($controllerArr);
                $controllerArr[$key] = ucfirst($controllerArr[$key]);
            } else {
                $controllerArr = [ucfirst($item)];
            }
            $adminPath = str_replace(DS . 'admin' . DS, DS . 'myadmin' . DS, dirname(dirname(__DIR__))) . DS . implode(DS, $controllerArr) . '.php';
            if (!is_file($adminPath)) {
                $this->error('找不到控制器：' . $item);
                return;
            }
            $this->importRule($item);
        }
        $this->success("生成成功!");
    }

    protected function importRule($controller)
    {
        $controller = str_replace('\\', '/', $controller);
        if (stripos($controller, '/') !== false) {
            $controllerArr = explode('/', $controller);
            end($controllerArr);
            $key = key($controllerArr);
            $controllerArr[$key] = ucfirst($controllerArr[$key]);
        } else {
            $key = 0;
            $controllerArr = [ucfirst($controller)];
        }
        $classSuffix = Config::get('controller_suffix') ? ucfirst(Config::get('url_controller_layer')) : '';
        $className = "\\app\\myadmin\\controller\\" . implode("\\", $controllerArr) . $classSuffix;

        $pathArr = $controllerArr;
        array_unshift($pathArr, '', 'application', 'myadmin', 'controller');
        $classFile = ROOT_PATH . implode(DS, $pathArr) . $classSuffix . ".php";
        $classContent = file_get_contents($classFile);
        $uniqueName = uniqid("FastAdmin") . $classSuffix;
        $classContent = str_replace("class " . $controllerArr[$key] . $classSuffix . " ", 'class ' . $uniqueName . ' ', $classContent);
        $classContent = preg_replace("/namespace\s(.*);/", 'namespace ' . __NAMESPACE__ . ";", $classContent);
        $reflector = new \ReflectionClass($className);
        //只匹配公共的方法
        $methods = $reflector->getMethods(\ReflectionMethod::IS_PUBLIC);
        $classComment = $reflector->getDocComment();
        //判断是否有启用软删除
        $softDeleteMethods = ['destroy', 'restore', 'recyclebin'];
        $withSofeDelete = false;
        $modelRegexArr = ["/\\\$this\->model\s*=\s*model\(['|\"](\w+)['|\"]\);/", "/\\\$this\->model\s*=\s*new\s+([a-zA-Z\\\]+);/"];
        $modelRegex = preg_match($modelRegexArr[0], $classContent) ? $modelRegexArr[0] : $modelRegexArr[1];
        preg_match_all($modelRegex, $classContent, $matches);
        if (isset($matches[1]) && isset($matches[1][0]) && $matches[1][0]) {
            \think\Request::instance()->module('myadmin');
            $model = model($matches[1][0]);
            if (in_array('trashed', get_class_methods($model))) {
                $withSofeDelete = true;
            }
        }
        //忽略的类
        if (stripos($classComment, "@internal") !== false) {
            return;
        }
        preg_match_all('#(@.*?)\n#s', $classComment, $annotations);
        $controllerIcon = 'fa fa-circle-o';
        $controllerRemark = '';
        //判断注释中是否设置了icon值
        if (isset($annotations[1])) {
            foreach ($annotations[1] as $tag) {
                if (stripos($tag, '@icon') !== false) {
                    $controllerIcon = substr($tag, stripos($tag, ' ') + 1);
                }
                if (stripos($tag, '@remark') !== false) {
                    $controllerRemark = substr($tag, stripos($tag, ' ') + 1);
                }
            }
        }

        //过滤掉其它字符
        $controllerTitle = trim(preg_replace(array('/^\/\*\*(.*)[\n\r\t]/u', '/[\s]+\*\//u', '/\*\s@(.*)/u', '/[\s|\*]+/u'), '', $classComment));

        //导入中文语言包
        \think\Lang::load(dirname(__DIR__) . DS . 'lang/zh-cn.php');

        //先导入菜单的数据
        $pid = 0;
        foreach ($controllerArr as $k => $v) {
            $key = $k + 1;
            //驼峰转下划线
            $controllerNameArr = array_slice($controllerArr, 0, $key);
            foreach ($controllerNameArr as &$val) {
                $val = strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $val), "_"));
            }
            unset($val);
            $name = implode('/', $controllerNameArr);
            $title = (!isset($controllerArr[$key]) ? $controllerTitle : '');
            $icon = (!isset($controllerArr[$key]) ? $controllerIcon : 'fa fa-list');
            $remark = (!isset($controllerArr[$key]) ? $controllerRemark : '');
            $title = $title ? $title : $v;

            $rulemodel = $this->model->get(['name' => $name]);
            if (!$rulemodel) {
                $this->model
                    ->data(['pid' => $pid, 'name' => $name, 'title' => $title, 'icon' => $icon, 'remark' => $remark, 'ismenu' => 1, 'status' => 'normal'])
                    ->isUpdate(false)
                    ->save();
                $pid = $this->model->id;
            } else {
                $pid = $rulemodel->id;
            }
        }
        $ruleArr = [];
        foreach ($methods as $m => $n) {
            //过滤特殊的类
            if (substr($n->name, 0, 2) == '__' || $n->name == '_initialize') {
                continue;
            }
            //未启用软删除时过滤相关方法
            if (!$withSofeDelete && in_array($n->name, $softDeleteMethods)) {
                continue;
            }
            //只匹配符合的方法
            if (!preg_match('/^(\w+)' . Config::get('action_suffix') . '/', $n->name, $matchtwo)) {
                unset($methods[$m]);
                continue;
            }
            $comment = $reflector->getMethod($n->name)->getDocComment();
            //忽略的方法
            if (stripos($comment, "@internal") !== false) {
                continue;
            }
            //过滤掉其它字符
            $comment = preg_replace(array('/^\/\*\*(.*)[\n\r\t]/u', '/[\s]+\*\//u', '/\*\s@(.*)/u', '/[\s|\*]+/u'), '', $comment);

            $title = $comment ? $comment : ucfirst($n->name);

            //获取主键，作为AuthRule更新依据
            $id = $this->getAuthRulePK($name . "/" . strtolower($n->name));

            $ruleArr[] = array('id' => $id, 'pid' => $pid, 'name' => $name . "/" . strtolower($n->name), 'icon' => 'fa fa-circle-o', 'title' => $title, 'ismenu' => 0, 'status' => 'normal');
        }

        $this->model->isUpdate(false)->saveAll($ruleArr);
    }

    //获取主键
    protected function getAuthRulePK($name)
    {
        if (!empty($name)) {
            $id = $this->model
                ->where('name', $name)
                ->value('id');
            return $id ? $id : null;
        }
    }
}
