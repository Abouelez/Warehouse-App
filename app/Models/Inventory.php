<?php

namespace App\Models;


class Inventory extends Model
{

    public $table = "inventory";
    public $attributes = ['item_id', 'available_stock'];
    public function __construct()
    {
        parent::__construct();
    }
}
