<?php
namespace models;
class Upload extends Base{
    public function insert(...$data){
        $sql = "INSERT INTO m_files(f_uid,f_lng,f_lat,f_url,f_slurl,f_wsysurl,f_date_time,f_towncode,f_address) VALUES($data)";
       return self::$pdo->exec($sql);
    }
}