<?php

namespace traits;

trait HttpTrait
{
    /**
     * 响应方法
     * @param $mes
     * @param $code
     * @param $data
     */
    public function response($mes, $code,$data=[])
    {
        echo json_encode([
            'code' => $code,
            'msg' => $mes,
            'data' => $data
        ]);
        die;
    }

    /**
     * 请求获取数据
     * @return mixed
     */
    public function request()
    {
        return json_decode(file_get_contents("php://input"), 1);
    }

}


