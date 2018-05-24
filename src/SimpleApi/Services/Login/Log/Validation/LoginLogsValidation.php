<?php

namespace SimpleApi\Services\Login\Log\Validation;

use SimpleApi\Helper\NotificationError;
use Symfony\Component\HttpFoundation\Response;

class LoginLogsValidation
{
    protected $notificationError;
    protected $formValidation;
    
    public function __construct(NotificationError $fv, LoginLogFormValidation $formValidation)
    {
       $this->notificationError = $fv;
       $this->formValidation = $formValidation;
    }
    
    public function validate($data)
    {

        $formIsValid = $this->formValidation->validate($data);

        if (!$formIsValid) {
            $this->notificationError->setCodigoErro(Response::HTTP_BAD_REQUEST);
        }

        return ($formIsValid);

    }
}
