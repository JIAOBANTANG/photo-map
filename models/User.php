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
   //关联相关先关掉
   // public function getFriendInfo($name){
   //    $info = self::$pdo->query("SELECT u_id,u_avatar FROM `m_users` WHERE u_name = '$name' ")->fetch(); 
   //    return $info;
   // }
   // public function getFriend($id){
   //    $my = self::$pdo->query("SELECT f.*,u.u_id,u.u_name,u.u_avatar FROM m_friend f lEFT JOIN m_users u ON u.u_id = f.f_fid WHERE f.f_mid = $id")->fetch();
   //    $you = self::$pdo->query("SELECT f.*,u.u_id,u.u_name,u.u_avatar FROM m_friend f lEFT JOIN m_users u ON u.u_id = f.f_fid WHERE f.f_fid = $id")->fetch();
   //    $info = [
   //       'my'=>$my,
   //       'you'=>$you
   //    ];
   //    return $info;
   // }
}