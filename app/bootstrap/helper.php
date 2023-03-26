<?php

if(!function_exists('guidv4')) {
    function guidv4() {
        $data=openssl_random_pseudo_bytes(16);

        $data[6]=chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8]=chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}

if(!function_exists('camelToKebabCase')) {
    function camelToKebabCase($str) {

        return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'],'$1-$2',$str));

    }
}

if(!function_exists('kebabToCamelCase')) {
    function kebabToCamelCase($str) {

        $str=str_replace(" ","",ucwords(str_replace("-"," ",$str)));

        $str[0]=strtolower($str[0]);

        return $str;

    }
}

if(!function_exists('kebabToPascalCase')) {
    function kebabToPascalCase($str) {

        $str=str_replace(" ","",ucwords(str_replace("-"," ",$str)));

        return $str;

    }
}

if(!function_exists('mb_ucfirst')) {
    function mb_ucfirst($string,$force=false) {
        return mb_strtoupper(mb_substr($string,0,1)).($force ? mb_strtolower(mb_substr($string,1)) : mb_substr($string,1));
    }
}

if(!function_exists('mb_lcfirst')) {
    function mb_lcfirst($string) {
        return mb_strtolower(mb_substr($string,0,1)).mb_substr($string,1);
    }
}