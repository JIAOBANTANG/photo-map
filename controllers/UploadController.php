<?php
namespace controllers;
use models\Upload;
class UploadController{
    public function index(){
        //接受表单传递过来的数据
        $file = $_FILES['file'];
        @$lngAndLat = $_POST['lng-and-lat'];
        @$time = $_POST['time'];
        $img = $file['tmp_name'];
        $saveDir = UPURL.'temp/'.$file['name'];
        $isTemp = move_uploaded_file($img,$saveDir);
        if($isTemp){
            if($lngAndLat){
                //获取定位信息
                list($lng,$lat)=explode(',',$lngAndLat);
                $gps_data = getGpsInfo($lng,$lat);
                //获取区号用于文件移入路径
                $towncode = $gps_data['regeocode']['addressComponent']['towncode'];
                //获取具体地点
                $address = $gps_data['regeocode']['formatted_address'];
                    //创建目录，移动文件
                    $dir = "photos/".$_SESSION['name']."/{$towncode}";
                    makeDir($dir);
                    $url = $dir."/".$file['name'];
                 //    rename()
                     $isRename =   rename($saveDir,$url);
                     if($isRename){
                         $upload = new Upload;
                         $sql = "INSERT INTO m_files(f_uid,f_lng,f_lat,f_url,f_date_time,f_towncode,f_address) VALUES(1,'$lng','$lat','$url','$time','$towncode', '$address')";
                         $isUp = $upload->insert($sql);
                         if($isUp){
                             echo json_encode([
                                 'code'=>2000,
                                 'msg'=>'上传成功',
                                 'address'=>$address
                             ]);    
                         }
                     }
           }else{  
               try{
                   //图片存在exif信息
                   @$exif = exif_read_data($saveDir,'IFD0',false);
                   //获取经度
                   @$lngs = getGps($exif["GPSLongitude"], $exif['GPSLongitudeRef']);
                   //获取纬度
                   @$lats = getGps($exif["GPSLatitude"], $exif['GPSLatitudeRef']);
                   if(!$lngs && !$lats){
                       //删除文件
                       // @unlink($saveDir);
                       echo json_encode([
                           'code'=>4001,
                           'msg'=>'没有获取到图片的定位信息'
                       ]);
                    //    dd("没有经纬度");
                   }else{
                       //获取拍摄时间
                       $time = getDateTime($exif);
                        //获取定位信息
                       $gps_data = getGpsInfo($lngs,$lats);
                       //获取区号用于保存路径
                       $towncode = $gps_data['regeocode']['addressComponent']['towncode'];
                       //获取具体地点
                       $address = $gps_data['regeocode']['formatted_address'];
                       //创建目录，移动文件
                       $dir = "photos/".$_SESSION['name']."/{$towncode}";
                       makeDir($dir);
                       $url = $dir."/".$file['name'];
                    //    rename()
                        $id = $_SESSION['id'];
                        $isRename =   rename($saveDir,$url);
                        if($isRename){
                            $upload = new Upload;
                            $sql = "INSERT INTO m_files(f_uid,f_lng,f_lat,f_url,f_date_time,f_towncode,f_address) VALUES($id,'$lngs','$lats','$url','$time','$towncode', '$address')";
                            $isUp = $upload->insert($sql);
                            if($isUp){
                                echo json_encode([
                                    'code'=>2000,
                                    'msg'=>'上传成功',
                                    'address'=>$address
                                ]);    
                            }
                        }
                   }
               }catch(Exception $e){
                   echo $e->getMessage();
               }    
           }   
        }else{
            echo json_encode([
                'code'=>4004,
                'msg'=>'这张图片貌似出了问题，暂已屏蔽上传'
            ]);
        }
    }
    public function login(){

    }
}


