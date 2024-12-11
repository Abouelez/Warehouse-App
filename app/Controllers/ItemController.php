<?php

namespace App\Controllers;

use App\Models\Inventory;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\Uom;
use App\Resources\ItemResource;

class ItemController extends Controller
{

    function all()
    {
        $items = Item::all();

        $items = ItemResource::collection_resource($items);

        $this->response(['data' => $items], 200);
    }

    function find($key)
    {
        $data = Item::find($key);
        if (!$data)
            echo $this->response(['message' => '404 Not Found.'], 404);

        $item = ItemResource::resource($data);

        $this->response(['data' => $item], 200);
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
            'user_id' => UserController::get_auth_user()['id'],
            'date' => date('Y-m-d H:i:s'),
            'description' => 'New Item',
            'type' => 'new'
        ]);

        $this->response([
            'message' => 'Item Created Successfully',
            'data' => ItemResource::resource($item)
        ], 201);
    }

    function update($id, $data)
    {
        if (!Item::find($id)) {
            $this->response(['message' => '404 Not Found.'], 404);
        }
        $data['id'] = $id;
        $this->validate($data, ['name' => 'unique:items,name']);
        //You must sent old data also with request even if it wasn't updated.
        $item = Item::update($id, [
            'name' => $data['name'],
            'description' => $data['description'],
            'uom_id' => $data['uom_id']
        ]);

        $this->response([
            "message" => "Item Updated Successfully",
            'data' => ItemResource::resource($item)
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
