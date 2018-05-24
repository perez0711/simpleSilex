<?php

namespace SimpleApi\Services\Login\Log;

use SimpleApi\Entity\LoginLogs;

class ParseLoginLog
{

    public function setLogFromData($data, LoginLogs $loginLogs)
    {
        $mensagem = $data['mensagem'];
        $complemento = $data['complemento'];

        $loginLogs->setMensagem($mensagem);
        $loginLogs->setComplemento($complemento);
        
        return $loginLogs;
    }

    
}
