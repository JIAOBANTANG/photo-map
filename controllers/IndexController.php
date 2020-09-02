<?php
/*
 * 主页控制器
 */

namespace controllers;

use models\User;
use models\File;
use traits\HttpTrait;

class IndexController extends BaseController
{
    use HttpTrait;

    private $user;
    private $file;

    public function __construct()
    {
        $this->file = new File;
        $this->user = new User;
    }

    public function index()
    {
        $this->verification();
        $love_id =  $this->user->getLoveId($_SESSION['id']);
        $data =  $this->file->getFiles($_SESSION['id'],$love_id);
        $rows = count($data);
        foreach ($data as $v => $k) {
            $data[$v]['lat'] = floatval($data[$v]['lat']);
            $data[$v]['lng'] = floatval($data[$v]['lng']);
        }
        $files = photoMap($data,$rows);
        $love_data = '';
        if ($love_id) {
            $love_info = $this->user->getLoveInfo($love_id);
            $love_data = $love_info['u_name'] . ',' . $love_info['u_avatar'] . ',' . $love_info['u_love_time'];
        }
        view('index.index', [
            'files' => $files,
            'love_data' => $love_data
        ]);
    }

    public function delete()
    {
        $data = $this->request();
        $fid = $data['fid'];
        $status = $this->file->del($fid);
        if ($status) {
            $this->response('删除成功', 200);
        } else {
            $this->response('文件不存在或已删除', 400);
        }
    }

    public function addmiaoshu()
    {
        $data = $this->request();
        $fid = $data['fid'];
        $content = $data['content'];
        if ($fid && $content) {
            $status = $this->file->addMiaoShu($fid, e($content));
            if ($status) {
                $this->response('描述修改或添加成功', 200);
            } else {
                $this->response('描述内容未变化或错误', 400);
            }
        }
    }
}
