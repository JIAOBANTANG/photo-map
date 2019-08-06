<?php
namespace models;
class File extends Base{
     public function getFiles($id){
      $fId = self::$pdo->query("SELECT f_fid FROM `m_friend` WHERE f_mid = $id AND f_is = 1")->fetch()['f_fid']; 
      $sql = "SELECT f.f_lat lat,f.f_lng lng,f.f_slurl thumbnail,f.f_wsysurl nondes,f.f_url url,f.f_video_url video,f.f_caption caption,f.f_event event,f.f_date_time data_time,f.f_address address,f.f_id id,u.u_name name,u.u_avatar avatar FROM `m_files` f LEFT JOIN m_users u ON f.f_uid = u.u_id WHERE f_uid =$id AND f_isd='0'";
      if($fId){
      $sql = "SELECT f.f_lat lat,f.f_lng lng,f.f_slurl thumbnail,f.f_wsysurl nondes,f.f_url url,f.f_video_url video,f.f_caption caption,f.f_event event,f.f_date_time data_time,f.f_address address,f.f_id id,u.u_name name,u.u_avatar avatar FROM `m_files` f LEFT JOIN m_users u ON f.f_uid = u.u_id WHERE f_uid IN($id,$fId) AND f_isd='0'";
      // $sql = "SELECT f_lat lat,f_lng lng,f_slurl thumbnail,f_wsysurl nondes,f_url url,f_video_url video,f_caption caption,f_event event,f_date_time data_time,f_address address,f_id id FROM `m_files` WHERE f_uid IN($id,$fId) AND f_isd='0'";
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
   //关联相关先关掉
   // public function getFriendId($id){
   //    $fId = self::$pdo->query("SELECT f_fid FROM `m_friend` WHERE f_mid = $id  AND f_is = 1")->fetch()['f_fid']; 
   //    return $fId;
   // }
}