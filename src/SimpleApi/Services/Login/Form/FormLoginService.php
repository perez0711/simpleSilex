<?php

namespace SimpleApi\Services\Login\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use SimpleApi\Helper\NotificationError;
use SimpleApi\Entity\Login;

use SimpleApi\Services\Login\Form\Storage\LoginStorage;
use SimpleApi\Services\Login\Form\Storage\LoginAlreadyRegistredStorage;

use SimpleApi\Services\Login\Form\Validation\LoginValidation;
use SimpleApi\Services\Login\Form\Validation\FormLoginValidation;
use SimpleApi\Services\Login\Form\Validation\LoginAlreadyRegistred;

use SimpleApi\Services\Elasticsearch\Login\ElasticsearchLoginService;

class FormLoginService
{
    protected $notificationError;
    protected $em;

    public function __construct(NotificationError $fne, EntityManager $em)
    {
        $this->notificationError = $fne;
        $this->em = $em;
    }

    public function cadastrar( $data )
    {

        $loginFormStorage               =  new LoginStorage($this->em);
        $loginIdAlreadyRegistredStorage = new LoginAlreadyRegistredStorage($this->em);

        $loginIdAlreadyRegistred = new LoginAlreadyRegistred($loginIdAlreadyRegistredStorage, $this->notificationError);
        $formLoginValidation     = new FormLoginValidation();
        $formLoginValidation->setNotificationErrors($this->notificationError);

        $loginFormValidation = new LoginValidation();
        $loginFormValidation->setFormNotificationPanel($this->notificationError);
        $loginFormValidation->setFormValidation($formLoginValidation);
        $loginFormValidation->setLoginIdAlreadyRegistred($loginIdAlreadyRegistred);

        $parseLogin                = new ParseLogin($this->em);
        $elasticsearchLoginService = new ElasticsearchLoginService();

        $formLogin = new RegraCadastrarLogin($this->em);
        $formLogin->setLoginValidation($loginFormValidation);
        $formLogin->setParserLogin($parseLogin);
        $formLogin->setLoginStorage($loginFormStorage);
        $formLogin->setElasticsearchLoginService($elasticsearchLoginService);

        $loginForm = $this->getNovoLogin();
        $loginFormInfo = $formLogin->cadastrar($data, $loginForm);

        return $loginFormInfo;

    }

    public function apagar( $data )
    {
        $loginForm       = $this->getLogin($data);
        $loginFormExiste = !is_null($loginForm);
        $loginFormInfo   = null;

        if (!$loginFormExiste){
            $this->notificationError->addErro('LoginValidation', Response::$statusTexts[Response::HTTP_NOT_FOUND]);
            $this->notificationError->setCodigoErro(Response::HTTP_NOT_FOUND);
        } else {
            $loginFormStorage = new LoginStorage($this->em);

            $elasticsearchLoginService = new ElasticsearchLoginService();

            $formLogin = new RegraApagarLogin($loginFormStorage, $this->notificationError);
            $formLogin->setElasticsearchLoginService($elasticsearchLoginService);

            $loginFormInfo = $formLogin->apagar($loginForm);
        }

        return $loginFormInfo;

    }

    public function atualizar( $data )
    {
        $loginForm      = $this->getLogin($data);
        $loginFormExiste = !is_null($loginForm);
        $loginFormInfo   = null;

        if ($loginFormExiste){

            $loginFormStorage               =  new LoginStorage($this->em);
            $loginIdAlreadyRegistredStorage = new LoginAlreadyRegistredStorage($this->em);

            $loginIdAlreadyRegistred = new LoginAlreadyRegistred($loginIdAlreadyRegistredStorage, $this->notificationError);
            $formLoginValidation     = new FormLoginValidation();
            $formLoginValidation->setNotificationErrors($this->notificationError);

            $loginFormValidation = new LoginValidation();
            $loginFormValidation->setFormNotificationPanel($this->notificationError);
            $loginFormValidation->setFormValidation($formLoginValidation);
            $loginFormValidation->setLoginIdAlreadyRegistred($loginIdAlreadyRegistred);

            $parseLogin                = new ParseLogin($this->em);
            $elasticsearchLoginService = new ElasticsearchLoginService();

            $formLogin = new RegraAtualizarLogin($this->em);
            $formLogin->setLoginValidation($loginFormValidation);
            $formLogin->setParserLogin($parseLogin);
            $formLogin->setLoginStorage($loginFormStorage);
            $formLogin->setElasticsearchLoginService($elasticsearchLoginService);
            $formLogin->setElasticsearchLoginService($elasticsearchLoginService);

            $loginFormInfo = $formLogin->atualizar($data, $loginForm);

        } else {
            $this->notificationError->addErro('login', Response::$statusTexts[Response::HTTP_NOT_FOUND]);
            $this->notificationError->setCodigoErro(Response::HTTP_NOT_FOUND);
        }

        return $loginFormInfo;

    }

    public function buscarIndexado( $index, $body )
    {
        $elasticsearchLoginService = new ElasticsearchLoginService();

        return $elasticsearchLoginService->buscar($index, $body);

    }

    public function buscarLogin($id)
    {
        $loginFormStorage =  new LoginStorage($this->em);
        $loginFormInfo = $loginFormStorage->getLoginPorId($id);

        if(is_null($loginFormInfo)){
            $this->notificationError->addErro('login', Response::$statusTexts[Response::HTTP_NOT_FOUND]);
            $this->notificationError->setCodigoErro(Response::HTTP_NOT_FOUND);
        }

        return $loginFormInfo;
    }

    protected function getNovoLogin()
    {
        $loginForm = new Login();
        $loginForm->setPermissao(Login::PERMISSAO_LOGIN_NORMAL);

        return $loginForm;
    }

    protected function getLogin($data)
    {
        $id               = isset($data['id']) ? $data['id'] : 0;

        $loginFormRepository = $this->em->getRepository(Login::class);
        $loginForm          = $loginFormRepository->find($id);

        return $loginForm;
    }

}