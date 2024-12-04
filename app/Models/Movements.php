<?php

namespace App\Models;


class Movements extends Model
{

    protected $table = "movements";
    protected $attributes = ['item_id', 'stock_change', 'type', 'date'];
    public function __construct()
    {
        parent::__construct();
    }
}
