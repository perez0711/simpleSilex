<?php

namespace SimpleApi\Entity;

use Doctrine\ORM\Mapping as ORM;
use SimpleApi\Entity\Login;
use SimpleApi\Entity\LoginAppClient;

/**
 * LoginLogs
 * @ORM\Entity
 * @ORM\Table(name="login_logs")
 */
class LoginLogs
{
    
    
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer") 
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
    * @var Login
    * @ORM\ManyToOne(targetEntity="Login")
    * @ORM\JoinColumn(name="id_login",referencedColumnName="id", onDelete="CASCADE")
    */
    private $login;
    
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $data;
    
    /**
    * @var string
    * @ORM\Column(type="text", nullable=true)
    */
    private $mensagem;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $complemento;

    public function __construct()
    {
        $this->data = new \DateTime('UTC');
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getComplemento()
    {
        return $this->complemento;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getMensagem()
    {
        return $this->mensagem;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function setMensagem($mensagem)
    {
        $this->mensagem = $mensagem;
    }

    public function setComplemento($complemento)
    {
        $this->complemento = $complemento;
    }

    public function toArray()
    {
        $idLogin = !is_null($this->login)? $this->login->getId(): null;

        return [
            "id" => $this->id,
            'data' => $this->data,
            'login' => $idLogin,
            'mensagem' => $this->mensagem,
            'compĺemento' => $this->complemento
        ];
    }
}
