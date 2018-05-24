<?php

namespace SimpleApi\Entity;

use Doctrine\ORM\Mapping as ORM;
use SimpleApi\Entity\Login;

/**
 * LoginConfig
 * @ORM\Entity
 * @ORM\Table(name="login_configuracao")
 */
class LoginConfig
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
    * @ORM\OneToOne(targetEntity="Login",inversedBy="config")
    * @ORM\JoinColumn(name="id_login",referencedColumnName="id", onDelete="CASCADE")
    */
    private $login;
    
    /**
     * @var string
     * @ORM\Column(type="string", options={"default":"pt-BR"})
     */
    private $idioma;

    /**
     * @var string
     * @ORM\Column(type="string", options={"default":"America/Sao_Paulo"})
     */
    private $time_zone;
    
    public function __construct()
    {
        $this->idioma    = "pt-BR";
        $this->time_zone = "America/Sao_Paulo";
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getIdioma()
    {
        return $this->idioma;
    }

    public function getTimeZone()
    {
        return $this->time_zone;
    }

    public function setLogin(Login $login)
    {
        $this->login = $login;
        return $this;
    }

    public function setIdioma($idioma)
    {
        $this->idioma = $idioma;
        return $this;
    }

    public function setTimeZone($time_zone)
    {
        $this->time_zone = $time_zone;
        return $this;
    }
}
