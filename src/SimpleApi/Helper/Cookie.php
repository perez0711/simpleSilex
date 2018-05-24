<?php
namespace SimpleApi\Helper;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie as CookieSymfony;

class Cookie
{
    
    const COOKIE_NAME = "SIMPLE_API_COM";
    
    public static function getCookie(Application $app, Request $request,  $cookiename = self::COOKIE_NAME)
    {
        $cookie = null;
        
        if ($request->cookies->has($cookiename)) {
            $cookiecrpt = $request->cookies->get($cookiename);
            $cookie = base64_decode($cookiecrpt);
        }
        
        return $cookie;
    }
    
    public static function setCookie($valueCookie, Application $app, Response $response, \DateTime $expira, $cookiename = self::COOKIE_NAME)
    {
        $valueCookieCrpt = base64_encode($valueCookie);
        
        $cookie = new CookieSymfony($cookiename, $valueCookieCrpt, $expira);
        $response->headers->setCookie($cookie);
    }
}
