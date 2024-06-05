<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Config;
use think\console\Input;
use think\Db;
use think\Exception;

class Editpage extends Backend
{
    public function _initialize()
    {
        parent::_initialize();
        if (!$this->auth->isSuperAdmin()) {
            $this->error('只有超级管理员可以使用');
        }
        if (!config('app_debug')) {
            $this->error('只在调试模式下可用');
        }
    }

    public function index()
    {
        $module = $this->request->request('module');
        $c = str_replace('.', '/', $this->request->request('c'));
        $a = $this->request->request('a');
        $type = $this->request->request('type');

        switch ($type) {
            case 'c':
                $data['filepath'] = $this->getControllerPath($c);
                $data['language_type'] = 'php';
                break;
            case 'm':
                $data['filepath'] = $this->getModelPath($c);
                $data['language_type'] = 'php';
                break;
            case 'v':
                $data['filepath'] = strtolower(APP_PATH . $module . '/view/' . $this->toUnderScore($c) . '/' . $this->toUnderScore($a) . '.html');
                $data['language_type'] = 'html';
                break;
            case 'j':
                $data['filepath'] = strtolower('assets/js/backend/' . $this->toUnderScore($c) . '.js');
                $data['language_type'] = 'javascript';
                break;
            default:
                $lang = strip_tags($this->request->langset());
                $lang = $lang ? $lang : config('default_lang');
                $data['filepath'] = strtolower(APP_PATH . $module . '/lang/' . $lang . '/' . $this->toUnderScore($c) . '.php');
                $data['language_type'] = 'php';
                break;
        }

        $editpage_config = get_addon_config('editpage');
        if (file_exists($data['filepath'])) {
            $data['size'] = filesize($data['filepath']);//返回文件的字节
            $data['size'] = round($data['size'] / 1024, 2);
            $data['fileatime'] = date('Y-m-d H:i:s', fileatime($data['filepath']));
            $data['filectime'] = date('Y-m-d H:i:s', filectime($data['filepath']));
            $data['code'] = file_get_contents($data['filepath']);
        } else {
            $data['code'] = '抱歉，没有找到相关文件！';
            $editpage_config['setreadonly'] = 1;
        }
        $data['editpage_config'] = $editpage_config;

        $this->assignconfig($data);
        $this->view->assign($data);
        return $this->view->fetch();
    }

    /**
     * 保存文件并备份
     *
     * @param 类型 $arg1 参数一的说明
     * @return array 返回类型
     * @example 示例
     * @author Created by xing <lx@xibuts.cn>
     */
    public function savefile()
    {
        if ($this->request->isPost()) {
            $file = $this->request->request('file');
            $content = $this->request->request('content');
            if (!file_exists($file)) {
                $this->error('文件不存在！');
            }
            //文件备份
            $info = pathinfo($file);
            $backup_path = RUNTIME_PATH . 'editpage_backup/' . date('Ym') . '/';
            if (!is_dir($backup_path)) {
                mkdir($backup_path, 0777, true);
            }
            $backup_path .= date('YmdH') . $info['basename'];
            if (!file_exists($backup_path)) {
                copy($file, $backup_path);
            }

            //重新写入文件
            if (file_put_contents($file, trim($content)) !== false) {
                $this->success();
            } else {
                $this->error('文件写入失败');
            }
        }
    }

    /**
     * 命令行工具
     *
     * @param 类型 $arg1 参数一的说明
     * @return array 返回类型
     * @example 示例
     * @author Created by xing <lx@xibuts.cn>
     */
    public function command()
    {
        if ($this->request->isPost()) {
            //设置过滤方法
            $this->request->filter(['strip_tags', 'trim']);
            $content = $this->request->request('content');
            $contentArr = explode("\n", $content);
            $command = '';
            if (is_array($contentArr)) {
                $command = trim($contentArr[count($contentArr) - 1]);
            }
            if (strpos($command, 'php think') === 0) {
                $command = explode(' ', $command);
                if (isset($command[3])) {
                    $data['type'] = $command[2];
                    unset($command[0]);
                    unset($command[1]);
                    unset($command[2]);
                    $data['params'] = json_encode(array_values($command));
                    $data['command'] = "php think {$data['type']} " . implode(' ', $command);
                    $data['executetime'] = time();
                }
            }

            if (isset($data)) {
                $commandName = "\\app\\admin\\command\\" . ucfirst($data['type']);
                $input = new Input(json_decode($data['params']));
                $output = new \addons\editpage\library\Output();
                $command = new $commandName($data['type']);
                try {
                    $command->run($input, $output);
                    $result = implode("\n", $output->getMessage());
                } catch (Exception $e) {
                    $result = implode("\n", $output->getMessage()) . "\n";
                    $result .= $e->getMessage();
                }
                $result = trim($result);
                $this->success($result);
            }
            exit();
        }
        $editpage_config = get_addon_config('editpage');
        $editpage_config['setreadonly'] = 0;
        $data['editpage_config'] = $editpage_config;
        $this->assignconfig($data);
        $this->view->assign($data);
        return $this->view->fetch();
    }

