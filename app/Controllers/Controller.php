<?php

namespace App\Controllers;

class Controller
{
    public function response($data, $status_code = 200)
    {
        http_response_code($status_code);
        header('Content-Type: Application/json');
        return json_encode($data);
    }

    function test()
    {
        return $this->response(['message' => 'Work Successfully']);
    }
}
