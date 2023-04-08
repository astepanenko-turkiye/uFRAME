<?php

try {

    require_once realpath(dirname(__FILE__,2)).DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR."bootstrap".DIRECTORY_SEPARATOR."bootstrap.php";

    require_once BASE_DIR."app".DS."config".DS."config.php";

    require_once BASE_DIR."app".DS."bootstrap".DS."helper.php";

    require_once BASE_DIR."app".DS."bootstrap".DS."cli.php";

} catch(\Exception $e) {

    echo var_export($e,1).PHP_EOL;

}