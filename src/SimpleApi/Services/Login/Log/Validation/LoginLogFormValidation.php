<?php

namespace SimpleApi\Services\Login\Log\Validation;

use Respect\Validation\Validator as v;
use SimpleApi\Helper\NotificationError;
use SimpleApi\Helper\NotificationErrorRespectValidationAdpter;

class LoginLogFormValidation extends NotificationErrorRespectValidationAdpter
{
    public function __construct(NotificationError $notificationErrors)
    {
        $this->setNotificationErrors($notificationErrors);
    }

    protected function getErrorsMessages($data)
    {
        return [
            'id_login' => ['api_cameras_sistema_msg_erro_sistema', []],
            'mensagem' => ['api_cameras_sistema_msg_erro_conexao', []],
            'complemento' => ['api_cameras_sistema_msg_erro_conexao', []]
        ];
    }

    protected function getValidation($data)
    {
        return v::arrayType()
                ->key('id_login',v::numeric()->positive())
                ->key('mensagem', v::stringType()->notEmpty())
                ->key('complemento', v::stringType()->notEmpty());
    }

}
