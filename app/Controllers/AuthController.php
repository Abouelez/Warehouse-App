<?php

namespace App\Controllers;

use App\Helpers\TokenHelper;
use App\Models\User;
use App\Models\AccessToken;
use App\Helpers\RequestHelper;

class AuthController extends Controller
{
    use TokenHelper;

    function register($data)
    {
        $this->validate($data, [
            'name' => 'required|string',
            'user_name' => 'required|unique:users,user_name|string|min:3',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);

        unset($data['password_confirmation']);
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        $user = User::insert($data);

        $this->response(['message' => 'Registration successful! Please login to continue.'], 201);
    }

    function login($data)
    {
        $this->validate($data, [
            'user_name' => 'required|string',
            'password' => 'required|min:8'
        ]);

        $user = User::find($data['user_name'], 'user_name') ?? null;

        if ($user && password_verify($data['password'], $user['password'])) {
            $token = self::generate_access_token($user['id']);

            AccessToken::insert([
                'user_id' => $user['id'],
                'token' => $token
            ]);

            $this->response([
                'Message' => 'Logged in Successfully',
                'toke' => $token
            ], 200);
        }
        $this->response(['Message' => 'Invalid credentials.'], 401);
    }

    function logout()
    {
        $token = AccessToken::find(RequestHelper::get_bearer_token(), 'token');
        $access_token = new AccessToken();
        $access_token->delete($token['id']);
        unset($_SESSION['user']);

        $this->response([], 204);
    }
}
