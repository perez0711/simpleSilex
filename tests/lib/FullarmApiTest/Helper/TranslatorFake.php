<?php

namespace SimpleApiTest\Helper;

use Symfony\Component\Translation\TranslatorInterface;

class TranslatorFake implements TranslatorInterface
{
    public function getLocale()
    {
        
    }

    public function setLocale($locale)
    {
        
    }

    public function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        return [
            'id' => $id,
            'parameters' => $parameters,
            'domain' => $domain,
            'locale' => $locale,
        ];
    }

    public function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null)
    {
        
    }

}
