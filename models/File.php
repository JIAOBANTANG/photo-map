<?php
namespace models;
class File extends Base{

    public function getFiles($id){
      $love_id = self::$pdo->query("SELECT u_love_uid FROM `m_users` WHERE u_id = '$id' ")->fetch()['u_love_uid'];
      $sql = "SELECT f.f_lat lat,f.f_lng lng,f.f_slurl thumbnail,f.f_wsysurl nondes,f.f_url url,f.f_video_url video,f.f_caption caption,f.f_event event,f.f_date_time data_time,f.f_address address,f.f_id id,u.u_name name,u.u_avatar avatar FROM `m_files` f LEFT JOIN m_users u ON f.f_uid = u.u_id WHERE f_uid =$id AND f_isd='0'";
      if($love_id){
        $sql = "SELECT f.f_lat lat,f.f_lng lng,f.f_slurl thumbnail,f.f_wsysurl nondes,f.f_url url,f.f_video_url video,f.f_caption caption,f.f_event event,f.f_date_time data_time,f.f_address address,f.f_id id,u.u_name name,u.u_avatar avatar FROM `m_files` f LEFT JOIN m_users u ON f.f_uid = u.u_id WHERE f_uid IN($id,$love_id) AND f_isd='0'";
      }
      $stmt = self::$pdo->query($sql);
      $data = $stmt->fetchAll(\PDO::FETCH_ASSOC );
      return $data;
   }

   public function del($fid){
      $uid = $_SESSION['id'];
      $sql = "UPDATE m_files SET f_isd= '1' WHERE f_uid= $uid and f_id=$fid";
      $sta = self::$pdo->exec($sql);
      return $sta;
   }

   public function addMiaoShu($fid,$content){
      $uid = $_SESSION['id'];
      $sql = "UPDATE m_files SET f_event= ? WHERE f_uid= $uid and f_id=$fid";
      $stmt = self::$pdo->prepare($sql);
      $sta = $stmt->execute([
         $content
      ]);
      return $sta;
   }
}