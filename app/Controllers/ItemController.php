<?php

namespace App\Controllers;

use App\Models\Inventory;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\Uom;

class ItemController extends Controller
{

    function all()
    {
        $items = Item::all();

        foreach ($items as &$item) {
            $item['uom'] = Uom::find($item['uom_id']); //get uom details
            unset($item['uom_id']);

            $item['available_stock'] = Item::get_available_stock($item['id']); //get available stock
        }

        $this->response(['data' => $items], 200);
    }

    function find($key)
    {
        $data = Item::find($key);
        if (!$data)
            echo $this->response(['message' => '404 Not Found.'], 404);
        $available_stock = Item::get_available_stock($data['id']);
        $data['available_stock'] = $available_stock;

        $data['uom'] = Uom::find($data['uom_id']); //get uom details
        unset($data['uom_id']);

        $this->response(['data' => $data], 200);
    }

    function store($data)
    {
        $this->validate($data, [
            'name' => 'required|string|unique:items,name',
            'stock' => 'required|number',
            'uom_id' => 'required|number'
        ]);
        $item = Item::insert($data);
        //Add record to inventory (Once item added all it's stock is available).
        Inventory::insert([
            'item_id' => $item['id'],
            'available_stock' => $item['stock']
        ]);
        //Also add record to transaction table.
        Transaction::insert([
            'item_id' => $item['id'],
            'quantity' => $item['stock'],
            'user_id' => '4',
            'date' => date('Y-m-d H:i:s'),
            'description' => 'New Item',
            'type' => 'new'
        ]);
        $this->response([
            'Message' => 'Item Created Successfully',
            'data' => $item
        ], 201);
    }

    function update($id, $data)
    {
        if (!Item::find($id)) {
            $this->response(['message' => '404 Not Found.'], 404);
        }

        $this->validate($data, ['name' => 'unique:items,name']);
        $item = Item::update($id, $data);

        $this->response([
            "Message" => "Item Updated Successfully",
            'data' => $item
        ], 200);
    }

    function delete($id)
    {
        if (!Item::find($id)) {
            $this->response(['message' => '404 Not Found.'], 404);
        }
        Item::delete($id);

        $this->response([], 204);
    }
}
