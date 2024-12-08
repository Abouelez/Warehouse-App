<?php

namespace App\Models;


class Transaction extends Model
{

    protected $attributes = ['item_id', 'quantity', 'user_id', 'receiver', 'date', 'description', 'type', 'returned_at'];
    public function __construct()
    {
        parent::__construct();
    }
}
