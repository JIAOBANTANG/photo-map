<?php
namespace controllers;

use models\Upload;
use libs\CoordTransform;
use WebPConvert\WebPConvert;

class UploadController extends Controller
{
    
    public function index()
    {
        //接受表单传递过来的数据
        $file = $_FILES['file'];
        @$lngAndLat = $_POST['lng-and-lat'];
        @$time = $_POST['time'];
        $img = $file['tmp_name'];
        makeDir(UPURL.'photos/'.$_SESSION['qq']);
        $dbsave_dir = 'photos/'.$_SESSION['qq'].'/'.$file['name'];
        $saveDir = UPURL.'photos/'.$_SESSION['qq'].'/'.$file['name'];
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
                        $dir = "photos/".$_SESSION['qq']."/{$towncode}";
                        makeDir($dir);
                        $url = $dir."/".$file['name'];
                        $slurl = $dir.'/SL'.$file['name'];
                        //图片无损压缩
                        $wsysurl =  UPURL.$dir.'/WEBP'.$file['name'].".webp";//保存图片的文件名
                        $dbwsysurl = $dir.'/WEBP'.$file['name'].".webp";
                        WebPConvert::convert($saveDir,$wsysurl, $options=[]);
                        $id = $_SESSION['id'];
                        suolue($saveDir,$slurl);
                        $upload = new Upload;
                        $sql = "INSERT INTO m_files(f_uid,f_lng,f_lat,f_url,f_slurl,f_wsysurl,f_date_time,f_towncode,f_address) VALUES($id,'$lngs','$lats','$dbsave_dir','$slurl','$dbwsysurl','$time','$towncode', '$address')";
                        $isUp = $upload->insert($sql);
                        if($isUp){
                            echo json_encode([
                                'code'=>200,
                                'msg'=>'上传成功',
                                'address'=>$address
                        ]);
                        }
                }else{
                    return $this->response('定位信息错误',407);
                }
           }else{
               try{
                   //图片存在exif信息
                   @$exif = exif_read_data($saveDir,'IFD0',false);
                   $image = imagecreatefromstring(file_get_contents($saveDir));
                   //获取经度
                   @$lngs = getGps($exif["GPSLongitude"], $exif['GPSLongitudeRef']);
                   //获取纬度
                   @$lats = getGps($exif["GPSLatitude"], $exif['GPSLatitudeRef']);
                   if(!$lngs && !$lats){
                       return $this->response('没有获取到图片的定位信息',400);
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
                       $dir = "photos/".$_SESSION['qq']."/{$towncode}";
                       makeDir($dir);
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
                       imagejpeg($image, $dir.'/'.$file['name']);
                       imagedestroy($image);
                       $url = $dir."/".$file['name'];
                       $slurl = $dir.'/SL_'.$file['name'];
                       $wsysurl =  UPURL.$dir.'/WEBP'.$file['name'].".webp";//保存图片的文件名
                       $dbwsysurl = $dir.'/WEBP'.$file['name'].".webp";
                       //缩略图
                       suolue($url,$slurl);
                       WebPConvert::convert($url,$wsysurl,$options=[]);
                       @unlink($dir.'/'.$file['name']);
                        $id = $_SESSION['id'];
                        $upload = new Upload;
                        $sql = "INSERT INTO m_files(f_uid,f_lng,f_lat,f_url,f_slurl,f_wsysurl,f_date_time,f_towncode,f_address) VALUES($id,'$lngs','$lats','$dbsave_dir','$slurl','$dbwsysurl','$time','$towncode', '$address')";
                        $isUp = $upload->insert($sql);
                        if($isUp){
                            echo json_encode([
                                'code'=>200,
                                'msg'=>'上传成功',
                                'address'=>$address
                            ]);
                        }
                   }
               }catch(Exception $e){
                   echo $e->getMessage();
               }    
           }   
        }else{
            echo json_encode([
                'code'=>404,
                'msg'=>'这张图片貌似出了问题，暂已屏蔽上传'
            ]);
        }
    }
}


