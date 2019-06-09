<?php
namespace models;
use PDO;
class Base{
    public static $pdo = null;
    public function __construct(){
        if(self::$pdo === null){
            // 读取配置文件
            $config = config('db');
            // 取日志的数据
            self::$pdo = new PDO('mysql:host='.$config['host'].';dbname='.$config['dbname'], $config['user'], $config['password']);
            self::$pdo->exec('SET NAMES '.$config['charset']);
        }
    }
 }