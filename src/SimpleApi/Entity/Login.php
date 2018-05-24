<?php

namespace SimpleApi\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use SimpleApi\Entity\LoginConfig;
use SimpleApi\Helper\PasswordHash;

/**
 * Login
 * @ORM\Entity(repositoryClass="SimpleApi\Repository\LoginRepository")
 * @ORM\Table(name="login",
 *  indexes={
 *      @ORM\Index(name="idx_email", columns={"email"}),
 *      @ORM\Index(name="idx_login", columns={"login"})
 *  },
 * uniqueConstraints={
 *      @ORM\UniqueConstraint(name="unique_login", columns={"login"})
 * })
 */
class Login
{
    
    const PERMISSAO_LOGIN_MASTER = 1;
    const PERMISSAO_LOGIN_NORMAL = 2;

    const TIPO_MONITORAMENTO = 1;
    const TIPO_CLIENTE = 2;
    
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer") 
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
    * @var string
    * @ORM\Column(type="string")
    */
    protected $login;
    
    /**
    * @var string
    * @ORM\Column(type="string", nullable=true)
    */
    protected $descricao;
    
    /**
    * @var string
    * @ORM\Column(type="string")
    */
    protected $senha;
    
    /**
    * @var string
    * @ORM\Column(type="string")
    */
    protected $email;
    
    /**
    * @var integer
    * @ORM\Column(type="integer")
    */
    protected $permissao;
    
    /**
    * @var LoginConfig
    * @ORM\OneToOne(targetEntity="LoginConfig", mappedBy="login",cascade={"persist","remove"}))
    */
    protected $config;
    
    public function __construct()
    {
        $this->config = new LoginConfig();
        $this->config->setLogin($this);
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getLogin()
    {
        return $this->login;
    }

    public function getSenha()
    {
        return $this->senha;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }
    
    public function getPermissao()
    {
        return $this->permissao;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    public function setSenha($senha)
    {
        $this->senha = $senha;
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
        return $this;
    }
    
    public function setPermissao($permissao)
    {
        $this->permissao = $permissao;
        return $this;
    }
    
    public function setConfig(LoginConfig $config)
    {
        $this->config = $config;
        return $this;
    }
    
    public function setIdioma ($idioma)
    {
        $this->config->setIdioma($idioma);
        return $this;
    }
    
    public function verifyPassword($password)
    {
        return PasswordHash::verificarSenha($password, $this->getSenha());
    }
    
    public function toOAuthArray()
    {
        return [
            'user_id' => $this->id,
            'user_lang' => $this->config->getIdioma(),
            'scope' => null,
        ];
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'tipo' => $this->getTipo(),
            'login' => $this->login,
            'email' => $this->email,
            'descricao' => $this->descricao,
            'config' => [
                'idioma' => $this->config->getIdioma(),
                'timezone' => $this->config->getTimeZone()
            ]
        ];
    }
}
