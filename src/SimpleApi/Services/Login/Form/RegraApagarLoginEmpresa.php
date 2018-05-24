<?php

namespace SimpleApi\Services\Login\Form;

use SimpleApi\Entity\Login;
use SimpleApi\Services\Login\Form\Storage\LoginStorage;
use SimpleApi\Services\Elasticsearch\Login\ElasticsearchLoginService;
use SimpleApi\Helper\NotificationError;

class RegraApagarLogin
{

    protected $storage;
    protected $notificationError;
    protected $elasticsearchLoginService;


    public function __construct(LoginStorage $storage , NotificationError $notificationError)
    {
        $this->storage = $storage;
        $this->notificationError = $notificationError;
    }

    public function setElasticsearchLoginService(ElasticsearchLoginService $elasticsearchLoginService)
    {
        $this->elasticsearchLoginService = $elasticsearchLoginService;
    }

    public function apagar(Login $loginForm= null)
    {
        $loginFormInfo = null;

        $this->elasticsearchLoginService->desindexar($loginForm->getForm()->getId(),
            $loginForm->getId(),  $loginForm->toArray() );
        $this->storage->delete($loginForm);

        return $loginFormInfo;
    }

}