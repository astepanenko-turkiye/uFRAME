<?php

use Application\Components\response\ErrorHttpResponse;

if(!defined('START_TIME')) define('START_TIME', microtime(1));

require_once realpath(dirname(__FILE__,2)).DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."config.php";

try {

    require_once BASE_DIR."app".DS."bootstrap".DS."helper.php";

    require_once BASE_DIR."app".DS."bootstrap".DS."http.php";

} catch(\Exception $e) {

    $http_response=new ErrorHttpResponse(500,$e->getMessage());

    $http_response->flush();

}


