<?php

namespace SimpleApi\Controller\Auth;

use Silex\Application;
use OAuth2\HttpFoundationBridge\Request as BridgeRequest;

class Token
{
    public static function addRoutes($routing)
    {
        $routing->post('/token', array(new self(), 'token'))->bind('grant_token');
        $routing->post('/token/revoke', array(new self(), 'revokeToken'))->bind('grant_token_revoke');
    }
    
    public function token(Application $app)
    {
        $server   = $app['oauth_server'];
        $response = $app['oauth_response'];
        
        $bridgeRequest = BridgeRequest::createFromRequest($app['request']);
        return $server->handleTokenRequest($bridgeRequest, $response);
    }
    
    public function revokeToken(Application $app)
    {
        $server   = $app['oauth_server'];
        $response = $app['oauth_response'];
        
        $bridgeRequest = BridgeRequest::createFromRequest($app['request']);
        
        return $server->handleRevokeRequest($bridgeRequest, $response);
    }
}
