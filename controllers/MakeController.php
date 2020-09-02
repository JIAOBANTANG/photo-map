<?php

namespace controllers;

class MakeController
{
    public function index()
    {
        //生成邀请码 请注意关闭
        dd(encryption(config('encryption_value')));
    }
}


