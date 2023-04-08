<?php

try {

    require_once realpath(dirname(__FILE__,2)).DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR."bootstrap".DIRECTORY_SEPARATOR."bootstrap.php";

    require_once BASE_DIR."app".DS."config".DS."config.php";

    require_once BASE_DIR."app".DS."bootstrap".DS."helper.php";

    require_once BASE_DIR."app".DS."bootstrap".DS."http.php";

} catch(\Exception $e) {

    $http_response=new Application\Components\response\ErrorHttpResponse(500,$e->getMessage());

    $http_response->flush();

}


