<?php

namespace App\Models;


class Transaction extends Model
{

    public $attributes = ['item_id', 'quantity', 'user_id', 'receiver', 'date', 'description', 'type'];
    public function __construct()
    {
        parent::__construct();
    }
}
