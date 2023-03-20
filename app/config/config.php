<?php

register_shutdown_function(function () {

    $e=error_get_last();

    if(!is_null($e)) { var_export($e); exit; }

});

if(!defined("TIMEOUT")) define("TIMEOUT", 30);

if(!defined("DS")) define("DS", DIRECTORY_SEPARATOR);

if(!defined("BASE_DIR")) define("BASE_DIR", realpath( dirname(__FILE__,3) ).DS);

if(!defined("NAMESPACES")) define("NAMESPACES",[
    "Application\\Controllers" => BASE_DIR."app".DS."controllers"
    ,"Application\\Components" => BASE_DIR."app".DS."components"
    ,"Application\\Tasks" => BASE_DIR."cron".DS."tasks"
]);

spl_autoload_register(function($className) {

    $data=explode("\\",$className);

    $class=array_pop($data);

    $class_filepath=null;

    $namespace="";

    foreach($data as $k=>$v) {

        $namespace.=($k===0 ? $v : "\\".$v);

        if(is_null($class_filepath)) {

            array_key_exists($namespace,NAMESPACES) && $class_filepath=NAMESPACES[$namespace];

        } else {

            $class_filepath.=DS.$v;

        }

    }

    $class_filepath.=DS.$class.".php";

    if(file_exists($class_filepath)) include_once $class_filepath;

});

if(!defined("CONFIG")) define("CONFIG",[
    "database" => [
        "host" => null,
        "port" => null,
        "username" => null,
        "password" => null,
        "dbname" => null,
        "charset" => "utf8mb4",
    ],
]);