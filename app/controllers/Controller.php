<?php

namespace Application\Controllers;

use Application\Components\common\View;

use Application\Components\response\HttpResponse;
use Application\Components\response\HttpResponseFactory;
use Application\Components\response\JsonResponse;
use Application\Components\response\OkHttpResponse;
use Application\Components\response\ErrorHttpResponse;

abstract class Controller {

    protected $http_response;

    public function __get($name) {

        if($name==="view") {

            $this->$name=new View();

            return $this->$name;

        }

        if($name==="httpResponseClassName") {

            $this->$name=HttpResponseFactory::get(CONTENT_TYPE);

            return $this->$name;

        }

        return null;

    }

    public function __destruct() {

        if(!isset($this->http_response)) $this->setHttpResponse(new ErrorHttpResponse);

    }

    public function setHttpResponse(HttpResponse $http_response) {

        $this->http_response=$http_response;

        return true;

    }

    public function getHttpResponse() {

        if(!$this->http_response) {

            try {

                $content=$this->view->getContent();

                $this->http_response=new OkHttpResponse(200,$content); unset($html);

            } catch(\Exception $e) {

                $this->http_response=new ErrorHttpResponse(500,$e->getMessage());

            }

        }

        return $this->http_response;

    }

    public function getAjaxResponse() {

        $this->view->setRenderLevel(View::ACTION);

        $title=$this->view->getVar("title");

        $html=$this->view->getContent();

        return $this->setHttpResponse(new $this->httpResponseClassName(200,[
            "success"=>true,
            "title"=>$title,
            "html"=>$html,
            "hash"=>md5($title.$html)
        ]));

    }

}