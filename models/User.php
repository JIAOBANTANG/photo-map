<?php
namespace models;
class User extends Base{
    public function findName($name){
    $sql = "SELECT u_id FROM `m_users` WHERE u_name ='$name'";
    $stmt = self::$pdo->query($sql);    
    $data = $stmt->fetch(\PDO::FETCH_NUM);
    return $data;
    }

    public function insert($sql,$data){
        $data = explode(',',$data);
        $stmt = self::$pdo->prepare($sql);
        $ret = $stmt->execute($data);
        return $ret;
     }
     public function getUserInfo($name){
        $sql = "SELECT * FROM `m_users` WHERE u_name ='$name'";
        $stmt = self::$pdo->query($sql);    
        $data = $stmt->fetch(\PDO::FETCH_ASSOC );
        return $data;
     }
     public function CookiegetUserInfo($name,$password){
      $sql = "SELECT * FROM `m_users` WHERE u_name ='$name' AND u_password ='$password' ";
      $stmt = self::$pdo->query($sql);    
      $data = $stmt->fetch(\PDO::FETCH_ASSOC );
      return $data;
   }
}