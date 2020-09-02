<?php

namespace controllers;

use models\Upload;
use libs\CoordTransform;
use traits\HttpTrait;
use WebPConvert\WebPConvert;

class UploadController
{
    use HttpTrait;
    private $upload;

    public function __construct()
    {
        $this->upload = new Upload;
    }

    public function index()
    {
        //接受表单传递过来的数据
        $file = $_FILES['file'];
        $lngAndLat = empty($_POST['lng-and-lat']) ? '' : $_POST['lng-and-lat'];
        $time = empty($_POST['time']) ? date('Y-m-d H:i:s') : $_POST['time'];
        $img = $file['tmp_name'];
        makeDir(UPURL . 'photos/' . $_SESSION['qq']);
        $dbSaveDir = 'photos/' . $_SESSION['qq'] . '/' . $file['name'];
        $saveDir = UPURL . 'photos/' . $_SESSION['qq'] . '/' . $file['name'];
        move_uploaded_file($img, $saveDir);
        if ($lngAndLat) {
            //获取定位信息
            list($lng, $lat) = explode(',', $lngAndLat);
            $gpsData = getGpsInfo($lng, $lat);
            if ($gpsData['status']) {
                //获取区号用于文件移入路径
                $townCode = $gpsData['regeocode']['addressComponent']['towncode'];
                //获取具体地点
                $address = $gpsData['regeocode']['formatted_address'];
                //创建目录，移动文件
                $dir = "photos/" . $_SESSION['qq'] . "/{$townCode}";
                makeDir($dir);
                $url = $dir . "/" . $file['name'];
                $slurl = $dir . '/SL' . $file['name'];
                //图片无损压缩
                $wsysurl = UPURL . $dir . '/WEBP' . $file['name'] . ".webp";//保存图片的文件名
                $dbwsysurl = $dir . '/WEBP' . $file['name'] . ".webp";
                WebPConvert::convert($saveDir, $wsysurl, $options = []);
                $id = $_SESSION['id'];
                suolue($saveDir, $slurl);
                $this->upload->insert($id,$lng,$lat,$dbSaveDir,$slurl,$dbwsysurl,$time,$townCode, $address);
                $this->response('上传成功', 200, [
                    'address' => $address
                ]);
            } else {
                $this->response('定位信息错误', 407);
            }
        } else {
            //图片存在exif信息
            @$exif = exif_read_data($saveDir, 'IFD0', false);
            $image = imagecreatefromstring(file_get_contents($saveDir));
            //获取经度
            @$lng = getGps($exif["GPSLongitude"], $exif['GPSLongitudeRef']);
            //获取纬度
            @$lat = getGps($exif["GPSLatitude"], $exif['GPSLatitudeRef']);
            if (!$lng && !$lat) {
                 $this->response('没有获取到图片的定位信息', 400);
            } else {
                $coordtransform = new CoordTransform();
                $GPS = $coordtransform->wgs84togcj02($lng, $lat);
                $lng = $GPS[0];
                $lat = $GPS[1];
                //获取拍摄时间
                $time = getDateTime($exif);
                //获取定位信息
                $gpsData = getGpsInfo($lng, $lat);
                //获取区号用于保存路径
                $towncode = $gpsData['regeocode']['addressComponent']['towncode'];
                //获取具体地点
                $address = $gpsData['regeocode']['formatted_address'];
                //创建目录，移动文件
                $dir = "photos/" . $_SESSION['qq'] . "/{$towncode}";
                makeDir($dir);
                //图片翻转
                switch ($exif['Orientation']) {
                    case 8:
                        $image = imagerotate($image, 90, 0);
                        break;
                    case 3:
                        $image = imagerotate($image, 180, 0);
                        break;
                    case 6:
                        $image = imagerotate($image, -90, 0);
                        break;
                }
                imagejpeg($image, $dir . '/' . $file['name']);
                imagedestroy($image);
                $url = $dir . "/" . $file['name'];
                $slurl = $dir . '/SL_' . $file['name'];
                $wsysurl = UPURL . $dir . '/WEBP' . $file['name'] . ".webp";//保存图片的文件名
                $dbwsysurl = $dir . '/WEBP' . $file['name'] . ".webp";
                //缩略图
                suolue($url, $slurl);
                WebPConvert::convert($url, $wsysurl, $options = []);
                @unlink($dir . '/' . $file['name']);
                $id = $_SESSION['id'];
                $this->upload->insert($id,$lng,$lat,$dbSaveDir,$slurl,$dbwsysurl,$time,$towncode, $address);
                $this->response('上传成功', 200, [
                    'address' => $address
                ]);
            }
        }
    }
}


