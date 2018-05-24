<?php

namespace SimpleApi\Helper;

use SimpleApi\Helper\NotificationError;
use Respect\Validation\Exceptions\NestedValidationException;

abstract class NotificationErrorRespectValidationAdpter
{
    protected $notificationErrors;

    public function setNotificationErrors(NotificationError $ne)
    {
        $this->notificationErrors = $ne;
    }
    
    public function validate($data)
    {
        try {
            $this->getValidation($data)->assert($data);
            $isValid = true;
        } catch (NestedValidationException $ex) {
            $isValid = false;
            $this->setErrors($ex, $data);
        }
        
        return $isValid;
    }
    
    protected function setErrors(NestedValidationException $ex, $data)
    {
        $mensagensDeErro = $this->getErrorsMessages($data);
        
        $todosOsErrors = $ex->findMessages(array_keys($mensagensDeErro));
        
        foreach ($todosOsErrors as $idx => $msg) {
            
            if(strlen($msg) > 0){
                $valor  = $mensagensDeErro[$idx][0];
                $params = $mensagensDeErro[$idx][1];
            
                $this->notificationErrors->addErro($idx, $valor, $params);
            }
        }
        
        
    }

    abstract protected function getValidation($data);
    abstract protected function getErrorsMessages($data);
}
