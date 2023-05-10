<?php

ini_set("max_execution_time",TIMEOUT); set_time_limit(TIMEOUT);

if(!defined("BASE_URL")) define("BASE_URL","//".$_SERVER['SERVER_NAME']);

if(!empty($_COOKIE) && array_key_exists("PHPSESSID",$_COOKIE)) session_start(['cookie_lifetime' => 60*60*24*30]);

$content_type=(
(!empty($_SERVER['HTTP_CONTENT_TYPE']) && substr($_SERVER['HTTP_CONTENT_TYPE'],0,11)==="application")
? strtolower($_SERVER['HTTP_CONTENT_TYPE'])
: (
    (!empty($_SERVER['HTTP_ACCEPT']) && substr($_SERVER['HTTP_ACCEPT'],0,11)==="application")
    ? strtolower($_SERVER['HTTP_ACCEPT'])
    : "application/json"
)
);
if(!defined("CONTENT_TYPE")) define("CONTENT_TYPE",$content_type); unset($content_type);

$is_ajax=(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])==='xmlhttprequest');
if(!defined("IS_AJAX")) define("IS_AJAX",$is_ajax); unset($is_ajax);

$request_method=array_key_exists("HTTP_ACTUAL_REQUEST_TYPE",$_SERVER) ? $_SERVER['HTTP_ACTUAL_REQUEST_TYPE'] : $_SERVER["REQUEST_METHOD"];

if(!array_key_exists($request_method,[
    "GET"=>null,
    "POST"=>null,
    "HEAD"=>null,
    "OPTIONS"=>null
])) $request_method="GET";

if(!defined("REQ_TYPE")) define("REQ_TYPE",$request_method); unset($request_method);

$auth=null;

if(array_key_exists("HTTP_AUTHORIZATION",$_SERVER)) $auth=trim(str_replace("Bearer","",$_SERVER["HTTP_AUTHORIZATION"]));

if(!defined("AUTH_TOKEN")) define("AUTH_TOKEN",$auth); unset($auth);

$auth_tokens=[
];

$is_compressed_request=0;

if(array_key_exists("HTTP_CMPRSDREQ",$_SERVER)) $is_compressed_request=trim($_SERVER["HTTP_CMPRSDREQ"]);

if(!defined("CMPRSDREQ")) define("CMPRSDREQ",$is_compressed_request); unset($is_compressed_request);


$is_compressed_response=0;

if(array_key_exists("HTTP_CMPRSDRES",$_SERVER)) $is_compressed_response=trim($_SERVER["HTTP_CMPRSDRES"]);

if(!defined("CMPRSDRES")) define("CMPRSDRES",$is_compressed_response); unset($is_compressed_response);



if(REQ_TYPE==="POST") {

    $input=file_get_contents("php://input");

    if(!empty(CMPRSDREQ)) {

        if(!array_key_exists(AUTH_TOKEN,$auth_tokens)) {

            $http_response=new \Application\Components\response\ErrorHttpResponse(403);
            $http_response->flush();
            exit;

        } else {

            switch(CMPRSDREQ) {
                case "bz2":
                    $input=bzdecompress($input);
                    break;
                case "gzip":
                    $input=gzuncompress($input);
                    break;
                case "zlib":
                    $input=gzdecode($input);
                    break;
            }

        }

    }

    if(!empty($input)) {

        switch(CONTENT_TYPE) {
            case "application/x-msgpack":

                $_POST=msgpack_unpack($input);

                break;

            default:

                $_POST=json_decode($input,true);

                break;
        }

    }

    if(!defined("RAW_INPUT")) define("RAW_INPUT",$input);

    unset($input);

}



if(!defined("DEFAULT_CONTROLLER")) define("DEFAULT_CONTROLLER","index");
if(!defined("DEFAULT_ACTION")) define("DEFAULT_ACTION","index");

if(!defined("REQUEST_URI")) define("REQUEST_URI",$_SERVER["REQUEST_URI"]);

$preparse_url=null; if(array_key_exists("REQUEST_URI",$_SERVER)) $preparse_url=parse_url($_SERVER["REQUEST_URI"],PHP_URL_PATH);
$params=explode("/",$preparse_url); unset($preparse_url); $params=array_filter($params,function($v) { return $v!==""; });

$count_params=count($params);

if($count_params===0) {

    $controller=DEFAULT_CONTROLLER;
    $action=DEFAULT_ACTION;

} elseif($count_params===1) {

    $controller=array_shift($params);
    $action=DEFAULT_ACTION;

} else {

    $controller=array_shift($params);
    $action=array_shift($params);

}

if(!defined("VIEW_ACTION_FOLDER")) define("VIEW_ACTION_FOLDER",$controller);

$controller=kebabToPascalCase($controller);

$controller="Application\\Controllers\\".(REQ_TYPE==="HEAD" ? "GET" : REQ_TYPE)."\\".$controller."Controller"; unset($controller_array);

if(!defined("CONTROLLER")) define("CONTROLLER",$controller);

$original_action=$action;

$action=kebabToCamelCase($action);

$action_name=$action."Action"; unset($action_array);

if(class_exists($controller)) {

    $controller_object=new $controller; unset($controller);

} else {

    throw new \Exception("no controller {$controller}");

}

if(!method_exists($controller_object,$action_name)) {

    array_unshift($params,$original_action); $action="index"; $action_name="indexAction"; unset($original_action);

}

if(!defined("VIEW_ACTION_FILE")) define("VIEW_ACTION_FILE","{$action}.php");

if(!defined("ACTION")) define("ACTION",$action_name);
if(!defined("PARAMS")) define("PARAMS",$params);

if(method_exists($controller_object,$action_name)) {

    call_user_func_array([$controller_object,$action_name],$params); unset($action_name,$action,$params);

} else {

    throw new \Exception("no action {$action_name}");

}

$http_response=$controller_object->getHttpResponse();

$http_response->flush();