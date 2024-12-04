<?php

namespace App\Models;


class Inventory extends Model
{

    protected $table = "inventory";
    protected $attributes = ['item_id', 'available_stock'];
    public function __construct()
    {
        parent::__construct();
    }
}
