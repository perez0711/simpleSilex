<?php

namespace SimpleApi\OAuth\Storage;

use OAuth2\Storage\AuthorizationCodeInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Types\Type;
use SimpleApi\Entity\LoginAuthorizationCode;
use SimpleApi\Entity\LoginAppClient;
use SimpleApi\Entity\Login;

class AuthorizationCodeStorage implements AuthorizationCodeInterface
{
    
    private $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function expireAuthorizationCode($codeStr)
    {
        $code = $this->em->getRepository(LoginAuthorizationCode::class)->findOneBy(['code' => $codeStr]);
        
        if ($code) {
            $this->em->remove($code);
            $this->em->flush();
        }
    }

    public function getAuthorizationCode($codeStr)
    {
        $qb = $this->em->createQueryBuilder();

        $q  = $qb->select('t.code as code, lac.client_identifier as client_id, l.id as user_id, t.expira as expires, t.scope as scope')
                 ->from(LoginAuthorizationCode::class, 't')
                 ->innerJoin(\SimpleApi\Entity\LoginAppClient::class, 'lac','WITH','lac.id = t.app_client')
                 ->leftJoin(\SimpleApi\Entity\Login::class, 'l', 'WITH','l.id = t.login')
                 ->where(
                   $qb->expr()->eq('t.code', ':code')
                 )
                 ->setParameter('code', $codeStr, Type::STRING)
                 ->getQuery();
        
        
        try {
            $codeArr = $q->getArrayResult();
            $code = (!empty($codeArr)) ? $codeArr[0] : null;
        } catch (\Exception $e) {
            $code = null;
        }
        
        if ($code) {
            $code['expires'] = $code['expires']->getTimestamp();
        }
        return $code;
    }

    public function setAuthorizationCode($code, $client_id, $user_id, $redirect_uri, $expires, $scope = null)
    {
        $loginAuthorizationCode = new LoginAuthorizationCode();
        
        $appClient = $this->em->getRepository(LoginAppClient::class)->findOneBy(['client_identifier' => $client_id]);
        $login     = $this->em->getRepository(Login::class)->findOneBy(['id' => $user_id]);
        
        $loginAuthorizationCode->setCode($code);
        $loginAuthorizationCode->setExpires((new \DateTime())->setTimestamp($expires));
        $loginAuthorizationCode->setAppClient($appClient);
        $loginAuthorizationCode->setLogin($login);
        $loginAuthorizationCode->setScope($scope);
        
        $this->em->persist($loginAuthorizationCode);
        $this->em->flush();
    
    }

}
