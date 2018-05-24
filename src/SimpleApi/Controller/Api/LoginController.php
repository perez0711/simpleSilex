<?php

namespace SimpleApi\Controller\Api;

use Silex\Application;
use SimpleApi\Helper\NotificationError;
use SimpleApi\Helper\RequestParamsParser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use SimpleApi\Services\Login\Form\FormLoginService;
use OAuth2\HttpFoundationBridge\Request as BridgeRequest;

class LoginController
{
    const SCOPE_LOGIN_CADASTRAR  = 'loginempresa_cadastrar';
    const SCOPE_LOGIN_ATUALIZAR  = 'loginempresa_atualizar';
    const SCOPE_LOGIN_DELETAR    = 'loginempresa_deletar';
    const SCOPE_LOGIN_VISUALIZAR = 'loginempresa_visualizar';

    public static function addRoutes($routing)
    {
        $routing->put('/login/{id}' , array(new self() , 'updateLogin'))
            ->assert('id_empresa' , '\d+')
            ->value('id_empresa' , 0)
            ->assert('id', '\d+')
            ->value('id', 0)
            ->bind('login_empresa_update');

        $routing->get('/login/{id}', array(new self(), 'buscarLogin'))
            ->assert('id_empresa', '\d+')
            ->value('id_empresa', 0)
            ->assert('id', '\d+')
            ->value('id', 0)
            ->bind('login_empresa_buscar');


        $routing->delete('/login/{id}', array(new self(), 'deleteLogin'))
            ->assert('id_empresa', '\d+')
            ->value('id_empresa', 0)
            ->assert('id', '\d+')
            ->value('id', 0)
            ->bind('login_empresa_delete');

        $routing->post('/login', array(new self(), 'insertLogin'))
            ->assert('id_empresa', '\d+')
            ->value('id_empresa', 0)
            ->bind('login_empresa_insert');

        $routing->post('/login/busca', array(new self(), 'buscarLoginIndexado'))
            ->assert('id_empresa', '\d+')
            ->value('id_empresa', 0)
            ->bind('buscar_login_empresa_indexado');
    }

    public function updateLogin(Application $app)
    {
        $server = $app['oauth_server'];
        $response = $app['oauth_response'];
        $request = $app['request'];

        try {
            $temPermissao = $server->verifyResourceRequest(BridgeRequest::createFromRequest($request) , $response , self::SCOPE_LOGIN_ATUALIZAR);
            $formNotificationError = new NotificationError();

            if ($temPermissao) {
                $data = RequestParamsParser::toArray($request);
                $data['id'] = $request->attributes->get('id');

                $formLoginService = new FormLoginService($formNotificationError , $app['orm.em']);
                $login = $formLoginService->atualizar($data);
            }
            $this->setResponse($response , $temPermissao , $formNotificationError , $app['translator'] , $login , Response::HTTP_OK);
        } catch (\Exception $ex) {
            $response = $this->getResponseError($app , $ex);
        }

        return $response;
    }

    public function insertLogin(Application $app)
    {
        $server   = $app['oauth_server'];
        $response = $app['oauth_response'];
        $request  = $app['request'];
        try {
            $temPermissao = $server->verifyResourceRequest(BridgeRequest::createFromRequest($request), $response , self::SCOPE_LOGIN_CADASTRAR);
            $formNotificationError = new NotificationError();
            $login = [];
            if ($temPermissao) {

                $data = RequestParamsParser::toArray($request);

                $formLoginService = new FormLoginService($formNotificationError, $app['orm.em']);
                $login = $formLoginService->cadastrar($data);
            }
            $this->setResponse($response, $temPermissao, $formNotificationError, $app['translator'], $login, Response::HTTP_OK);
        } catch (\Exception $ex) {
            $response = $this->getResponseError($app, $ex);
        }

        return $response;
    }

    public function deleteLogin(Application $app)
    {
        $server   = $app['oauth_server'];
        $response = $app['oauth_response'];
        $request  = $app['request'];

        try {
            $temPermissao = $server->verifyResourceRequest(BridgeRequest::createFromRequest($request), $response, self::SCOPE_LOGIN_DELETAR);
            $formNotificationError = new NotificationError();

            if ($temPermissao) {
                $data = RequestParamsParser::toArray($request);
                $data['id']            = $request->attributes->get('id');

                $formLoginService = new FormLoginService($formNotificationError, $app['orm.em']);
                $login = $formLoginService->apagar($data);
            }
            $this->setResponse($response, $temPermissao, $formNotificationError, $app['translator'], $login, Response::HTTP_NO_CONTENT);
        } catch (\Exception $ex) {
            $response = $this->getResponseError($app, $ex);
        }

        return $response;
    }

    public function buscarLoginIndexado(Application $app)
    {
        $server   = $app['oauth_server'];
        $response = $app['oauth_response'];
        $request  = $app['request'];

        try {
            $temPermissao = $server->verifyResourceRequest(BridgeRequest::createFromRequest($request), $response, self::SCOPE_LOGIN_VISUALIZAR);
            $formNotificationError = new NotificationError();
            $login = [];
            if ($temPermissao) {

                $body = $request->getContent();

                $formLoginService = new FormLoginService($formNotificationError, $app['orm.em']);
                $login = $formLoginService->buscarIndexado($body);
            }

            $this->setResponse($response, $temPermissao, $formNotificationError, $app['translator'], $login, Response::HTTP_OK);
        } catch (\Exception $ex) {
            $response = $this->getResponseError($app, $ex);
        }

        return $response;
    }

    public function buscarLogin(Application $app)
    {
        $server   = $app['oauth_server'];
        $response = $app['oauth_response'];
        $request  = $app['request'];

        try {
            $temPermissao = $server->verifyResourceRequest(BridgeRequest::createFromRequest($request), $response, self::SCOPE_LOGIN_VISUALIZAR);
            $formNotificationError = new NotificationError();
            $login = [];
            if ($temPermissao) {

                $idLogin = $request->attributes->get('id');

                $formLoginService = new FormLoginService($formNotificationError, $app['orm.em']);
                $login = $formLoginService->buscarLogin($idLogin);
            }

            $this->setResponse($response, $temPermissao, $formNotificationError, $app['translator'], $login, Response::HTTP_OK);
        } catch (\Exception $ex) {
            $response = $this->getResponseError($app, $ex);
        }

        return $response;
    }

    private function setResponse($response, $temPermissao, $formNotificationError, $translator, $responseData, $responseCode)
    {
        if ($temPermissao && !$formNotificationError->hasErrors()) {
            $response->setStatusCode($responseCode);
            $response->setData($responseData);
        } elseif ($temPermissao && $formNotificationError->hasErrors()) {
            $errors = $formNotificationError->getErrors($translator);
            $response->setStatusCode($formNotificationError->getCodigoErro());
            $response->setData(["erros" => $errors]);
        }
    }

    private function getResponseError($app, $ex)
    {
        $app['logger']->critical($ex->getMessage());
        return new JsonResponse(["erro" => $app->trans('erro_500')], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

}
