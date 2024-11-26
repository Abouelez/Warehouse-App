<?php

namespace App\Models;


class Movements extends Model
{

    public $table = "movements";
    public $attributes = ['item_id', 'stock_change', 'type', 'date'];
    public function __construct()
    {
        parent::__construct();
    }
}
