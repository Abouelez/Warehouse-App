<?php

namespace App\Controllers;

use App\Models\Inventory;
use Core\Response;

class InventoryController extends Controller
{


    function store($data)
    {
        $this->validate([$data], [
            'item_id' => 'required|unique',
            'available_stock' => 'required|number'
        ]);

        $record = Inventory::insert($data);
        Response::json_response("Created Successfully.", 201);
    }
}
