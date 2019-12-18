<?php
namespace controllers;

use models\User;
use libs\Helper;
use Vectorface\GoogleAuthenticator;

class UserController extends Controller
{
    public function login()
    {
        $bgimg = '/images/bgimg/'.mt_rand(1,10).'.jpg';
        view('index.login',[
            'bgimg'=>$bgimg,
        ]);
    }

    public function register()
    {

        if(!config('reg_switch')){
            return view('close.index');
        }
        $bgimg = '/images/bgimg/'.mt_rand(1,10).'.jpg';
        $ga = new GoogleAuthenticator();
        $secret = $ga->createSecret();
        $qr = $ga->getQRCodeUrl('photo-map', $secret);
        view('index.register',[
            'bgimg'=>$bgimg,
            'qr'=>$qr,
            'secret'=>$secret
        ]);
    }

    public function dologin()
    {
        $data = $this->request();
        if($data['qq'] && $data['password'] && $data['google_auth']){
            $user = new User;
            $user_info = $user->getUserInfo($data['qq'],encryption($data['password']));
            if($user_info){
                $ga = new GoogleAuthenticator();
                $check = $ga->verifyCode($user_info['u_google_auth'], $data['google_auth'], 1);
                if(!$check){
                    return $this->response('google验证码不正确或已失效',401);
                }
                setcookie("map-info",encryption($user_info['u_qq']).'-'.encryption($user_info['u_password']),time()+7*24*3600);
                return $this->response('登录成功',200);
            }else{
                return $this->response('qq号或密码错误',401);
            }
        }else{
            return $this->response('缺少参数',401);
        }
    }

    public function doregister()
    {
        if(!config('reg_switch')){
            return $this->response('很抱歉-地图相册现已关闭注册',500);
        }
        $data = $this->request();
        $ga = new GoogleAuthenticator();
        $check = $ga->verifyCode($data['secret'], $data['google_auth'], 1);
        if(!$check){
           return $this->response('google验证码不正确或已失效',401);
        }
        $user_model = new User();
        $qq = $user_model->find($data['qq']);
        if($qq){
            return $this->response('此用户已存在',401);
        }
        $encryption = config('encryption_value_code');
        if($data['code'] !== $encryption) {
            return $this->response('邀请码错误', 401);
        }
        $qq_info= Helper::getQQInfo($data['qq']);
        $data['name']=$qq_info['name'];
        $data['avatar']=$qq_info['avatar'];
        $data['email']=$qq_info['email'];
        $data['password']=encryption($data['password']);
        $data['love']='love'.encryption(($data['qq']));
        unset($data['google_auth']);
        unset($data['code']);
        $res = $user_model->insert($data);
        if($res){
            return $this->response('注册成功',200);
        }else{
            return $this->response('服务器内部错误',500);
        }
    }

    public function out()
    {
        setcookie('map-info', NULL);
        session_destroy();
        redirect('/user💕login');
    }

    public function bind()
    {
        $data = $this->request();
        $user = new User;
        $my_info = $user->getCodeInfo($data['my_code']);
        $to_info = $user->getCodeInfo($data['to_code']);
        if($my_info && $to_info){
            if(!$my_info['u_love_uid']==0 ||!$to_info['u_love_uid']==0){
                return $this->response('你们两个好像其中一个有关联关系哦',400);
            }
            $time = date('Y-m-d H:i:s');
            $res1 = $user->bindLove($my_info['u_id'],$to_info['u_id'],$data['to_code'],$time);
            if($res1){
                $res2 = $user->bindLove($to_info['u_id'],$my_info['u_id'],$data['my_code'],$time);
                if($res2){
                    return $this->response('关联成功',200);
                }else{
                    return $this->response('数据库好像出问题了',500);
                }
            }else{
                return $this->response('数据库好像出问题了',500);
                
            }
        }else{
            return $this->response('请求数据好像出了问题呢',400);
        }    
    }
}
