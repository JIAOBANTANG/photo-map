<?php
namespace models;
class File extends Base{
     public function getFiles($id){
      $sql = "SELECT f_lat lat,f_lng lng,f_url thumbnail,f_url url,f_video_url video,f_caption caption,f_event event,f_date_time data_time,f_address address,f_id id FROM `m_files` WHERE f_uid =$id";
      $stmt = self::$pdo->query($sql);    
      $data = $stmt->fetchAll(\PDO::FETCH_ASSOC );
      return $data;
   }
}