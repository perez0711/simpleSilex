<?php
namespace SimpleApi\Services;

interface AuthenticationInterface
{
    public function authenticate($token);
}
