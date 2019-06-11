<?php
namespace libs;
/**
 * 坐标转换
 *
 * 提供了百度坐标（BD09） 、国测局坐标（火星坐标，GCJ02）和地球坐标（WGS84）之间的转换
 *
 * 根据 https://github.com/wandergis/coordtransform 写的 PHP 版本
 *
 * @author fooleap
 * @date 2017-04-26 17:22
 * @url https://github.com/fooleap/coordtransform_php
 *
 */
class CoordTransform {
    private $x_PI = 3.14159265358979324 * 3000.0 / 180.0;
    private $PI = 3.1415926535897932384626;
    private $a = 6378245.0;
    private $ee = 0.00669342162296594323;
    private function transformlat($lng, $lat) {
        $ret = -100.0 + 2.0 * $lng + 3.0 * $lat + 0.2 * $lat * $lat + 0.1 * $lng * $lat + 0.2 * sqrt(abs($lng));
        $ret += (20.0 * sin(6.0 * $lng * $this->PI) + 20.0 * sin(2.0 * $lng * $this->PI)) * 2.0 / 3.0;
        $ret += (20.0 * sin($lat * $this->PI) + 40.0 * sin($lat / 3.0 * $this->PI)) * 2.0 / 3.0;
        $ret += (160.0 * sin($lat / 12.0 * $this->PI) + 320 * sin($lat * $this->PI / 30.0)) * 2.0 / 3.0;
        return $ret;
    }
    private function transformlng($lng, $lat) {
        $ret = 300.0 + $lng + 2.0 * $lat + 0.1 * $lng * $lng + 0.1 * $lng * $lat + 0.1 * sqrt(abs($lng));
        $ret += (20.0 * sin(6.0 * $lng * $this->PI) + 20.0 * sin(2.0 * $lng * $this->PI)) * 2.0 / 3.0;
        $ret += (20.0 * sin($lng * $this->PI) + 40.0 * sin($lng / 3.0 * $this->PI)) * 2.0 / 3.0;
        $ret += (150.0 * sin($lng / 12.0 * $this->PI) + 300.0 * sin($lng / 30.0 * $this->PI)) * 2.0 / 3.0;
        return $ret;
    }
    /**
     * 判断是否在国内，不在国内则不做偏移
     * @param $lng
     * @param $lat
     * @return boolean
     */
    private function out_of_china($lng, $lat) {
        return ($lng < 72.004 || $lng > 137.8347) || (($lat < 0.8293 || $lat > 55.8271) || false);
    }
    /**
     * 百度坐标系 (BD-09) 与 火星坐标系 (GCJ-02)的转换
     * @param $lng
     * @param $lat
     * @return array
     */
    public function bd09togcj02($lng, $lat) {
        $lng = +$lng;
        $lat = +$lat;
        $x = $lng - 0.0065;
        $y = $lat - 0.006;
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $this->x_PI);
        $theta = atan2($y, $x) - 0.000003 * cos($x * $this->x_PI);
        $gg_lng = $z * cos($theta);
        $gg_lat = $z * sin($theta);
        return [$gg_lng, $gg_lat];
    }
    /**
     * 火星坐标系 (GCJ-02) 与百度坐标系 (BD-09) 的转换
     * @param $lng
     * @param $lat
     * @return array
     */
    public function gcj02tobd09($lng, $lat) {
        $lat = +$lat;
        $lng = +$lng;
        $z = sqrt($lng * $lng + $lat * $lat) + 0.00002 * sin($lat * $this->x_PI);
        $theta = atan2($lat, $lng) + 0.000003 * cos($lng * $this->x_PI);
        $bd_lng = $z * cos($theta) + 0.0065;
        $bd_lat = $z * sin($theta) + 0.006;
        return [$bd_lng, $bd_lat];
    }
    /**
     * 地球坐标系 (WGS-84) 与火星坐标系 (GCJ-02) 的转换
     * @param $lng
     * @param $lat
     * @return array
     */
    public function wgs84togcj02($lng, $lat) {
        if ($this->out_of_china($lng, $lat)) {
            return [$lng, $lat];
        }
        else {
            $dlat = $this->transformlat($lng - 105.0, $lat - 35.0);
            $dlng = $this->transformlng($lng - 105.0, $lat - 35.0);
            $radlat = $lat / 180.0 * $this->PI;
            $magic = sin($radlat);
            $magic = 1 - $this->ee * $magic * $magic;
            $sqrtmagic = sqrt($magic);
            $dlat = ($dlat * 180.0) / (($this->a * (1 - $this->ee)) / ($magic * $sqrtmagic) * $this->PI);
            $dlng = ($dlng * 180.0) / ($this->a / $sqrtmagic * cos($radlat) * $this->PI);
            $mglat = $lat + $dlat;
            $mglng = $lng + $dlng;
            return [$mglng, $mglat];
        }
    }
    /**
     * 火星坐标系 (GCJ-02) 与地球坐标系 (WGS-84) 的转换
     * @param $lng
     * @param $lat
     * @return array
     */
    public function gcj02towgs84($lng, $lat) {
        $lat = +$lat;
        $lng = +$lng;
        if ($this->out_of_china($lng, $lat)) {
            return [$lng, $lat];
        } else {
            $dlat = $this->transformlat($lng - 105.0, $lat - 35.0);
            $dlng = $this->transformlng($lng - 105.0, $lat - 35.0);
            $radlat = $lat / 180.0 * $this->PI;
            $magic = sin($radlat);
            $magic = 1 - $this->ee * $magic * $magic;
            $sqrtmagic = sqrt($magic);
            $dlat = ($dlat * 180.0) / (($this->a * (1 - $this->ee)) / ($magic * $sqrtmagic) * $this->PI);
            $dlng = ($dlng * 180.0) / ($this->a / $sqrtmagic * cos($radlat) * $this->PI);
            $mglat = $lat + $dlat;
            $mglng = $lng + $dlng;
            return [$lng * 2 - $mglng, $lat * 2 - $mglat];
        }
    }
    
    /**
     * 地球坐标系 (WGS-84) 与百度坐标系 (BD-09) 的转换
     * @param $lng
     * @param $lat
     * @return array
     */
    public function wgs84tobd09($lng, $lat) {
        $coord = $this->wgs84togcj02($lng, $lat);
        return $this->gcj02tobd09($coord[0], $coord[1]);
    }
    /**
     * 百度坐标系 (BD-09) 与地球坐标系 (WGS-84) 的转换
     * @param $lng
     * @param $lat
     * @return array
     */
    public function bd09towgs84($lng, $lat) {
        $coord = $this->bd09togcj02($lng, $lat);
        return $this->gcj02towgs84($coord[0], $coord[1]);
    }
}