<?php
namespace models;
class Upload extends Base{
    public function insert($sql){
       return self::$pdo->exec($sql);
    }
}