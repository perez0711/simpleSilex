<?php

namespace SimpleApi\Helper;

use Symfony\Component\Translation\TranslatorInterface;

class NotificationError
{
    protected $codigoErro = 0;
    protected $errors = [];
    protected $hasErrors =  false;
    
    public function reset()
    {
        $this->codigoErro =  0;
        $this->errors     = [];
        $this->hasErrors = false;
    }
    
    public function setCodigoErro($codigo)
    {
        $this->codigoErro = $codigo;
    }
    
    public function addErro($indice, $valor, $params = array())
    {
        $this->errors[$indice]['valor']  = $valor;
        $this->errors[$indice]['params'] = $params;
        $this->hasErrors = true;
    }
    
    public function getCodigoErro()
    {
        return $this->codigoErro;
    }
    
    public function hasErrors()
    {
        return $this->hasErrors;
    }
    
    public function getErrors(TranslatorInterface $translator)
    {
        $errors = [];
        
        foreach($this->errors as $indice => $erro){
            $errors[$indice] = $translator->trans($erro['valor'],$erro['params']);
        }
        
        return $errors;
    }
}
