<?php
namespace controllers;

class MakeController extends Controller
{
    public function index(){
        //生成邀请码
        dd(encryption(config('encryption_value')));
    }
}


