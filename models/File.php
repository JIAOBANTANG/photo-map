<?php
namespace models;
class File extends Base{
     public function getFiles($id){
      $sql = "SELECT f_lat lat,f_lng lng,f_slurl thumbnail,f_wsysurl nondes,f_url url,f_video_url video,f_caption caption,f_event event,f_date_time data_time,f_address address,f_id id FROM `m_files` WHERE f_uid =$id AND f_isd='0'";
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