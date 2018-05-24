<?php

namespace SimpleApi\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use SimpleApi\Helper\PasswordHash;

/**
 * LoginAppClient
 * @ORM\Entity(repositoryClass="SimpleApi\Repository\LoginAppClientRepository")
 * @ORM\Table(name="login_app_cliente",
 * indexes={
 *      @ORM\Index(name="idx_client_identifier", columns={"client_identifier"})
 *  },
 * uniqueConstraints={
 *      @ORM\UniqueConstraint(name="unique_client_identifier", columns={"client_identifier"})
 * })
 * 
 */
class LoginAppClient
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
     * @ORM\Column(type="string")
     */
    private $client_identifier;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $client_secret;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $redirect_uri = '';
    
    
    /**
     * @var Collections LoginScope
     * @ORM\ManyToMany(targetEntity="LoginScope", mappedBy="appClients")
     */
    private $scopes;
    
    public function __construct()
    {
        $this->scopes = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setClientIdentifier($clientIdentifier)
    {
        $this->client_identifier = $clientIdentifier;
        return $this;
    }

    public function getClientIdentifier()
    {
        return $this->client_identifier;
    }

    public function setClientSecret($clientSecret)
    {
        $this->client_secret = $clientSecret;
        return $this;
    }

    public function getClientSecret()
    {
        return $this->client_secret;
    }

    public function verifyClientSecret($clientSecret)
    {
        return ($clientSecret == $this->getClientSecret());
    }

    public function setRedirectUri($redirectUri)
    {
        $this->redirect_uri = $redirectUri;
        return $this;
    }

    public function getRedirectUri()
    {
        return $this->redirect_uri;
    }
    
    public function getScopes()
    {
        return $this->scopes;
    }

    public function toArray()
    {
        $scope = array_map(function ($scope) {
            return $scope->getScope();
        }, $this->scopes->toArray());
        
        return [
            'client_id' => $this->client_identifier,
            'client_secret' => $this->client_secret,
            'redirect_uri' => $this->redirect_uri,
            'scope' => empty($scope) ? null : implode(" ", $scope)
        ];
    }
}
