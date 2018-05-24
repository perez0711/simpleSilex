<?php

namespace SimpleApi\Services\Login\Form;

use SimpleApi\Entity\Login;
use SimpleApi\Services\Login\Form\ParserLogin;
use SimpleApi\Services\Login\Form\Storage\LoginStorage;
use SimpleApi\Services\Login\Form\Validation\LoginValidation;
use SimpleApi\Services\Elasticsearch\Login\ElasticsearchLoginService;
use Doctrine\ORM\EntityManager;

class RegraCadastrarLogin
{
    protected $empresaValidation;
    protected $parser;
    protected $storage;
    protected $elasticsearchLoginService;

    protected $notificationError;
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function setLoginValidation(LoginValidation $validation)
    {
        $this->empresaValidation = $validation;
    }

    public function setParserLogin(ParseLogin $parse)
    {
        $this->parser = $parse;
    }

    public function setLoginStorage(LoginStorage $storage)
    {
        $this->storage = $storage;
    }

    public function setElasticsearchLoginService(ElasticsearchLoginService $elasticsearchLoginService)
    {
        $this->elasticsearchLoginService = $elasticsearchLoginService;
    }

    public function cadastrar( $data , Login $loginForm)
    {
        $dataIsValid = $this->empresaValidation->validate($data);
        $loginFormInfo = [];

        if($dataIsValid){
            $this->parser->setLoginFromData($data, $loginForm);
            $this->storage->save($loginForm);
            $this->elasticsearchLoginService->indexar($loginForm->getForm()->getId(),
                $loginForm->getId(),  $loginForm->toArray() );
            $loginFormInfo = $loginForm->toArray();
        }

        return $loginFormInfo;
    }
}