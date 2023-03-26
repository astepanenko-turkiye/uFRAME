<?php

$params=array_filter($argv,function($k) { return $k!==0; },ARRAY_FILTER_USE_KEY);

$count_params=count($params);

if($count_params===0 || $count_params===1) {

    throw new \Exception("missing arguments");

} else {

    $task=array_shift($params);

    $task=kebabToPascalCase($task);

    $task="Application\\Tasks\\".$task."Task";

    $action=array_shift($params);

    $action=kebabToCamelCase($action);

    $action=lcfirst($action)."Action";

}

if(class_exists($task)) {

    $task_object=new $task; unset($task);

} else {

    throw new \Exception("no task {$task}");

}

if(method_exists($task_object,$action)) {

    call_user_func_array([$task_object,$action],$params); unset($action,$params);

} else {

    throw new \Exception("no action {$action}");

}