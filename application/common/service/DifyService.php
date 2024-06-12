<?php

namespace app\common\service;

use addons\myadmin\model\Company;
use app\common\model\ai\KnowledgeBase;

/**
 * dify相关服务
 */
class DifyService extends Base
{
    // 创建知识库
    public static function createKnowledgeBase($companyId) {
        
        $company = Company::get($companyId);

        $dify = new DifyKnowledgeBase();

        $knowledgeBase = $dify->createKnowledgeBase($company->name);
        if(isset($knowledgeBase['id'])){
            $re = KnowledgeBase::insert([
                'id' => $knowledgeBase['id'],
                'company_id' => $companyId,
                'name' => $company->name,
                'createtime' => time(),
                'updatetime' => time(),
            ]);

            return ['code' => 1,'msg'=>'success','data'=>$knowledgeBase];
        }else{
            abort(0,'dify_create_error');
            return ['code' => 0,'msg'=>'error','data'=>$knowledgeBase];
        }
    }

    public static function createDocumentFromText($knowledgeBaseId,$name,$content) {
        $dify = new DifyKnowledgeBase();

        // 从文本创建文档
        $document = $dify->createDocumentFromText($knowledgeBaseId, $name, $content);
        if(isset($document['document'])){
            return $document;
        }else{
            return false;
        }
    }

    public static function updateDocumentFromText($knowledgeBaseId,$documentId,$name,$content) {
        $dify = new DifyKnowledgeBase();

        // 从文本创建文档
        $document = $dify->updateDocumentFromText($knowledgeBaseId,$documentId, $name, $content);
        var_dump($document);exit;
        if(isset($document['document'])){
            return true;
        }else{
            return false;
        }
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

