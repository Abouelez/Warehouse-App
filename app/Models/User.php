<?php

namespace App\Models;

class User extends Model
{

    protected $attributes = ['name', 'user_name', 'password'];
    public function __construct()
    {
        parent::__construct();
    }
}
