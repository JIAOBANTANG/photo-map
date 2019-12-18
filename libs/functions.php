<?php

//视图函数
function view($file,$data=[]){
    //如果传了数据就要把数组展开成变量
    if($data){
        extract($data);
    }
    include(ROOT.'views/'.str_replace('.','/',$file).'.html');
}

//配置信息函数
function config($name){
    static $config = null;
    if($config==null){
        $config = include(ROOT.'config.php');
    }
    return $config[$name];
}

//跳转函数
function redirect($url){
    header('Location:'.$url);
}

function back(){
    redirect($_SERVER['HTTP_REFERER']);
}

//提示信息
function mess($mess){
    $_SESSION['mess']=$mess;
}

//xss处理
function e($content){
    return htmlspecialchars($content);
}

//表单token
function csrf(){
    if(!isset($_SESSION['token'])){
        $token = md5(rand(1,99999).microtime());
        $_SESSION['token']=$token;
    }
}

//获取经纬度
function getGps($exifCoord, $hemi) {
    $degrees = count($exifCoord) > 0 ? gps2Num($exifCoord[0]) : 0;
    $minutes = count($exifCoord) > 1 ? gps2Num($exifCoord[1]) : 0;
    $seconds = count($exifCoord) > 2 ? gps2Num($exifCoord[2]) : 0;
    $flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;
    return $flip * ($degrees + $minutes / 60 + $seconds / 3600);
}

function gps2Num($coordPart) {
    $parts = explode('/', $coordPart);
    if (count($parts) <= 0)
        return 0;
    if (count($parts) == 1)
        return $parts[0];
    return floatval($parts[0]) / floatval($parts[1]);
}

//获取地理信息
function getGpsInfo($lon,$lat){
    $url = "http://restapi.amap.com/v3/geocode/regeo?location={$lon},{$lat}&key=".config('map-key');
    return json_decode(file_get_contents($url),1);
}

//获取拍摄时间
function getDateTime($exif){
    return $exif['DateTimeOriginal'];
}

//获取拍摄设备
function getMake($exif){
    return $exif['Make'].' '.$exif['Model'];
}

//输出函数
function dd($variable){
    var_dump($variable);
    die;
}

//加盐
function encryption($value,$type=0){
    $key=config('encryption_key');
    if($type == 0){//加密
        return str_replace('=','',base64_encode($value^$key));
    }else{
        $value = base64_decode($value);
        return $value ^ $key;
    }
}

//创建目录函数
function makeDir($dir){
    if(!is_dir($dir)){
        mkdir($dir, 0777, true);
    }
}

//图片缩略函数
function suolue($filename,$savePath){
    $per=0.4;
    list($width, $height)=getimagesize($filename);
    $n_w=$width*$per;
    $n_h=$height*$per;
    $new=imagecreatetruecolor($n_w, $n_h);
    $img=imagecreatefromjpeg($filename);
    //copy部分图像并调整
    imagecopyresized($new, $img,0, 0,0, 0,$n_w, $n_h, $width, $height);
    //图像输出新图片、另存为
    imagejpeg($new,$savePath);
    imagedestroy($new);
    imagedestroy($img);
}
