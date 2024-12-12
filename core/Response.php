<?php

namespace Core;

class Response
{

    function __construct() {}

    static function json_response($msg = "", $data = [], $status_code = 200, $headers = [], $error = false)
    {
        http_response_code($status_code);
        header('Content-Type: application/json');
        foreach ($headers as $header)
            header($header);

        $response = [];
        if ($msg)
            $response['message'] = $msg;
        if ($data) {
            if ($error)
                $response['errors'] = $data;
            else
                $response['data'] = $data;
        }
        echo json_encode($response);
        exit;
    }
}
