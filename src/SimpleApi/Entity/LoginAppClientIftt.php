<?php

namespace SimpleApi\Entity;

use Doctrine\ORM\Mapping as ORM;
use SimpleApi\Entity\LoginAppClient;

/**
 * LoginAppClientIftt
 * @ORM\Entity
 * @ORM\Table(name="login_app_cliente_ifttt")
 */
class LoginAppClientIftt
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer") 
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
    * @var LoginAppClient
    * @ORM\OneToOne(targetEntity="LoginAppClient",inversedBy="config")
    * @ORM\JoinColumn(name="id_client",referencedColumnName="id", onDelete="CASCADE")
    */
    private $client;
    
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $channel_key;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $service_key;
    
    public function getId()
    {
        return $this->id;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getChannelKey()
    {
        return $this->channel_key;
    }

    public function getServiceKey()
    {
        return $this->service_key;
    }

    public function setClient(LoginAppClient $client)
    {
        $this->client = $client;
        return $this;
    }

    public function setChannelKey($channel_key)
    {
        $this->channel_key = $channel_key;
        return $this;
    }

    public function setServiceKey($service_key)
    {
        $this->service_key = $service_key;
        return $this;
    }


}