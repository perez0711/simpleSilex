<?php

namespace SimpleApi\Services\Login\Form\Validation;

use Respect\Validation\Validator as v;
use SimpleApi\Helper\NotificationErrorRespectValidationAdpter;

class FormLoginValidation extends NotificationErrorRespectValidationAdpter
{
    protected function getErrorsMessages($data)
    {
        return [
            'descricao'    => ['api_config_login_empresa_msg_erro_descricao',[]],
            'email'       => ['api_config_login_empresa_msg_erro_email',[]],
            'login'        => ['api_config_login_empresa_msg_erro_login',[]],
            'senha'        => ['api_config_login_empresa_msg_erro_senha',[]],
            'senha_confirmar' => ['confirmação de senha invalida',[]]
        ];
    }

    public function validate($data)
    {
        return parent::validate($data);
    }

    protected function getValidation($data)
    {

        $senha_confirmar  = v::arrayType()->keyValue('senha', 'equals', 'confirma_senha')->setName('senha_confirmar');

        $basic =  v::arrayType()
                ->key('descricao' , v::stringType()->notEmpty()->setName("descricao"))
                ->key('email' , v::email()->notEmpty()->setName("email"))
                ->Key('login', v::stringType()->notEmpty()->setName("login"))
                ->Key('senha', v::optional(v::stringType()->notEmpty()->setName("senha")));

        return v::allOf(
            $basic,
            $senha_confirmar
        );


    }
}