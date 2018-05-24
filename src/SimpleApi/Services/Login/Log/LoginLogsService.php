<?php

namespace SimpleApi\Services\Login\Log;

use Doctrine\ORM\EntityManager;
use SimpleApi\Entity\Login;
use SimpleApi\Entity\LoginLogs;
use SimpleApi\Helper\NotificationError;
use SimpleApi\Services\Login\Log\Storage\LoginLogsStorage;
use SimpleApi\Services\Login\Log\Validation\LoginLogsValidation;
use SimpleApi\Services\Login\Log\Validation\LoginLogFormValidation;

class LoginLogsService
{
    protected $notificationError;
    protected $em;
    
    public function __construct(EntityManager $em, NotificationError $fne = null)
    {
        if(!$fne){
            $fne = new NotificationError();
        }
        $this->notificationError = $fne;
        $this->em = $em;
    }
    
    public function cadastrar( $data )
    {
        $parseLoginLog = new ParseLoginLog();

        $loginLogsStorage = new LoginLogsStorage($this->em);

        $loginLogFormValidation = new LoginLogFormValidation($this->notificationError);
        $loginLogsValidation = new LoginLogsValidation($this->notificationError, $loginLogFormValidation);

        $regraCadastrarLoginLog = new RegraCadastrarLoginLog();
        $regraCadastrarLoginLog->setParser($parseLoginLog);
        $regraCadastrarLoginLog->setStorage($loginLogsStorage);
        $regraCadastrarLoginLog->setValidation($loginLogsValidation);
        return $regraCadastrarLoginLog->cadastrar($data, $this->getLoginLog($data));
    }

    private function getLoginLog($data)
    {
        $idLogin  = $data['id_login'];

        $login   = $this->em->getReference(Login::class,$idLogin);
        $empresa = $login->getForm();

        $loginLog = new LoginLogs();
        $loginLog->setLogin($login);
        $loginLog->setForm($empresa);
        return $loginLog;
    }
}
