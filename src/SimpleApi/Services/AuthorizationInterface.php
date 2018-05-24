<?php

namespace SimpleApi\Services;

interface AuthorizationInterface
{
    public function isAuthorized($token, $resource);
}
