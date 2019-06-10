<?php
return [
    'db'=>[
        'host'=>'127.0.0.1',
        'dbname'=>'map',
        'user'=>'root',
        'password'=>'123456',
        'charset'=>'utf8'
    ],
    'redis'=>[
        'scheme'=>'tcp',
        'host'=>'127.0.0.1',
        'port'=>6379
    ],
    //高德地图webKey
    'map-key'=>'d2fa68abf1fcdfd76f65dfce6274ff6c',
    //盐
    'encryption_value'=>'map_invitation_code',
    //加盐
    'encryption_key'=>'d441d33a65d31dbf0a8016a85c71a5b3',
    //验证盐
    'encryption_value_code'=>'CVVEbg1dRQhCVBBaXgo9BV8FXQ',
    // DVpCWBBSRwhZWztQXgAH
    'domain'=>'http://photo.treelo.xin/',
    //二级路由小心心  win：'💕' liunx:'%F0%9F%92%95'
    'ext-hart'=>'💕'
];