<?php
namespace libs;

class Helper{
    public static function getQQInfo($qq){
        $url='http://r.qzone.qq.com/fcg-bin/cgi_get_portrait.fcg?g_tk=1518561325&uins=';
        $data=file_get_contents($url.$qq);
        $data=iconv("GB2312","UTF-8",$data);
        $pattern = '/portraitCallBack\((.*)\)/is';
        preg_match($pattern,$data,$result);
        $info['name'] = json_decode($result[1], true)["$qq"][6];
        $info['avatar'] = 'http://q1.qlogo.cn/g?b=qq&nk='.$qq.'&s=640';
        $info['email'] = $qq."@qq.com";
        return($info);
    }
}