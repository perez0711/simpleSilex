<?php

namespace SimpleApi\Services\Login\Form\Storage;

use Doctrine\ORM\EntityManager;
use SimpleApi\Entity\Login;
use Doctrine\DBAL\Types\Type;


class LoginAlreadyRegistredStorage
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getLoginPorLogin($login, $diffId = null)
    {

        $qb = $this->em->createQueryBuilder();

        $qb->select(array('cr'))
            ->from(Login::class, 'cr')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->eq("cr.login", ":login")
                )
            )
            ->setParameter("login", $login, Type::STRING);

        if (is_numeric($diffId) && $diffId > 0) {
            $qb->andWhere($qb->expr()->neq("cr.id", ":id"))
                ->setParameter("id", (int) $diffId, Type::INTEGER);
        }

        $q = $qb->getQuery();


        try {
            $configLogin = $q->getSingleResult();
        } catch (\Exception $e) {
            $configLogin = null;
        }

        return $configLogin;
    }

    public function getLoginPorEmail($email, $diffId = null)
    {

        $qb = $this->em->createQueryBuilder();

        $qb->select(array('cr'))
            ->from(Login::class, 'cr')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->eq("cr.email", ":email")
                )
            )
            ->setParameter("email", $email, Type::STRING);

        if (is_numeric($diffId) && $diffId > 0) {
            $qb->andWhere($qb->expr()->neq("cr.id", ":id"))
                ->setParameter("id", (int) $diffId, Type::INTEGER);
        }

        $q = $qb->getQuery();


        try {
            $configLogin = $q->getSingleResult();
        } catch (\Exception $e) {
            $configLogin = null;
        }

        return $configLogin;
    }
}
