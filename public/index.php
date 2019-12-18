<?php
session_start();
define('ROOT',__DIR__.'/../');
define('UPURL',__DIR__.'/');
require_once ROOT . 'vendor/autoload.php';
require(ROOT.'libs/functions.php');
function autoLoadClass($class){
    include(ROOT.str_replace('\\','/',$class).'.php');
}
spl_autoload_register('autoLoadClass');
$server_ext = '%F0%9F%92%95';
$server_path_item =2;
if(strstr(PHP_OS, 'WIN')){
    $server_ext = '💕';
    $server_path_item =1;
}
if(empty($_SERVER['PATH_INFO'])){
    $controller = 'IndexController';
    $action  = 'index';
}else{
    $path = $_SERVER['PATH_INFO'];
    $path = explode('/',$path);
    $path = explode($server_ext,$path[$server_path_item]);
    $controller = ucfirst($path[0].'Controller');
    $action = $path[1];
}
$controller = 'controllers\\'.$controller;
$C = new $controller;
$C->$action();