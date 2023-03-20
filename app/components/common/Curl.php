<?php

namespace Application\Components\common;

class Curl {

    public function request($url,$a,$header=[]) {

        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);

        if($a) {
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,is_array($a) ? http_build_query($a) : $a);
        }

        if($header) {
            curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        }

        curl_setopt($ch,CURLOPT_VERBOSE,false);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

        $response=curl_exec($ch);
        $httpcode=curl_getinfo($ch,CURLINFO_HTTP_CODE);

        curl_close($ch);

        return [$httpcode,$response];

    }

}