<?php
return [
    'db'=>[
        'host'=>'47.106.222.197',
        'dbname'=>'photo_map',
        'user'=>'photo_map',
        'password'=>'JBT-serve',
        'charset'=>'utf8'
    ],
    'redis'=>[
        'scheme'=>'tcp',
        'host'=>'127.0.0.1',
        'port'=>6379
    ],
    //高德地图webKey
    'map-key'=>'',
    //邀请码明文,自行填入
    'encryption_value'=>'',
    //盐
    'encryption_key'=>'d441d33a65d31dbf0a80a85c71a5b3',
    //邀请码,执行 host/make💕index可生成
    'encryption_value_code'=>'',
    //注册开关
    'reg_switch'=>true
];