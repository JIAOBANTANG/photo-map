<?php
namespace controllers;
use models\Upload;
use libs\CoordTransform;
use libs\Imgcompress;
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
                list($lngs,$lats)=explode(',',$lngAndLat);
                $gps_data = getGpsInfo($lngs,$lats);
                if($gps_data['status']){
                    //获取区号用于文件移入路径
                    $towncode = $gps_data['regeocode']['addressComponent']['towncode'];
                    //获取具体地点
                    $address = $gps_data['regeocode']['formatted_address'];
                        //创建目录，移动文件
                        $dir = "photos/".$_SESSION['name']."/{$towncode}";
                        makeDir($dir);
                        $url = $dir."/".$file['name'];
                        $slurl = $dir.'/sl'.$file['name'];
                      //图片无损压缩
                       $wsysurl =  UPURL.$dir.'/wsys'.$file['name'];//保存图片的文件名
                       $dbwsysurl = $dir.'/wsys'.$file['name'];
                       $percent = 1;  #原图压缩，不缩放，但体积大大降低
                       $image = (new Imgcompress($saveDir,$percent))->compressImg($wsysurl);
                        //    rename()
                        $id = $_SESSION['id'];
                        suolue($saveDir,$slurl);
                    //    rename()
                        $isRename =   rename($saveDir,$url);
                        if($isRename){
                            $upload = new Upload;
                            $sql = "INSERT INTO m_files(f_uid,f_lng,f_lat,f_url,f_slurl,f_wsysurl,f_date_time,f_towncode,f_address) VALUES($id,'$lngs','$lats','$url','$slurl','$dbwsysurl','$time','$towncode', '$address')";
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
                    echo json_encode([
                        'code'=>4007,
                        'msg'=>'定位信息错误',
                    ]);  
                }
           }else{  
               try{
                   //图片存在exif信息
                   @$exif = exif_read_data($saveDir,'IFD0',false);
                   $image = imagecreatefromstring(file_get_contents($saveDir));
                //    dd($exif['Orientation']);
                   //图片翻转
                   switch($exif['Orientation']) {
                    case 8:
                     $image = imagerotate($image,90,0);
                     break;
                    case 3:
                     $image = imagerotate($image,180,0);
                     break;
                    case 6:
                     $image = imagerotate($image,-90,0);
                     break;
                   }
                   imagejpeg($image, $saveDir);
                   imagedestroy($image);
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
                        $coordtransform = new CoordTransform();
                        $GPS = $coordtransform->wgs84togcj02($lngs,$lats);
                        $lngs = $GPS[0];
                        $lats = $GPS[1];
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
                       $slurl = $dir.'/su'.$file['name'];


                       //图片无损压缩
                       $wsysurl =  UPURL.$dir.'/wsys'.$file['name'];//保存图片的文件名
                       $dbwsysurl = $dir.'/wsys'.$file['name'];
                       $percent = 1;  #原图压缩，不缩放，但体积大大降低
                       $image = (new Imgcompress($saveDir,$percent))->compressImg($wsysurl);

                    //    rename()
                        $id = $_SESSION['id'];
                        suolue($saveDir,$slurl);
                        $isRename = rename($saveDir,$url);
                        if($isRename){
                            $upload = new Upload;
                            $sql = "INSERT INTO m_files(f_uid,f_lng,f_lat,f_url,f_slurl,f_wsysurl,f_date_time,f_towncode,f_address) VALUES($id,'$lngs','$lats','$url','$slurl','$dbwsysurl','$time','$towncode', '$address')";
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
    //上传视频
    // public function upVideo(){
    //     $file = $_FILES['file'];
    //     dd($file);
    //     @$lngAndLat = $_POST['lng-and-lat'];
    //     @$time = $_POST['time'];
    //     $img = $file['tmp_name'];
    //     $saveDir = UPURL.'temp/'.$file['name'];
    //     $isTemp = move_uploaded_file($img,$saveDir);
    //     @$exif = exif_read_data($saveDir,'IFD0',false);
    //     dd($exif);

    // }
}


