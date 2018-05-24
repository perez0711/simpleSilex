<?php

namespace SimpleApi\Services\Ifttt\Status\User;


use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Types\Type;
use SimpleApi\Entity\Login;

class UserStorage
{
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getLoginInfo($id)
    {
        if (is_null($id)) {
           return null;
        }
        
        $qb = $this->em->createQueryBuilder();

        $q  = $qb->select(array('l'))
                 ->from(Login::class, 'l')
                 ->where(
                   $qb->expr()->eq('l.id', ':id')
                 )
                ->setParameter('id', $id, Type::INTEGER)
                ->getQuery();
        
        try {
            $loginArr = $q->getArrayResult();
            $loginInfo = !empty($loginArr) ? $loginArr[0] : null;
        } catch (\Exception $e) {
            $loginInfo = null;
        }
        
        return $loginInfo;
    }
}
