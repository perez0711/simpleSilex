<?php

namespace SimpleApi;

use OAuth2\GrantType\AuthorizationCode;
use Silex\Application;

use OAuth2\HttpFoundationBridge\Response as BridgeResponse;
use OAuth2\GrantType\ClientCredentials;
use OAuth2\Server;
use OAuth2\GrantType\UserCredentials;
use OAuth2\GrantType\RefreshToken;

use SimpleApi\OAuth\GrantType\UserApiCredentials;
use SimpleApi\OAuth\Storage\AuthorizationCodeStorage;

use SimpleApi\Entity\Login;
use SimpleApi\Entity\LoginScope;
use SimpleApi\Entity\LoginAppClient;
use SimpleApi\Entity\LoginAccessToken;
use SimpleApi\Entity\LoginRefreshToken;
use SimpleApi\OAuth\Storage\UserApiCredentialsStorage;

abstract class AbstractControllerProvider
{
    
    protected function setup(Application $app)
    {
        $userStorage              = $app['orm.em']->getRepository(Login::class);
        $clientStorage            = $app['orm.em']->getRepository(LoginAppClient::class);
        $accessTokenStorage       = $app['orm.em']->getRepository(LoginAccessToken::class);
        $refreshTokenStorage      = $app['orm.em']->getRepository(LoginRefreshToken::class);
        $scopeStorage             = $app['orm.em']->getRepository(LoginScope::class);

        $authorizationCodeStorage = new AuthorizationCodeStorage($app['orm.em']);


        $storages = [
            'client_credentials' => $clientStorage,
            'user_credentials'   => $userStorage,
            'access_token'       => $accessTokenStorage,
            'refresh_token'      => $refreshTokenStorage,
            'scope'              => $scopeStorage,
            'authorization_code' => $authorizationCodeStorage
        ];


        $grantTypes = [
            'client_credentials' => new ClientCredentials($clientStorage, ['allow_public_clients' => false]) ,
            'user_credentials'   => new UserCredentials($userStorage),
            'refresh_token'      => new RefreshToken($refreshTokenStorage, ['always_issue_new_refresh_token' => true, 'unset_refresh_token_after_use' => false]),
            'authorization_code' => new AuthorizationCode($authorizationCodeStorage),
            'user_platform_credentials' => new UserApiCredentials($userStorage)
        ];

        $config = [];

        $server = new Server($storages, $config , $grantTypes);

        $app['oauth_server'] = $server;
        $app['oauth_response'] = new BridgeResponse();
    }
}
