<?php

namespace SimpleApi\Entity;

use Doctrine\ORM\Mapping as ORM;
use SimpleApi\Entity\Login;
use SimpleApi\Entity\LoginAppClient;

/**
 * LoginAuthorizationCode
 * @ORM\Entity
 * @ORM\Table(name="login_authorizationcode",
 *  indexes={
 *      @ORM\Index(name="idx_code", columns={"code"})
 *  }
 * )
 */
class LoginAuthorizationCode
{
    
    
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer") 
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    
    /**
     * @var string
     * @ORM\Column(type="string", length=70, unique=true)
     */
    private $code;
    
    /**
    * @var Login
    * @ORM\ManyToOne(targetEntity="Login")
    * @ORM\JoinColumn(name="id_login",referencedColumnName="id", onDelete="CASCADE")
    */
    private $login;
    
    /**
    * @var AppClient
    * @ORM\ManyToOne(targetEntity="LoginAppClient")
    * @ORM\JoinColumn(name="id_app_client",referencedColumnName="id", onDelete="CASCADE")
    */
    private $app_client;
    
    
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $expira;
    
    /**
    * @var string
    * @ORM\Column(type="text", nullable=true)
    */
    private $scope;
    
    public function getId()
    {
        return $this->id;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getLogin()
    {
        return $this->login;
    }
    
    public function getAppClient()
    {
        return $this->app_client;
    }

    public function getExpires()
    {
        return $this->expira;
    }
    
    public function getScope()
    {
        return $this->scope;
    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function setLogin(Login $login = null)
    {
        $this->login = $login;
        return $this;
    }
    
    public function setAppClient(LoginAppClient $app_client = null)
    {
        $this->app_client = $app_client;
        return $this;
    }

    public function setExpires(\DateTime $expira)
    {
        $this->expira = $expira;
        return $this;
    }
    
    public function setScope($scope)
    {
        $this->scope = $scope;
        return $this;
    }
    
    public function toArray()
    {
        $cliente_id = ($this->app_client && $this->app_client instanceof LoginAppClient) ? $this->app_client->getClientIdentifier() : null;
        $user_id    = ($this->login && $this->login instanceof Login) ? $this->login->getId() : null;
        return [
            'code' => $this->code,
            'client_id' => $cliente_id,
            'user_id' => $user_id,
            'expires' => $this->expira,
            'scope' => $this->scope,
        ];
    }
}
