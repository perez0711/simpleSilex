<?php

namespace SimpleApi\Services;

use SimpleApi\Services\AuthenticationInterface;
use SimpleApi\Entity\LoginAccessToken;

class AuthenticationServiceApp implements AuthenticationInterface
{
    
    private $em;
    
    public function authenticate($token)
    {
        if (is_null($this->getEntityManager())) {
            throw new \RuntimeException('Entity Manager is null, please set it');
        }
        
        if (is_null($token) || (strlen($token) < 64)) {
            return false;
        }
        
        $loginAccessTokenRepository = $this->getEntityManager()->getRepository('SimpleApi\Entity\LoginAccessToken');
        $loginAccessToken = $loginAccessTokenRepository->findTokenByDateExpira($token, new \DateTime());
        
        if (is_null($loginAccessToken)) {
            return false;
        }
        
        return true;
    }
    
    public function setEntityManager($em)
    {
        $this->em = $em;
    }
    
    public function getEntityManager()
    {
        return $this->em;
    }
}
