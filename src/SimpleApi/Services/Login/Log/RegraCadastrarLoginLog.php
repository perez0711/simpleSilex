<?php

namespace SimpleApi\Services\Login\Log;

use SimpleApi\Entity\LoginLogs;
use SimpleApi\Services\Login\Log\Storage\LoginLogsStorage;
use SimpleApi\Services\Login\Log\Validation\LoginLogsValidation;

class RegraCadastrarLoginLog
{
    protected $validation;
    protected $storage;
    protected $parser;

    public function setValidation(LoginLogsValidation $validation)
    {
        $this->validation = $validation;
    }

    public function setParser(ParseLoginLog $parse)
    {
        $this->parser = $parse;
    }

    public function setStorage(LoginLogsStorage $storage)
    {
        $this->storage = $storage;
    }
    
    public function cadastrar( $data , LoginLogs $loginLogs)
    {
        $dataIsValid = $this->validation->validate($data);

        if($dataIsValid){
            $this->parser->setLogFromData($data, $loginLogs);
            $this->storage->save($loginLogs);
        }
        
        return $loginLogs;
    }
}
