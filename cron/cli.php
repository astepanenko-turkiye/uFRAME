<?php

if(!defined('START_TIME')) define('START_TIME', microtime(1));

require_once realpath(dirname(__FILE__,2)).DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."config.php";

try {

    require_once BASE_DIR."app".DS."bootstrap".DS."helper.php";

    require_once BASE_DIR."app".DS."bootstrap".DS."cli.php";

} catch(\Exception $e) {

    echo var_export($e,1).PHP_EOL;

}