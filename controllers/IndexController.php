<?php

namespace controllers;

use models\User;
use models\File;

class IndexController extends Controller
{
    public function index()
    {
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
                $file = new File;
                $data = $file->getFiles($_SESSION['id']);
                $rows = count($data);
                foreach ($data as $v => $k) {
                    $data[$v]['lat'] = floatval($data[$v]['lat']);
                    $data[$v]['lng'] = floatval($data[$v]['lng']);
                    if($data[$v]['event']==null){
                        $data[$v]['event']='';
                    }
                }
                $files = [
                    "rows" => $data,
                    'time' => 0.03,
                    "fields" => [
                        "lat" => [
                            "type" => "number"
                        ],
                        "lng" => [
                            "type" => "number"
                        ],
                        "thumbnail" => [
                            "type" => "string"
                        ],
                        "nodes" => [
                            "type" => "string"
                        ],
                        "url" => [
                            "type" => "string"
                        ],
                        "video" => [
                            "type" => "string"
                        ],
                        "caption" => [
                            "type" => "string"
                        ],
                        "name" => [
                            "type" => "string"
                        ],
                        "avatar" => [
                            "type" => "string"
                        ]
                    ],
                    "total_rows" => $rows
                ];
                $files = json_encode($files);
                $id = $userinfo['u_id'];
                $love_id = $user->getLoveId($id);
                $love_data='';
                if($love_id){
                   $love_info =  $user->getLoveInfo($love_id);
                   $love_data = $love_info['u_name'].','.$love_info['u_avatar'].','.$love_info['u_love_time'];
//                   dd($love_info);
                }
//                dd($love_id);
                view('index.index', [
                    'files' => $files,
                    'love_data'=>$love_data
                ]);
            } else {
                redirect('/user💕login');
            }
        } else {
            redirect('/user💕login');
        }
    }

    public function delete()
    {
        $data = $this->request();
        $fid = $data['fid'];
        $file = new File;
        $status = $file->del($fid);
        if ($status) {
            return $this->response('删除成功',200);
        }else {
            return $this->response('文件不存在或已删除',400);
        }
    }
    
    public function addmiaoshu()
    {
        $data = $this->request();
        $fid = $data['fid'];
        // $content = e($data['content']);
        $content = $data['content'];
        if ($fid && $content) {
            $file = new File;
            $status = $file->addMiaoShu($fid, $content);
            if ($status) {
                return $this->response('描述修改或添加成功',200);
            } else {
                return $this->response('描述内容未变化或错误',400);
            }
        }
    }
}
