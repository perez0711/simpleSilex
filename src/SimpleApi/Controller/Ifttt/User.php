<?php

namespace SimpleApi\Controller\Ifttt;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use OAuth2\HttpFoundationBridge\Request as BridgeRequest;
use SimpleApi\Services\Ifttt\Status\User\InformerUser;
use SimpleApi\Services\Ifttt\Helper\AccessToken;
use SimpleApi\Services\Ifttt\Status\User\UserStorage;

class User
{

    const SCOPE = 'ifttt';

    public static function addRoutes($routing)
    {                  
        $routing->get('/user/info' , array(new self() , 'getUserInfo'))
                ->bind('ifttt_v1_user_info');

    }

    public function getUserInfo(Application $app)
    {
        $server   = $app['oauth_server'];
        $response = $app['oauth_response'];
        $request  = $app['request'];

        try {

            $temPermissao = $server->verifyResourceRequest(BridgeRequest::createFromRequest($request) , $response , self::SCOPE);
            $user = null;
            
            if ($temPermissao) {
                
                $accessTokenData = $app['oauth_server']->getResourceController()->getToken();
                $token = $accessTokenData['token'];
                
                $accessTokenHelper = new AccessToken($app['orm.em']);
                $userStorage       = new UserStorage($app['orm.em']);
                $informerUser      = new InformerUser($accessTokenHelper, $userStorage);
                
                $user = $informerUser->getUserInfoFromToken($token);
            }
            
            if($temPermissao && $user){
                $responseData = [
                    "data" => [
                        "name" => $user['descricao'],
                        "id"   => (string)$user['id'],
                        "url" => null
                    ]
                ];
                
                $response->setStatusCode(Response::HTTP_OK);
                $response->setData($responseData);
                 
            }
            
            if($temPermissao && !$user){
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
                $response->setData([]);
            }

        } catch (\Exception $ex) {
            $response = $this->getResponseError($app , $ex);
        }

        return $response;
    }
    
    private function getResponseError($app , $ex)
    {
        $app['logger']->critical($ex->getMessage());
        return new JsonResponse([] , Response::HTTP_INTERNAL_SERVER_ERROR);
    }

}
