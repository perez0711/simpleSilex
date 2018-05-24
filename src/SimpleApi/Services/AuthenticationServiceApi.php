<?php

namespace SimpleApi\Services;

use SimpleApi\Services\AuthenticationInterface;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Parser;

class AuthenticationServiceApi implements AuthenticationInterface
{
    
    private $em;
    private $pathPublicKey;
    private $token;
    
    const ISSUE = "api.fullarm.com";
    
    public function __construct($pathPublicKey)
    {
        $this->pathPublicKey  = $pathPublicKey;
    }
    
    public function authenticate($tokenStr)
    {
        $authenticated = false;
        
        try {
            $publicKey  = new Key("file://" . $this->pathPublicKey);

            $signer = new Sha256();
            $this->token = (new Parser())->parse((string) $tokenStr);

            if ($this->token->verify($signer, $publicKey)) {
                $cdate = new \DateTime();
                $edate = new \DateTime();
                
                $edate->setTimestamp($this->token->getClaim('exp'));
                
                $notExpired = ($edate > $cdate);
                
                $authenticated = $notExpired ;
            }
        } catch (\Exception $e) {
        }
      
        return $authenticated;
    }
    
    public function setEntityManager($em)
    {
        $this->em = $em;
    }
    
    public function getEntityManager()
    {
        return $this->em;
    }
    
    public function getToken()
    {
        return $this->token;
    }
}
