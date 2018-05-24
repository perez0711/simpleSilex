<?php

namespace SimpleApi\Services\Login\Form\Validation;

use SimpleApi\Helper\NotificationError;

use Symfony\Component\HttpFoundation\Response;

class LoginValidation
{
    protected $formValidation;
    protected $notificationError;
    protected $loginIdAlreadyRegistred;

    public function setFormNotificationPanel(NotificationError $fv)
    {
        $this->notificationError = $fv;
    }

    public function setFormValidation(FormLoginValidation $formValidation)
    {
        $this->formValidation = $formValidation;
    }

    public function setLoginIdAlreadyRegistred(LoginAlreadyRegistred $loginIdAlreadyRegistred){
        $this->loginIdAlreadyRegistred = $loginIdAlreadyRegistred;
    }

    public function validate($data)
    {
        $loginIdOuEmailJaRegistrado = false;

        $formIsValid = $this->formValidation->validate($data);

        if($formIsValid){
            $id = isset($data['id']) ? $data['id'] : null;
            $loginIdOuEmailJaRegistrado = $this->loginIdAlreadyRegistred->check($data['login'], $data['email'],$id);
        } else {
            $this->notificationError->setCodigoErro(Response::HTTP_BAD_REQUEST);
        }

        if($loginIdOuEmailJaRegistrado){
            $this->notificationError->setCodigoErro(Response::HTTP_CONFLICT);
        }

        return ($formIsValid  && !$loginIdOuEmailJaRegistrado);

    }

}