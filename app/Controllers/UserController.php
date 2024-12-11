<?php

namespace App\Controllers;

use App\Models\AccessToken;
use App\Models\User;

class UserController extends Controller
{

    static function get_auth_user()
    {
        $user_id = AccessToken::find(\App\Helpers\RequestHelper::get_bearer_token(), 'token')['user_id'];
        return User::find($user_id);
    }

    function get_user()
    {
        $user = self::get_auth_user();
        $this->response([
            'id' => $user['id'],
            'user_name' => $user['user_name'],
            'name' => $user['name']
        ]);
    }

    function update($data)
    {
        $this->validate($data, [
            'name' => 'required|string',
            'user_name' => 'required|string|unique:users,user_name'
        ]);
        $user = self::get_auth_user();
        User::update($user['id'], $data);

        $this->response([
            'message' => 'User data updated successfully.'
        ], 200);
    }
}
