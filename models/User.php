<?php
namespace models;
class User extends Base{
    public function find($qq){
        $sql = "SELECT u_qq FROM `m_users` WHERE u_qq ='$qq'";
        $stmt = self::$pdo->query($sql);
        $data = $stmt->fetch(\PDO::FETCH_NUM);
        return $data;
    }

    public function insert($data){
        $sql = "INSERT INTO m_users(u_qq,u_password,u_google_auth,u_name,u_avatar,u_email,u_code) VALUES(?,?,?,?,?,?,?)";
        $inser_data = [];
        foreach ($data as $v){
            $inser_data[]=$v;
        }
        $stmt = self::$pdo->prepare($sql);
        $ret = $stmt->execute($inser_data);
        return $ret;
     }

     public function getUserInfo($qq,$password){
        $sql = "SELECT * FROM `m_users` WHERE u_qq ='$qq' and u_password = '$password'";
        $stmt = self::$pdo->query($sql);    
        $data = $stmt->fetch(\PDO::FETCH_ASSOC );
        return $data;
     }


    public function getLoveInfo($id){
        $sql = "SELECT * FROM `m_users` WHERE u_id ='$id' ";
        $stmt = self::$pdo->query($sql);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC );
        return $data;
    }

     public function CookiegetUserInfo($qq,$password){
      $sql = "SELECT * FROM `m_users` WHERE u_qq ='$qq' AND u_password ='$password' ";
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
    public function getLoveId($id){
        return self::$pdo->query("SELECT u_love_uid FROM `m_users` WHERE u_id = '$id' ")->fetch()['u_love_uid'];
    }

    public function getCodeInfo($code){
        $sql = "SELECT * FROM `m_users` WHERE u_code ='$code'";
        $stmt = self::$pdo->query($sql);    
        $data = $stmt->fetch(\PDO::FETCH_ASSOC );
        return $data;
    }

    public function bindLove($my_id,$love_to_id,$love_to_code,$love_to_time){
        $sql = "UPDATE m_users SET u_love_code= '$love_to_code',u_love_uid = '$love_to_id',u_love_time='$love_to_time' WHERE u_id= $my_id";
        $sta = self::$pdo->exec($sql);
        return $sta;
    }

    //修改关联信息
}