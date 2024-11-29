<?php

namespace App\Controllers;

use App\Models\Item;

class ItemController extends Controller
{

    function create($data)
    {
        $item = Item::insert($data);
    }
}
