<?php

namespace App\Helpers;

trait RequestHelper
{
    static function get_authorization_headers()
    {
        $headers = null;

        // Check if the 'Authorization' header is directly available in the $_SERVER global.
        if (isset($_SERVER['Authorization']))
            $headers = trim($_SERVER['Authorization']);
        // Check if 'HTTP_AUTHORIZATION' is available in $_SERVER. 
        // This is common in some configurations like Nginx or FastCGI.
        elseif (isset($_SERVER['HTTP_AUTHORIZATION']))
            $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
        elseif (function_exists('apache_request_headers')) {
            $request_headers = apache_request_headers();
            $request_headers = array_combine(
                array_map('ucwords', array_keys($request_headers)),
                array_values($request_headers)
            );

            if (isset($request_headers['Authorization']))
                $headers = trim($request_headers['Authorization']);
        }

        return $headers;
    }

    static function get_bearer_token()
    {
        $headers = self::get_authorization_headers();
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
}
