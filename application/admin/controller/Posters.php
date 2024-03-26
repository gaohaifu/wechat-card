<?php

namespace app\admin\controller;

use app\admin\model\PostersRecord;
use app\common\controller\Backend;
use think\exception\ValidateException;

/**
 * 自定义海报
 */
class Posters extends Backend
{

    /**
     * Posters模型对象
     * @var \app\admin\model\Posters
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Posters;
    }

    public function add()
    {
        if ($this->request->isAjax()) {
            return $this->save();
        }
        return $this->view->fetch('create');
    }

    public function detail(int $id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            $this->error('海报不存在');
        }
        $config = $model->config;
        $config['title'] = $model->title;
        $this->success('获取成功', '', $config);
    }

    public function edit($ids = null)
    {
        if ($this->request->isAjax()) {
            return $this->save();
        }
        $this->view->assign('id', $ids);
        $this->view->assign('create', false);
        return $this->view->fetch('create');
    }

    public function records(){
        $posters_id = $this->request->get('posters_id');
        $this->model = new PostersRecord();
        list($where, $sort, $order, $offset, $limit) = $this->buildparams(['name']);
        $list = $this->model->where($where)
            ->where('posters_id', $posters_id)
            ->order($sort, $order)
            ->paginate($limit);
        $this->success('获取成功', '', $list);
    }

    public function recordadd()
    {
        try{
            $input = $this->request->only(['id', 'posters_id', 'name', 'size', 'params']);
            $this->validate($input,
                            [
                                'posters_id|海报' => 'require',
                                'name|名称' => 'require',
                                'size|尺寸' => 'require',
                                'params|参数' => 'require',
                            ]
            );
            $model = new PostersRecord();
            if (($id = $input['id'] ?? 0) && (!$model = $model->find($id))) {
                throw new \Exception("记录不存在");
            }
            unset($input['id']);
            if (!$model->save($input)) {
                throw new \Exception("保存失败");
            }
        }catch (ValidateException|\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('保存成功', null, ['id' => $model->id]);
    }

    public function recorddel(){
        if ($id = $this->request->post('id/d')) {
            (new PostersRecord())->where('id', $id)->delete();
        }
        $this->success("删除成功");
    }

    private function save()
    {
        try{
            $id = $this->request->param('id/d', 0);
            $title = $this->request->post('title/s', '');
            $bg = $this->request->post('bg/a', []);
            $materials = $this->request->post('materials/a', []);

            $model = $this->model;

            if ($id > 0) {
                $model = $model->find($id);
                if (!$model) {
                    throw new \Exception('海报不存在');
                }
            }

            $this->failException = true;

            $this->validate(
                array_merge(['title' => $title], $bg),
                [
                    'title|标题' => 'require' . ($id ? '' : '|unique:posters'),
                    'width|背景宽度' => 'require|integer|gt:0',
                    'height|背景高度' => 'require|integer|gt:0',
                    'color|背景颜色' => 'require',
                ]
            );

            if (count($materials) < 1) {
                throw new \Exception('缺少素材');
            }
            
            $materials_sort = array_column($materials, 'zIndex');
            array_multisort($materials_sort, SORT_ASC,$materials);

            foreach ($materials as $m) {
                if (!isset($model::$typeLabels[$m['type']])) {
                    throw new \Exception('未定义的素材类型');
                }
                $c = $m['config'];
                switch ($m['type']) {
                    case $model::IMAGE:
                        if (!$m['generate'] && !$c['image']) {
                            throw new \Exception('请选择素材图片');
                        }
                        break;
                    case $model::TEXT:
                        if (!$m['generate'] && !$c['text']) {
                            throw new \Exception('请设置文本内容');
                        }
                        break;
                    case $model::QR:
                        if (!$c['text']) {
                            throw new \Exception('请设置二维码内容');
                        }
                        break;
                }
            }

            $model->save(['title' => $title, 'config' => ['bg' => $bg, 'materials' => $materials]]);
        }catch (ValidateException $e) {
            $this->error($e->getMessage());
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('保存成功', null, ['id' => $model->id]);
    }

}
