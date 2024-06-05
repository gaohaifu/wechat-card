<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use app\common\service\DifyKnowledgeBase;

class Index extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function index()
    {
        return $this->view->fetch();
    }


    public function difyzsk() {

        $dify = new DifyKnowledgeBase();

        // 创建知识库
        //$knowledgeBase = $dify->createKnowledgeBase('My Knowledge Base');
        //print_r($knowledgeBase);
        //exit;
        // 获取所有知识库
        //$knowledgeBases = $dify->getKnowledgeBases();
        //print_r($knowledgeBases);
        //exit;
        $knowledgeBase['id'] = '74667139-35af-4b44-bad4-06d896710d67';
        // 从文本创建文档
        $document = $dify->createDocumentFromText($knowledgeBase['id'], 'Example Document', 'This is an example document.');
        print_r($document);
        exit;
        // 从文件创建文档
        $documentFromFile = $dify->createDocumentFromFile($knowledgeBase['id'], '/path/to/file.txt', 'Example File Document');
        print_r($documentFromFile);

        // 获取文档列表
        $documents = $dify->getDocuments($knowledgeBase['id']);
        print_r($documents);

        // 删除文档
        $dify->deleteDocument($knowledgeBase['id'], $document['id']);
        echo "Document deleted.";
    }
}
