<?php

namespace App\Helpers;

use App\Models\AccessToken;
use Config\Database;
use Firebase\JWT\JWT;
use PDO;

trait TokenHelper
{

    public static function generate_access_token($user_id, $expires_at = null)
    {
        $payload = [
            'iss' =>   'My-App',   //Issuer
            'sub' => $user_id,     //Subject
            'iat' => time(),       //Issued At
            'exp' => $expires_at  //Expires in 
        ];

        return JWT::encode($payload, $_ENV['JWT_SECRET_KEY'], 'HS256');
    }

    public static function generate_refresh_token($user_id)
    {
        $refresh_token = bin2hex(random_bytes(32));
        $stmt = Database::get_instance()->get_connection()->prepare('INSERT INTO refresh_tokens (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at');
        $stmt->execute([
            'user_id' => $user_id,
            'token' => $refresh_token,
            'expires_at' => date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60))
        ]);
    }

    static function is_valid_token($token)
    {
        $result = AccessToken::find($token, 'token');
        return $result;
    }
}
