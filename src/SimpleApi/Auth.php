<?php

namespace SimpleApi;

use Silex\Application;
use Silex\ControllerProviderInterface;

use SimpleApi\Controller\Auth\Authorize;
use SimpleApi\Controller\Auth\Token;

class Auth extends AbstractControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $this->setup($app);
        
        $routing = $app['controllers_factory'];
        
        Token::addRoutes($routing);
        Authorize::addRoutes($routing);
        
        return $routing;
    }
}
