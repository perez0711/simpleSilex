<?php

namespace SimpleApi\Helper;

/**
 * Description of Hash
 *
 * @author fmarcusl
 */
class PasswordHash
{
    
    public static $salt = "simple_silex_api_841";
    
    public static function gerarHashSenha($senha, $salt = null)
    {
        return password_hash(self::getRealPassword($senha, $salt), PASSWORD_BCRYPT, ['cost' => 10]);
    }
    
    public static function verificarSenha($senha, $hash, $salt = null)
    {
        return password_verify(self::getRealPassword($senha, $salt), $hash);
    }
    
    private static function getDefaultSaltIfInvalid($salt)
    {
        if (is_null($salt) or strlen($salt) < 4) {
            $salt = self::$salt;
        }
        
        return $salt;
    }
    
    private static function getRealPassword($password, $salt)
    {
        $saltf = self::getDefaultSaltIfInvalid($salt);
        
        return $password . $saltf;
    }
}
