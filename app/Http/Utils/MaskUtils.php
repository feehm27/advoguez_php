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

    public static function maskProcessNumber($str) {

        $characteres = ['-', '.'];
        $newStr = $str;
  
        $first = substr($newStr, 0, 7).$characteres[0];
        $second = $str[7].$str[8].$characteres[1];
        $three = $str[9].$str[10].$str[11].$str[12].$characteres[1];
        $four = $str[13].$characteres[1];
        $five = $str[14].$str[15].$characteres[1];
        $six = $str[16].$str[17].$str[18].$str[19];

        return $first.$second.$three.$four.$five.$six;
    }

    public static function maskPrice($value) 
    {
        $lenght = strlen($value);
        $lastPosition = substr($value, -2);
        $firstPosition = substr($value, 0, $lenght -2);

        return 'R$ ' .$firstPosition.','.$lastPosition;
    }
}