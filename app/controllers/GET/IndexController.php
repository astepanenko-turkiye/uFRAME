<?php

namespace Application\Controllers\GET;

use
    Application\Controllers\Controller
;

class IndexController extends Controller {

    public function indexAction() {

        $this->view->setVar("title","Index Page");
        $this->view->setVar("h1","Index Page");

    }

}

?>