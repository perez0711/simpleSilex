<?php

namespace SimpleApi\Services\Login\Form;

use Doctrine\ORM\EntityManager;
use SimpleApi\Entity\Login;
use SimpleApi\Helper\NotificationError;
use SimpleApi\Services\Login\Form\Storage\LoginStorage;


class RegraBuscarLogin
{
    protected $em;
    protected $formNotificationError;
    protected $storage;

    public function __construct(EntityManager $em, NotificationError $fne, LoginStorage $ffs )
    {
        $this->em = $em;
        $this->storage = $ffs;
        $this->formNotificationError = $fne;
    }

    public function buscar(Login $loginForm)
    {

        $objLogin = new \stdClass();
        $objLogin->descricao = $loginForm->getDescricao();
        $objLogin->email = $loginForm->getEmail();
        $objLogin->login = $loginForm->getLogin();
        $objLogin->idioma = $loginForm->getConfig()->getIdioma();

        return $objLogin;
    }
}