<?php

namespace Application\Components\response;

class OkHttpResponse extends HttpResponse {

    public function __construct($code=200,$body=null) {

        parent::__construct($code,$body);

    }

}