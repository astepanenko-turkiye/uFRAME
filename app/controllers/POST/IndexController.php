<?php

namespace Application\Controllers\POST;

use
    Application\Controllers\Controller
    ,Application\Components\response\JsonResponse
;

class IndexController extends Controller {

    public function indexAction() {

        if(empty($_POST)) {
            return $this->setHttpResponse(new JsonResponse(400,"empty body"));
        }

    }

}

?>