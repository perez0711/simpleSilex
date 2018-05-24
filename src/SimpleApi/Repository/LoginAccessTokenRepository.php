<?php

namespace SimpleApi\Repository;

use SimpleApi\Entity\Login;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use SimpleApi\Entity\LoginAppClient;
use SimpleApi\Entity\LoginAccessToken;
use OAuth2\Storage\AccessTokenInterface;

class LoginAccessTokenRepository extends EntityRepository implements AccessTokenInterface
{
    
    public function findTokenByDateExpira($token,  \DateTime $date, $totalResults = 1)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $result = null;
        
        $q  = $qb->select(array('c'))
                 ->from(LoginAccessToken::class, 'c')
                 ->where(
                   $qb->expr()->eq('c.token', ':token'),
                   $qb->expr()->gte('c.expira', ':date')
                 )
                 ->setParameter('token', $token, Type::STRING)
                 ->setParameter('date', $date, Type::DATETIME)
                 ->setMaxResults($totalResults)
                 ->getQuery();
        
        if ($totalResults > 1) {
            $result = $q->getResult();
        } else {
            try {
                $result = $q->getSingleResult();
            } catch (\Exception $e) {
            }
        }
        
        return $result;
    }

    public function getAccessToken($tokenStr)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $q  = $qb->select('t.token as token, lac.client_identifier as client_id, l.id as user_id, t.expira as expires, t.scope as scope')
                 ->from(LoginAccessToken::class, 't')
                 ->innerJoin(LoginAppClient::class, 'lac','WITH','lac.id = t.app_client')
                 ->leftJoin(Login::class, 'l', 'WITH','l.id = t.login')
                 ->where(
                   $qb->expr()->eq('t.token', ':token')
                 )
                 ->setParameter('token', $tokenStr, Type::STRING)
                 ->getQuery();
        
        
        try {
            $tokenArr = $q->getArrayResult();
            $token = (!empty($tokenArr)) ? $tokenArr[0] : null;
        } catch (\Exception $e) {
            $token = null;
        }
        
        if ($token) {
            $token['expires'] = $token['expires']->getTimestamp();
        }
        return $token;
    }

    public function setAccessToken($oauth_token, $client_id, $user_id, $expires, $scope = null)
    {
        $loginAccessToken = new LoginAccessToken();
        
        $appClient = $this->_em->getRepository(LoginAppClient::class)->findOneBy(['client_identifier' => $client_id]);
        $login     = $this->_em->getRepository(Login::class)->findOneBy(['id' => $user_id]);
        
        $loginAccessToken->setToken($oauth_token);
        $loginAccessToken->setExpires((new \DateTime())->setTimestamp($expires));
        $loginAccessToken->setAppClient($appClient);
        $loginAccessToken->setLogin($login);
        $loginAccessToken->setScope($scope);
        
        
        $this->_em->persist($loginAccessToken);
        $this->_em->flush();
    }
    
    public function unsetAccessToken($token)
    {
        $token = $this->findOneBy(['token' => $token]);
        
        if ($token) {
            $this->_em->remove($token);
            $this->_em->flush();
        }
    }
}
