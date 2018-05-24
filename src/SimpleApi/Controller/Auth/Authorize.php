<?php

namespace SimpleApi\Controller\Auth;

use Silex\Application;
use OAuth2\HttpFoundationBridge\Request as BridgeRequest;
use SimpleApi\Services\AuthorizationCode\Login\LoginService;
use SimpleApi\Services\AuthorizationCode\Login\LoginValidation;
use SimpleApi\Services\AuthorizationCode\Login\LoginStorage;
use SimpleApi\Helper\NotificationError;

class Authorize
{
    
    public static function addRoutes($routing)
    {
        $routing->get('/authorize', array(new self(), 'authorize'))->bind('authorize');
        $routing->post('/authorize', array(new self(), 'authorizeFormSubmit'))->bind('authorize_post');
        $routing->get('/authorize_return', array(new self(), 'authorizeReturn'))->bind('authorize_return');
        $routing->get('/authorize_form', array(new self(), 'authorizeForm'))->bind('authorize_form');
    }
    
    public function authorize(Application $app)
    {
        $server   = $app['oauth_server'];
        $response = $app['oauth_response'];
        
        $bridgeRequest = BridgeRequest::createFromRequest($app['request']);
        
        $requestIsValid = $server->validateAuthorizeRequest($bridgeRequest, $response);
        
        if (!$requestIsValid) {
            return $server->getResponse();
        }
        
        return $this->getForm($app);
    }
    
    public function authorizeFormSubmit(Application $app)
    {
        $server       = $app['oauth_server'];
        $response     = $app['oauth_response'];
        $authorized   = (bool) $app['request']->request->get('authorize');
        $userId       = null;
        $loginIsValid = false;
        $loginOrEmail = $app['request']->request->filter('account-user-login-email',FILTER_SANITIZE_EMAIL);
        $password     = $app['request']->request->filter('account-user-password',FILTER_SANITIZE_STRING);
        
        if( !$authorized ){
            $bridgeRequest = BridgeRequest::createFromRequest($app['request']);
            $response = $server->handleAuthorizeRequest($bridgeRequest, $response, $authorized);
        }
        
        if( $authorized ){
            
            $formNotificationError = new NotificationError();
        
            $loginValidation = new LoginValidation();
            $loginValidation->setNotificationErrors($formNotificationError);

            $loginStorage    = new LoginStorage($app['orm.em']);
            $loginService    = new LoginService($loginStorage, $loginValidation, $formNotificationError);

            $loginIsValid = $loginService->checkLogin($loginOrEmail, $password);

        }
        
        if( $authorized  && $loginIsValid ){
            
            $userId = $loginService->getUserId();
            
            $bridgeRequest = BridgeRequest::createFromRequest($app['request']);
            $response      = $server->handleAuthorizeRequest($bridgeRequest, $response, $authorized, $userId);
        }
        
        if( $authorized  && !$loginIsValid ){
           $response = $this->getForm($app, $formNotificationError->getErrors($app['translator'])) ;
        }
        
        return $response;
    }
    
    
    public function authorizeReturn(Application $app)
    {
        $server = $app['oauth_server'];
        $response = $app['oauth_response'];
        $authorized = (bool) $app['request']->request->get('authorize');
        
        var_dump($app['request']->request->all(), $app['request']->query->all(), $app['request']->getContent());
        exit;
        
        return $response;
        
    }
    
    
    public function authorizeForm(Application $app)
    {
        return $this->getForm($wapp);
        
    }
    
    private function getForm($app, $errors = []){
        return $app['twig']->render('server/authorize.twig', array(
            'client_id' => $app['request']->query->get('client_id'),
            'response_type' => $app['request']->query->get('response_type'),
            'errors' => $errors
        ));
    }
    
    
}
