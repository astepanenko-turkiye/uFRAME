<?php

namespace Application\Controllers\POST;

use Application\Controllers\Controller;

class IndexController extends Controller {

    public function indexAction() {

        if(empty($_POST)) {
            return $this->setHttpResponse(new $this->httpResponseClassName(400,"empty body"));
        }

    }

}

?>