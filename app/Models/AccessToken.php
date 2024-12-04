<?php

namespace App\Models;

class AccessToken extends Model
{
    protected $attributes = ['user_id', 'token', 'expires_at'];
    protected $table = 'access_tokens';

    public function __construct()
    {
        parent::__construct();
    }
}
