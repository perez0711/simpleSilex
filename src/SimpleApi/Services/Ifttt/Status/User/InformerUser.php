<?php

namespace SimpleApi\Services\Ifttt\Status\User;

use SimpleApi\Services\Ifttt\Helper\AccessToken;
use SimpleApi\Services\Ifttt\Status\User\UserStorage;

class InformerUser
{
    
    private $accessTokenHelper;
    private $userStorage;
    
    public function __construct(AccessToken $accessTokenHelper, UserStorage $userStorage )
    {
        $this->accessTokenHelper = $accessTokenHelper;
        $this->userStorage       = $userStorage;
    }
    
    public function getUserInfoFromToken($tokenStr)
    {
        
        $id   = $this->accessTokenHelper->getIdUser($tokenStr);
        $user = $this->userStorage->getLoginInfo($id);
        
        return $user;
    }
}
