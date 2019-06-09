<?php
error_reporting(0);
session_start();
define('ROOT',__DIR__.'/../');
define('UPURL',__DIR__.'/');
require(ROOT.'libs/functions.php');
function autoLoadClass($class){
    include(ROOT.str_replace('\\','/',$class).'.php');
}
spl_autoload_register('autoLoadClass');
if(empty($_SERVER['PATH_INFO'])){
    $controller = 'IndexController';
    $action  = 'index';
}else{
    $path = $_SERVER['PATH_INFO'];
    $path = explode('/',$path);
    $path = explode(config('ext-hart'),$path[1]);
    $controller = ucfirst($path[0].'Controller');
    $action = $path[1];
}
$controller = 'controllers\\'.$controller;
$C = new $controller;
$C->$action();