<?php
namespace controllers;


class Controller{

    //请求返回状态数据
    public function response($mes,$code){
        echo json_encode([
            'code'=>$code,
            'msg'=>$mes
        ]);
        die;
    }

    //请求获取数据
    public function request(){
        return json_decode(file_get_contents("php://input"),1);
    }

}


