<?php

namespace SimpleApi\Services\Login\Form\Storage;

use SimpleApi\Entity\Login;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;

class LoginStorage
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getLoginPorId($id)
    {
        $loginFormInfo = null;
        $loginFormRepository = $this->em->getRepository(Login::class);
        $loginForm          = $loginFormRepository->find($id);
        if(!is_null($loginForm)){
            $loginFormInfo = $loginForm->toArray();
        }
        return $loginFormInfo;
    }

    public function save(Login $loginForm = null)
    {
        if($loginForm){
            $this->em->persist($loginForm);
            $this->em->flush();
        }
    }

    public function delete(Login $loginForm = null)
    {
        if($loginForm){
            $this->em->remove($loginForm);
            $this->em->flush();
        }
    }
}
