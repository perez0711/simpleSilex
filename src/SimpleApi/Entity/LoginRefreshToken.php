<?php

namespace SimpleApi\Entity;

use Doctrine\ORM\Mapping as ORM;
use SimpleApi\Entity\Login;
use SimpleApi\Entity\LoginAppClient;

/**
 * LoginRefreshToken
 * @ORM\Entity(repositoryClass="SimpleApi\Repository\LoginRefreshTokenRepository")
 * @ORM\Table(name="login_refresh_token",
 *  indexes={
 *      @ORM\Index(name="idx_refreshtoken", columns={"refresh_token"})
 *  }
 * )
 */
class LoginRefreshToken
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
    * @ORM\JoinColumn(name="id_login",referencedColumnName="id",onDelete="CASCADE")
    */
    private $login;
    
    /**
    * @var string
    * @ORM\Column(type="string")
    */
    private $refresh_token;
    
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

    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function getRefreshToken()
    {
        return $this->refresh_token;
    }
    
    public function getLogin()
    {
        return $this->login;
    }
    
    public function getAppClient()
    {
        return $this->app_client;
    }

    public function getExpira()
    {
        return $this->expira;
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function setRefreshToken($refresh_token)
    {
        $this->refresh_token = $refresh_token;

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

    public function setExpira(\DateTime $expira)
    {
        $this->expira = $expira;
        return $this;
    }

    public function setScope($scope)
    {
        $this->scope = $scope;
        return $this;
    }

    
    public static function fromArray($params)
    {
        $token = new self();
        foreach ($params as $property => $value) {
            $token->$property = $value;
        }
        return $token;
    }
    
    public function toAuthArray()
    {
        return [
           'refresh_token'  => $this->refresh_token,
           'client_id'      => $this->app_client->getClientIdentifier(),
           'user_id'        => is_null($this->login) ? null : $this->login->getId(),
           'expires'        => $this->expira,
           'scope'          => $this->scope,
        ];
    }
}
