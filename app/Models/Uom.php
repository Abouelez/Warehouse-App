<?php

namespace App\Models;

/**
 * Uom stands for Unit of Measurement
 * pcs => for countable items
 * kg => kilo gram
 * etc..
 */
class Uom extends Model
{

    protected  $table = "UOM";
    protected $attributes = ['id', 'name', 'description'];
    public function __construct()
    {
        parent::__construct();
    }
}
