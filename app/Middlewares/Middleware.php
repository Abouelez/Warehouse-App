<?php

namespace App\Middlewares;

use App\Helpers\RequestHelper;
use App\Helpers\TokenHelper;


class Middleware
{
    use RequestHelper;
    use TokenHelper;
    public static function apply_middleware($middleware)
    {
        self::$middleware();
    }

    static function auth()
    {
        $bearer_token = self::get_bearer_token() ?? null;
        if (!$bearer_token) {

            http_response_code(401); // Unauthorized
            header('Content-Type: application/json');

            echo json_encode(['message' => 'Authentication required']);
            exit();
        }
        // $secret_key = $_ENV['JWT_SECRET_KEY'];
        // $token = JWT::decode($bearer_token, new key($secret_key, 'HS256'));
        $token = self::is_valid_token($bearer_token);
        if (!$token) {
            http_response_code(401); // Unauthorized
            header('Content-Type: application/json');

            echo json_encode(['message' => 'Authentication required']);
            exit();
        }
        $_SESSION['user'] = ['id' => $token['user_id']];
    }
}
