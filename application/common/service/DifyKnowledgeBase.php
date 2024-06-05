<?php
namespace app\common\service;

class DifyKnowledgeBase extends Base
{   
    private $apiUrl;
    private $apiKey;

    /**
     * 构造函数，初始化 API 基础 URL 和 API 密钥
     * 
     * @param string $apiUrl API 基础 URL
     * @param string $apiKey API 密钥
     */
    public function __construct()
    {
        $apiUrl = 'http://47.100.98.244:8080';
        $apiKey = 'dataset-QW2pgGghbrVSILkxnt53qDNl';

        $this->apiUrl = rtrim($apiUrl, '/') . '/v1/datasets';
        $this->apiKey = $apiKey;
    }

    /**
     * 发送 HTTP 请求的方法
     * @param string $method HTTP 方法（GET, POST, DELETE 等）
     * @param string $endpoint API 端点
     * @param array|null $data 请求数据（可选）
     * @param bool $isFile 是否为文件上传（可选）
     * @return array|null 请求响应
     * @throws Exception 请求失败时抛出异常
     */
    private function request($method, $endpoint = '', $data = null, $isFile = false)
    {
        $url = $this->apiUrl . $endpoint;
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
        ];

        if ($isFile) {
            // 处理文件上传
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } elseif ($data) {
            // 处理 JSON 数据
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $headers[] = 'Content-Type: application/json';
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * 创建空知识库
     * @param string $name 知识库名称
     * @return array 创建的知识库信息
     */
    public function createKnowledgeBase($name)
    {
        return $this->request('POST', '', ['name' => $name]);
    }

    /**
     * 获取所有知识库
     * @return array 知识库列表
     */
    public function getKnowledgeBases()
    {
        return $this->request('GET');
    }

    /**
     * 从文本创建文档
     * @param string $datasetId 知识库 ID
     * @param string $name 文档名称
     * @param string $text 文档内容
     * @return array 创建的文档信息
     */
    public function createDocumentFromText($datasetId, $name, $text)
    {
        $endpoint = "/$datasetId/document/create_by_text";
        $data = [
            'name' => $name,
            'text' => $text,
            'indexing_technique' => 'high_quality',
            'process_rule' => [
                'rules' => [
                    'pre_processing_rules' => [
                        ['id' => 'remove_extra_spaces', 'enabled' => true],
                        ['id' => 'remove_urls_emails', 'enabled' => true]
                    ],
                    'segmentation' => [
                        'separator' => '###',
                        'max_tokens' => 500
                    ]
                ],
                'mode' => 'custom'
            ]
        ];
        return $this->request('POST', $endpoint, $data);
    }
    /**
     * 从文本更新文档
     * @param string $datasetId 知识库 ID
     * @param string $name 文档名称
     * @param string $text 文档内容
     * @return array 创建的文档信息
     */
    public function updateDocumentFromText($datasetId,$document_id, $name, $text)
    {
        $endpoint = "/$datasetId/documents/$document_id/update_by_text";
        $data = [
            'name' => $name,
            'text' => $text,
            'indexing_technique' => 'high_quality',
            'process_rule' => [
                'rules' => [
                    'pre_processing_rules' => [
                        ['id' => 'remove_extra_spaces', 'enabled' => true],
                        ['id' => 'remove_urls_emails', 'enabled' => true]
                    ],
                    'segmentation' => [
                        'separator' => '###',
                        'max_tokens' => 500
                    ]
                ],
                'mode' => 'custom'
            ]
        ];
        return $this->request('POST', $endpoint, $data);
    }

    /**
     * 从文件创建文档
     * @param string $datasetId 知识库 ID
     * @param string $filePath 文件路径
     * @param string $name 文档名称
     * @return array 创建的文档信息
     */
    public function createDocumentFromFile($datasetId, $filePath, $name)
    {
        $endpoint = "/$datasetId/document/create_by_file";
        $data = [
            'data' => json_encode([
                'name' => $name,
                'indexing_technique' => 'high_quality',
                'process_rule' => [
                    'rules' => [
                        'pre_processing_rules' => [
                            ['id' => 'remove_extra_spaces', 'enabled' => true],
                            ['id' => 'remove_urls_emails', 'enabled' => true]
                        ],
                        'segmentation' => [
                            'separator' => '###',
                            'max_tokens' => 500
                        ]
                    ],
                    'mode' => 'custom'
                ]
            ]),
            'file' => new CURLFile($filePath)
        ];
        return $this->request('POST', $endpoint, $data, true);
    }

    /**
     * 获取指定知识库的文档列表
     * @param string $datasetId 知识库 ID
     * @return array 文档列表
     */
    public function getDocuments($datasetId)
    {
        $endpoint = "/$datasetId/documents";
        return $this->request('GET', $endpoint);
    }

    /**
     * 删除指定的文档
     * @param string $datasetId 知识库 ID
     * @param string $documentId 文档 ID
     * @return array 删除操作的结果
     */
    public function deleteDocument($datasetId, $documentId)
    {
        $endpoint = "/$datasetId/documents/$documentId";
        return $this->request('DELETE', $endpoint);
    }
}