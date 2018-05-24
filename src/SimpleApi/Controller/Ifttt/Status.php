<?php

namespace SimpleApi\Controller\Ifttt;

use Silex\Application;
use Doctrine\DBAL\Types\Type;
use SimpleApi\Entity\LoginAppClientIftt;
use Symfony\Component\HttpFoundation\Response;

class Status
{

    public static function addRoutes($routing)
    {                  
        $routing->get('/status', array(new self() , 'getStatus'))
                ->bind('ifttt_v1_get_status');

    }

    public function getStatus(Application $app)
    {
         try {
           
            $request = $app['request'];
            
            $channelKey = $request->headers->get('IFTTT-Channel-Key');
            $serviceKey = $request->headers->get('IFTTT-Service-Key');
            
            $isAuthorized = false;
            
            if($channelKey && $serviceKey){
                $isAuthorized = $this->checkStatus($app, $channelKey, $serviceKey);
            }
        
            if($isAuthorized){
                $response = new Response();
            }
            
            if($isAuthorized){
                $response = new Response('', Response::HTTP_UNAUTHORIZED);
            }
            
            
        } catch (\Exception $ex) {
            $response = new Response('', Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return $response;
    }
    
    private function checkStatus($app, $channelKey, $serviceKey)
    {
        $em = $app['orm.em'];
        $qb = $em->createQueryBuilder();
        
        $q  = $qb->select('a')
                 ->from(LoginAppClientIftt::class, 'a')
                 ->where(
                   $qb->expr()->eq('a.service_key', ':service_key'),
                   $qb->expr()->eq('a.channel_key', ':channel_key')
                 )
                 ->setParameter('service_key', $serviceKey, Type::STRING)
                 ->setParameter('channel_key', $channelKey, Type::STRING)
                 ->getQuery();
        
        
        try {
            $service = $q->getResult();
        } catch (\Exception $e) {
            $service = null;
        }
        
        return !is_null($service);
    }
    
    

}
