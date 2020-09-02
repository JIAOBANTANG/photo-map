<?php
namespace controllers;

use models\User;

class BaseController
{
    //判断是否登录
    public function verification(){
        $info = $_COOKIE["map-info"];
        if ($info) {
            $info = explode('-', $info);
            $qq = encryption($info[0], 1);
            $password = encryption($info[1], 1);
            $user = new User;
            $userinfo = $user->CookiegetUserInfo($qq, $password);
            if ($userinfo) {
                $_SESSION['id'] = $userinfo['u_id'];
                $_SESSION['name'] = $userinfo['u_name'];
                $_SESSION['avatar'] = $userinfo['u_avatar'];
                $_SESSION['qq'] = $userinfo['u_qq'];
                $_SESSION['love_code'] = $userinfo['u_code'];
                $_SESSION['love_uid'] = $userinfo['u_love_uid'];
            }else{
                redirect('/user💕login');
            }
        }else{
            redirect('/user💕login');
        }
    }
}


