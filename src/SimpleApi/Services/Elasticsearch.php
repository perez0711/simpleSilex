<?php

namespace SimpleApi\Services;

class Elasticsearch
{
    public function index($params)
    {
        try {
            $client = $this->getClient();
            return $client->index($params);
        } catch (\Exception $ex) {
        }
    }
    
    public function delete($params)
    {
        try {
            $client = $this->getClient();
            return $client->delete($params);
        } catch (\Exception $ex) {
        }
    }
    
    public function search($params)
    {
        try {
            $client = $this->getClient();
            return $client->search($params);
        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    protected function getClient()
    {
        
        $builderClient = \Elasticsearch\ClientBuilder::create();

        $hosts = [ELASTICSEARCH];
        $builderClient->setHosts($hosts);
                
        $client = $builderClient->build();
        return $client;
    }
}
