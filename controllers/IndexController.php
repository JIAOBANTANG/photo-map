<?php
namespace controllers;
use models\User;
use models\File;
class IndexController{
    public function index(){
        $info = $_COOKIE["map-info"];
        if($info){
            $info = explode('-',$info);
            $name = encryption($info[0],1);
            $password = encryption($info[1],1);
            $user = new User;
            $userinfo = $user-> CookiegetUserInfo($name,$password);
            if($userinfo){
                $_SESSION['id']=$userinfo['u_id'];
                $_SESSION['name']=$userinfo['u_name'];
                $_SESSION['avatar']=$userinfo['u_avatar'];
                $file = new File;
            $data = $file->getFiles($_SESSION['id']);
            // dd($data);
            $rows = count($data);
            foreach($data as $v=>$k){
                $data[$v]['lat']=floatval($data[$v]['lat']);
                $data[$v]['lng']=floatval($data[$v]['lng']);
                $data[$v]['thumbnail']= config('domain').$data[$v]['thumbnail'];
            }
            // dd($rows);
            // $data = json_encode($data);
            $files =[
                "rows"=>$data,
                'time'=>0.03,
                "fields"=>[
                    "lat"=>[
                        "type"=>"number"
                    ],
                    "lng"=>[
                        "type"=>"number"
                    ],
                    "thumbnail"=>[
                        "type"=>"string"
                    ],
                    "url"=>[
                        "type"=>"string"
                    ],
                    "video"=>[
                        "type"=>"string"
                    ],
                    "caption"=>[
                        "type"=>"string"
                    ]
                ],
                "total_rows"=>$rows
            ];
            $files = json_encode($files);
                view('index.index',[
                    'files'=>$files
                ]);
            }else{
                redirect('/user💕login');
            }
        }
        else{
            // if(empty($_SESSIOM['id'])){
                redirect('/user💕login');
            // }else{
            //     view('index.index');
            // }
        }
    }
    public function getfile(){
        if(empty($_SESSIOM['id'])){
            $file = new File;
            $data = $file->getFiles($_SESSION['id']);
            $rows = count($data);
            foreach($data as $v=>$k){
                $data[$v]['lat']=floatval($data[$v]['lat']);
                $data[$v]['lng']=floatval($data[$v]['lng']);
            }
            // dd($rows);
            // $data = json_encode($data);
            $files =[
                "rows"=>$data,
                'time'=>0.03,
                "fields"=>[
                    "lat"=>[
                        "type"=>"number"
                    ],
                    "lng"=>[
                        "type"=>"number"
                    ],
                    "thumbnail"=>[
                        "type"=>"string"
                    ],
                    "url"=>[
                        "type"=>"string"
                    ],
                    "video"=>[
                        "type"=>"string"
                    ],
                    "caption"=>[
                        "type"=>"string"
                    ]
                ],
                "total_rows"=>$rows
            ];
            $files = json_encode($files);
            echo $files;
        }else{
            redirect('/user💕login');
        }
    }
    public function delete(){
        $data = json_decode(file_get_contents("php://input"),1);
        $fid = $data['fid'];
        $file = new File;
        $status = $file->del($fid);
        if($status){
            echo json_encode([
                'code'=>2000,
                'msg'=>'删除成功',
            ]);  
        }else{
            echo json_encode([
                'code'=>4000,
                'msg'=>'文件不存在,或已删除',
            ]);  
        }
    }
}
