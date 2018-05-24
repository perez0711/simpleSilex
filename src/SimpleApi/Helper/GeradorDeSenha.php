<?php

namespace SimpleApi\Helper;

class GeradorDeSenha
{
    public function gerarSenha($tamanho = 6,  $caracteresAceitos = 'abcdxywz0123456789')
    {
        $max = strlen($caracteresAceitos)-1;

        $rec_usu_pass = null;

        for ($i=0; $i < $tamanho; $i++) {
            $rec_usu_pass .= $caracteresAceitos{mt_rand(0, $max)};
        }

        return $rec_usu_pass;
    }
}
