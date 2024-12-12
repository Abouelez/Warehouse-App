<?php

namespace App\Controllers;

use App\Models\AccessToken;
use App\Models\User;
use Core\Response;

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
        Response::json_response("", [
            'id' => $user['id'],
            'user_name' => $user['user_name'],
            'name' => $user['name']
        ]);
    }

    function update($data)
    {
        $user = self::get_auth_user();
        $user_id = $user['id'];
        $data['id'] = $user_id;
        $this->validate($data, [
            'name' => 'required|string',
            'user_name' => 'required|string|unique:users,user_name'
        ]);
        User::update($user_id, $data);

        Response::json_response("User Data Updated Successfully.");
    }
}
