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

    public  $table = "UOM";
    public $attributes = ['name', 'description'];
    public function __construct()
    {
        parent::__construct();
    }
}
