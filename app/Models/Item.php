<?php

namespace App\Models;


class Item extends Model
{

    protected $attributes = ['name', 'description', 'stock', 'uom_id'];
    public function __construct()
    {
        parent::__construct();
    }

    public static function get_available_stock($id)
    {
        return Inventory::find($id, 'item_id')['available_stock'];
    }

    
}
