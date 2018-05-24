<?php

namespace SimpleApi\Repository;

use OAuth2\Storage\ScopeInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Types\Type;
use SimpleApi\Entity\LoginScope;

class LoginScopeRepository extends EntityRepository implements ScopeInterface
{
    
    public function getDefaultScope($client_id = null)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $q  = $qb->select(array('l.scope'))
                 ->from(LoginScope::class, 'l')
                 ->innerJoin('l.appClients', 'lsa')
                 ->where(
                   $qb->expr()->eq('lsa.client_identifier', ':client_id')
                 )
                ->setParameter('client_id', $client_id, Type::STRING)
                ->getQuery();
        
        try {
            $scopes_a = $q->getResult();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        
        $scopes = array_map(function ($line) {
            return $line['scope'];
        }, $scopes_a);

        return implode(' ', $scopes);
    }

    public function scopeExists($scope)
    {
        return $scope;
    }
}
