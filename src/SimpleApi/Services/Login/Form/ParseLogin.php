<?php

namespace SimpleApi\Services\Login\Form;

use SimpleApi\Entity\Login;
use SimpleApi\Helper\PasswordHash;
use Doctrine\ORM\EntityManager;

class ParseLogin
{

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function setLoginFromData($data, Login $loginForm)
    {
        $descricao   = $data['descricao'];
        $email       = $data['email'];
        $login       = $data['login'];
        $senha       = ($data['senha'] == "")? null: $data['senha'];
        $idioma      = isset($data['idioma']) ? $data['idioma'] : null;

        $loginForm->setDescricao($descricao);
        $loginForm->setEmail($email);
        $loginForm->setLogin($login);
        $loginForm->setPermissao(Login::PERMISSAO_LOGIN_NORMAL);

        if(!is_null($senha)){
            $loginForm->setSenha(PasswordHash::gerarHashSenha($senha));
        }

        $loginForm->getConfig()->setIdioma('pt-BR');

        return $loginForm;
    }

}
