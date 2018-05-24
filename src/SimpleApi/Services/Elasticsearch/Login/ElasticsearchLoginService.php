<?php

namespace SimpleApi\Services\Elasticsearch\Login;

use SimpleApi\Services\Elasticsearch;

class ElasticsearchLoginService
{
    public function indexar($index, $id, $content)
    {
        $loginMonitoramentoElasticsearch = $this->getElasticsearchService();

        $loginMonitoramentoElasticsearch->index([
            'index' => $index,
            'type' => 'loginMonitoramento',
            'id'   => $id,
            'body' => $content
        ]);
    }

    public function desindexar($index, $id)
    {
        $loginMonitoramentoElasticsearch = $this->getElasticsearchService();

        $loginMonitoramentoElasticsearch->delete([
            'index' => $index,
            'type' => "loginMonitoramento",
            'id' => $id
        ]);
    }

    public function buscar($index, $body)
    {
        $loginMonitoramentoElasticsearch = $this->getElasticsearchService();

        $params = [
            'index' => $index,
            'type' => "loginMonitoramento",
            'body' => $body
        ];

        return $loginMonitoramentoElasticsearch->search($params);
    }

    protected function getElasticsearchService()
    {
        return new Elasticsearch();
    }
}
