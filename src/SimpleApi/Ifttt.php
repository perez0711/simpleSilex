<?php

namespace SimpleApi;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class Ifttt extends AbstractControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $this->setup($app);
        
        $controllers = $app['controllers_factory'];
        
        Controller\Ifttt\Status::addRoutes($controllers);
        Controller\Ifttt\User::addRoutes($controllers);
        
        
        $controllers->match("{url}", function ($url) use ($app) {
            return new Response('', 204);
        })->assert('url', '.*')->method("OPTIONS");
        
        $controllers->before(function (Request $request) use ($app) {

            $method = $request->getMethod();
            
            if ($method == 'OPTIONS') {
                return new Response('', Response::HTTP_NO_CONTENT);
            }
            
            $this->setLanguage($request, $app);
        });
        
        return $controllers;
    }
    
    private function setLanguage(Request $request, Application $app)
    {
        $idoma = "pt-BR";
        
        foreach ($request->getLanguages() as $lang) {
            if (strpos($lang, "pt") !== false) {
                break;
            }
            if (strpos($lang, "en") !== false) {
                $idoma = "en";
                break;
            }
            if (strpos($lang, "es") !== false) {
                $idoma = "es";
                break;
            }
        }
        
        $app['translator']->setLocale($idoma);
    }
}