    /**
     * @notes 得到控制器文件
     * @return string
     * @author 兴
     * @date 2022/10/3 0:01
     */
    private function getControllerPath($controller)
    {
        $controller = str_replace('\\', '/', $controller);
        if (stripos($controller, '/') !== false) {
            $controllerArr = explode('/', $controller);
            end($controllerArr);
            $key = key($controllerArr);
            $controllerArr[$key] = ucfirst($controllerArr[$key]);
        } else {
            $controllerArr = [ucfirst($controller)];
        }
        $classSuffix = Config::get('controller_suffix') ? ucfirst(Config::get('url_controller_layer')) : '';
        $pathArr = $controllerArr;
        array_unshift($pathArr, '', 'application', 'admin', 'controller');
        $classFile = ROOT_PATH . implode(DS, $pathArr) . $classSuffix . ".php";
        return $this->pathAction($classFile);
    }

    /**
     * @notes 得到模型文件
     * @return string
     * @author 兴
     * @date 2022/10/3 0:02
     */
    private function getModelPath($controller)
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
        $pathArr = $controllerArr;
        array_unshift($pathArr, '', 'application', 'admin', 'controller');
        $classFile = $this->pathAction(ROOT_PATH . implode(DS, $pathArr) . $classSuffix . ".php");
        $classContent = file_get_contents($classFile);
        $uniqueName = uniqid("FastAdmin") . $classSuffix;
        $classContent = str_replace(
            "class " . $controllerArr[$key] . $classSuffix . " ",
            'class ' . $uniqueName . ' ',
            $classContent
        );
        $classContent = preg_replace("/namespace\s(.*);/", 'namespace ' . __NAMESPACE__ . ";", $classContent);

        $modelRegexArr = [
            "/\\\$this\->model\s*=\s*model\(['|\"](\w+)['|\"]\);/", "/\\\$this\->model\s*=\s*new\s+([a-zA-Z\\\]+);/"
        ];
        $modelRegex = preg_match($modelRegexArr[0], $classContent) ? $modelRegexArr[0] : $modelRegexArr[1];
        preg_match_all($modelRegex, $classContent, $matches);
        $module = $this->request->request('module');
        if (isset($matches[1]) && isset($matches[1][0]) && $matches[1][0]) {
            if (strpos($matches[1][0], '\\') === false) {
                $matches[1][0] = 'app\\' . $module . '\model\\' . $matches[1][0];
            }
            $model = $matches[1][0];
        } else {
            $model = 'app\\' . $module . '\model\\' . $controllerArr[$key];
        }
        if (!class_exists($model) && strpos($model, $module)) {
            $model = str_replace($module, 'common', $model);
        } elseif (!class_exists($model) && strpos($model, 'common')) {
            $model = str_replace('common', $module, $model);
        }
        if (class_exists($model)) {
            $func = new \ReflectionClass($model);
            $model = $func->getFileName();
        }
        return $this->pathAction($model);
    }

    /**
     * @notes 对路径中的文件名处理
     * @return string
     * @author 兴
     * @date 2022/10/3 0:02
     */
    private function pathAction($path)
    {
        $path = str_replace('//', '/', $path);
        if (!file_exists($path)) {
            $info = pathinfo($path);
            $filepath = strtolower($info['dirname']);
            if (isset($info['extension']) && $info['extension'] == 'php') {
                $filepath .= '/' . $info['basename'];
            }
        } else {
            $filepath = $path;
        }
        return $filepath;
    }

    /**
     * @notes 下划线命名到驼峰命名
     * @return string
     * @author 兴
     * @date 2022/10/3 0:02
     */
    private function toCamelCase($str)
    {
        $array = explode('_', $str);
        $result = $array[0];
        $len=count($array);
        if ($len>1) {
            for ($i=1; $i<$len; $i++) {
                $result.= ucfirst($array[$i]);
            }
        }
        return $result;
    }

    private function toUnderScore($str)
    {
        $dstr = preg_replace_callback('/([A-Z]+)/', function ($matchs) {
            return '_' . strtolower($matchs[0]);
        }, $str);
        return trim(preg_replace('/_{2,}/', '_', $dstr), '_');
    }
}
