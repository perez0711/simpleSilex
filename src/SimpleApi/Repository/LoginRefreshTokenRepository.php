<?php

namespace SimpleApi\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Types\Type;
use OAuth2\Storage\RefreshTokenInterface;
use SimpleApi\Entity\Login;
use SimpleApi\Entity\LoginAppClient;
use SimpleApi\Entity\LoginRefreshToken;

class LoginRefreshTokenRepository extends EntityRepository implements RefreshTokenInterface
{
    
    public function getRefreshToken($refreshToken)
    {
        $refreshToken = $this->findOneBy(['refresh_token' => $refreshToken]);
        
        if ($refreshToken) {
            $refreshToken = $refreshToken->toAuthArray();
            $refreshToken['expires'] = $refreshToken['expires']->getTimestamp();
        }
        return $refreshToken;
    }

    public function setRefreshToken($refreshToken, $clientIdentifier, $userId, $expires, $scope = null)
    {
        $client = $this->_em->getRepository(LoginAppClient::class)
                            ->findOneBy(['client_identifier' => $clientIdentifier]);
        $user = $this->_em->getRepository(Login::class)
                            ->findOneBy(['id' => $userId]);
        
        $refreshTokenNew= LoginRefreshToken::fromArray([
           'refresh_token'  => $refreshToken,
           'app_client'     => $client,
           'login'          => $user,
           'expira'         => (new \DateTime())->setTimestamp($expires),
           'scope'          => $scope,
        ]);
        
        $this->_em->persist($refreshTokenNew);
        $this->_em->flush();
    }

    public function unsetRefreshToken($refreshToken)
    {
        $refreshToken = $this->findOneBy(['refresh_token' => $refreshToken]);
        if ($refreshToken) {
            $this->_em->remove($refreshToken);
            $this->_em->flush();
        }
    }
}
