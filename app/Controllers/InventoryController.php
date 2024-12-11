<?php

namespace App\Controllers;

use App\Models\Inventory;

class InventoryController extends Controller
{


    function store($data)
    {
        $this->validate([$data], [
            'item_id' => 'required|unique',
            'available_stock' => 'required|number'
        ]);

        $record = Inventory::insert($data);
        $this->response([
            'Message' => "Record Added Successfully",
        ], 201);
    }
}
