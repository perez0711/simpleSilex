<?php

namespace SimpleApi\OAuth\GrantType;


use OAuth2\GrantType\GrantTypeInterface;
use OAuth2\ResponseType\AccessTokenInterface;
use OAuth2\RequestInterface;
use OAuth2\ResponseInterface;
use OAuth2\Storage\UserCredentialsInterface;


class UserApiCredentials implements GrantTypeInterface
{
    
    private $userInfo;

    protected $storage;

    public function __construct(UserCredentialsInterface $storage)
    {
        $this->storage = $storage;
    }
    
    public function getQuerystringIdentifier()
    {
        return 'user_platform_credentials';
    }

    public function validateRequest(RequestInterface $request, ResponseInterface $response)
    {
        if (!$request->request("password") || !$request->request("username")) {
            $response->setError(400, 'invalid_request', 'Missing parameters: "username" and "password" required');

            return null;
        }

        if (!$this->storage->checkUserCredentials($request->request("username"), $request->request("password"))) {
            $response->setError(401, 'invalid_grant', 'Invalid username and password combination');

            return null;
        }
        $userInfo = $this->storage->getUserDetails($request->request("username"));
        
        if (empty($userInfo)) {
            $response->setError(400, 'invalid_grant', 'Unable to retrieve user information');

            return null;
        }
        
        if (!isset($userInfo['user_id'])) {
            throw new \LogicException("you must set the user_id on the array returned by getUserDetails");
        }

        $this->userInfo = $userInfo;

        return true;
    }

    public function getClientId()
    {
        return null;
    }

    public function getUserId()
    {
        return $this->userInfo['user_id'];
    }

    public function getScope()
    {
        return null;
    }

    public function createAccessToken(AccessTokenInterface $accessToken, $client_id, $user_id, $scope)
    {
        $accessTokenData = $accessToken->createAccessToken($client_id, $user_id, $scope);
        
        return array_merge(
              $accessTokenData,
              [
                  'user_id' => $this->userInfo['user_id'],
                  'user_lang' => $this->userInfo['user_lang']
              ]
        );
    }

}
