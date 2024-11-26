<?php

namespace App\Models;

class User extends Model
{

    public $attributes = ['name', 'user_name', 'password'];
    public function __construct()
    {
        parent::__construct();
    }
}
