<?php

namespace SimpleApi\Repository;

use OAuth2\Storage\ClientCredentialsInterface;
use Doctrine\ORM\EntityRepository;

class LoginAppClientRepository extends EntityRepository implements ClientCredentialsInterface
{
    
    public function checkClientCredentials($clientIdentifier, $clientSecret = null)
    {
        $client = $client = $this->findOneBy(['client_identifier' => $clientIdentifier]);
        if ($client) {
            return $client->verifyClientSecret($clientSecret);
        }
        return false;
    }

    public function checkRestrictedGrantType($clientId, $grantType)
    {
        return true;
    }

    public function getClientDetails($clientIdentifier)
    {
        $client = $client = $this->findOneBy(['client_identifier' => $clientIdentifier]);
        if ($client) {
            $client = $client->toArray();
        }
        return $client;
    }

    public function isPublicClient($clientId)
    {
        return false;
    }

    public function getClientScope($clientId)
    {
        return null;
    }
}
