<?php

namespace SimpleApi\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LoginScope
 * @ORM\Entity(repositoryClass="SimpleApi\Repository\LoginScopeRepository")
 * @ORM\Table(name="login_scope",
 *  indexes={
 *      @ORM\Index(name="idx_scope", columns={"scope"})
 *  }
 * )
 */
class LoginScope
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
     * @ORM\Column(type="string", unique=true)
     */
    private $scope;
    
    /**
     * @var string
     * @ORM\Column(type="string", nullable = true)
     */
    private $descricao;

     /**
     * @ORM\ManyToMany(targetEntity="LoginAppClient", inversedBy="scopes")
     * @ORM\JoinTable(name="login_scope_appclient",
     *      joinColumns={@ORM\JoinColumn(name="id_scope", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_appclient", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private $appClients;
    
    public function setScope($scope)
    {
        $this->scope = $scope;
        return $this;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }
}
