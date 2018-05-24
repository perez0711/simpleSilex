<?php

namespace SimpleApi\Services\Ifttt\Helper;

use Doctrine\ORM\EntityManager;
use SimpleApi\Entity\LoginAccessToken;

class AccessToken
{
    private $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getAccessToken($tokenStr)
    {
        $loginAccessTokenRepository = $this->em->getRepository(LoginAccessToken::class);
        $accessToken = $loginAccessTokenRepository->getAccessToken($tokenStr);
        
        return $accessToken;
    }
    
    public function getIdUser($tokenStr)
    {
        $accessToken = $this->getAccessToken($tokenStr);
        
        $id = ($accessToken) ? $accessToken['user_id'] : null;
        
        return $id;    
    }
}
