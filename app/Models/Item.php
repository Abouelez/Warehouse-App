<?php

namespace App\Models;


class Item extends Model
{

    public $attributes = ['name', 'description', 'stock', 'uom_id'];
    public function __construct()
    {
        parent::__construct();
    }
}