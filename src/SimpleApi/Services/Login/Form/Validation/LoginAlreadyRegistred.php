<?php

namespace SimpleApi\Services\Login\Form\Validation;

use SimpleApi\Helper\NotificationError;
use SimpleApi\Services\Login\Form\Storage\LoginAlreadyRegistredStorage;

class LoginAlreadyRegistred
{

    private $storage;
    protected $notificationErrors;

    public function __construct(LoginAlreadyRegistredStorage $storage, NotificationError $fne)
    {
        $this->storage = $storage;
        $this->notificationErrors = $fne;
    }

    public function check($login , $email, $id = null)
    {

        $emailJaExiste = false;
        $loginForm = $this->storage->getLoginPorLogin($login, $id);

        $loginJaExiste = !empty($loginForm);

        if($loginJaExiste){
            $this->notificationErrors->addErro('login','api_config_login_empresa_msg_erro_id_ja_existe',[]);
        }

        if(!$loginJaExiste){
            $emailForm = $this->storage->getLoginPorEmail($email, $id);

            $emailJaExiste = !empty($emailForm);

            if($emailJaExiste){
                $this->notificationErrors->addErro('email','api_config_email_empresa_msg_erro_id_ja_existe',[]);
            }
        }

        return ($loginJaExiste || $emailJaExiste);
    }

}
