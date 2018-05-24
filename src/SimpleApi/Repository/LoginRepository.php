<?php

namespace SimpleApi\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Types\Type;
use OAuth2\Storage\UserCredentialsInterface;
use SimpleApi\Entity\Login;

class LoginRepository extends EntityRepository implements UserCredentialsInterface
{
    
    public function findByLoginAndDiffId($login, $id)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $q  = $qb->select(array('c'))
                 ->from(Login::class, 'c')
                 ->where(
                   $qb->expr()->like('c.login', ':login'),
                   $qb->expr()->neq('c.id', ':id')
                 )
                 ->setParameter('login', $login, Type::STRING)
                 ->setParameter('id', (int) $id, Type::INTEGER)
                 ->getQuery();
        
        
        return $q->getResult();
    }


    public function checkUserCredentials($login, $password)
    {
        $user = $this->findOneBy(['login' => trim($login)]);
        if ($user) {
            return $user->verifyPassword(trim($password));
        }
        return false;
    }

    public function getUserDetails($login)
    {
        $user = $this->findOneBy(['login' => trim($login)]);
        if ($user) {
            $user = $user->toOAuthArray();
        }
        return $user;
    }

}
