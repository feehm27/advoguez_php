<?php

namespace App\Http\Utils;

class MaskUtils
{
    public static function maskPhone(String $number = null)
    {
        if(!$number) return $number;
        
        $number= "(".substr($number,0,2).") ".substr($number,2,-4)." - ".substr($number,-4);
        return $number;
    }

    public static function maskCPF(String $cpf = null)
    {
        if (!$cpf) return '';

        if (strlen($cpf) == 11){
            return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9);
        }
        
        return $cpf;
    }
}