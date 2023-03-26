<?php

namespace Application\Components\response;

abstract class HttpResponse {

    protected $code;
    protected $content_type='Content-Type: text/html; charset=utf-8';
    protected $body;
    protected $COMPRESSED;

    protected function response_header() {

        http_response_code($this->code);
        header($this->content_type);

    }

    public function __construct($code=200,$body=null,$COMPRESSED=null) {

        $this->code=$code;

        $this->COMPRESSED=$COMPRESSED;

        if(!is_null($body)) $this->setBody($body);

    }

    public function setBody($body) {

        $this->body=$body;

    }

    public function flush() {

        $this->response_header();

        $fp=fopen("php://output","w");

        if(is_null($this->body)) $this->body="Unknown error";

        switch($this->COMPRESSED) {
            case "bz2":
                $this->body=bzcompress($this->body,9);
                break;
            case "gzip":
                $this->body=gzcompress($this->body,9);
                break;
            case "zlib":
                $this->body=gzencode($this->body,9);
                break;
        }

        fwrite($fp,$this->body);

        fclose($fp);

    }

}