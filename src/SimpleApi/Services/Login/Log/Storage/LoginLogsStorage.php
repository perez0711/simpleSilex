<?php

namespace SimpleApi\Services\Login\Log\Storage;

use Doctrine\ORM\EntityManager;
use SimpleApi\Entity\LoginLogs;

class LoginLogsStorage
{
    private $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function save(LoginLogs $loginLogs)
    {
        if($loginLogs){
            $this->em->persist($loginLogs);
            $this->em->flush();
        }
    }
}
