<?php
namespace controllers;
use models\Base;
class MakeController{
    public function index(){
        dd(encryption(config('encryption_value')));
    }
}
