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

    static function check_if_stock_available($inventory_record, $stock)
    {
        return $inventory_record['available_stock'] > $stock;
    }
}
