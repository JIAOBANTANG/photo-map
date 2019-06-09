<?php
namespace controllers;
use models\User;
class UserController{
    public function login(){
        // echo "hahah";
    
        $bgimg = '/images/bgimg/'.mt_rand(1,10).'.jpg';
        view('index.login',[
            'bgimg'=>$bgimg
        ]);
        // $sql = new Base;


    }
    public function register(){
        $bgimg = '/images/bgimg/'.mt_rand(1,10).'.jpg';
        view('index.register',[
            'bgimg'=>$bgimg
        ]);
    }
    public function dologin(){
        $data = json_decode(file_get_contents("php://input"),1);
        if($data['username'] && $data['password']){
            $user = new User;
            $userinfo = $user->getUserInfo($data['username']);
            if($userinfo){
                // var_dump($userinfo);
                // var_dump(encryption($data['password']));
                //     dd($userinfo['u_password']);
                if(encryption($data['password'])==$userinfo['u_password']){
                    $_SESSION['id']=$userinfo['u_id'];
                    $_SESSION['name']=$userinfo['u_name'];
                    $_SESSION['avatar']=$userinfo['u_avatar'];
                    setcookie("map-info",encryption($userinfo['u_name']).'-'.encryption($userinfo['u_password']),time()+2*7*24*3600);
                     echo json_encode([
                        'code'=>2000,
                        'msg'=>'登陆成功',
                    ]);  
                    // $in_data = $data['username'].",/images/avatar/Fruit-".mt_rand(1,10).".png,'".encryption($data['password']);
                    // $sql = "INSERT INTO m_users(u_name,u_avatar,u_password) VALUES(?,?,?)";
                    // $is_id = $user->insert($sql,$in_data);
                    // if($is_id){
                    //     echo json_encode([
                    //         'code'=>2000,
                    //         'msg'=>'注册成功',
                    //     ]);  
                    // }
                    // dd('密码对了');

                }else{
                    echo json_encode([
                        'code'=>4007,
                        'msg'=>'密码错误',
                    ]);  
                }
            
            }else{
                echo json_encode([
                    'code'=>4006,
                    'msg'=>'该用户不存在',
                ]); 
            }
        }else{
            echo json_encode([
                'code'=>4005,
                'msg'=>'非法操作，已记录在册',
            ]); 
        }
    }
    public function doregister(){
        $data = json_decode(file_get_contents("php://input"),1);
        if($data['username'] && $data['password'] && $data['code']){
            $user = new User;
            $is_id = $user->findName($data['username']);
            if($is_id){
                echo json_encode([
                    'code'=>4002,
                    'msg'=>'用户名已存在',
                ]);  
            }else{
                if(encryption($data['code'],1)==config('encryption_value')){
                    $in_data = $data['username'].",/images/avatar/Fruit-".mt_rand(1,10).".png,".encryption($data['password']);
                    $sql = "INSERT INTO m_users(u_name,u_avatar,u_password) VALUES(?,?,?)";
                    $is_id = $user->insert($sql,$in_data);
                    if($is_id){
                        echo json_encode([
                            'code'=>2000,
                            'msg'=>'注册成功',
                        ]);  
                    }
                }else{
                    echo json_encode([
                        'code'=>4003,
                        'msg'=>'邀请码错误或不存在',
                    ]);  
                }
            }
        }else{
            echo json_encode([
                'code'=>4005,
                'msg'=>'非法操作，已记录在册',
            ]); 
        }
       
    }
    public function out(){
        setcookie('map-info', NULL);
        session_destroy();
        redirect('/user💕login');
    }
}
