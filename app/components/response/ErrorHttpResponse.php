<?php

namespace Application\Components\response;

class ErrorHttpResponse extends HttpResponse {

    public function __construct($code=404,$body=null) {

        parent::__construct($code,$body);

    }

}