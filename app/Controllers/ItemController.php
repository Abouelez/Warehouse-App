<?php

namespace App\Controllers;

use App\Models\Inventory;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\Uom;
use App\Resources\ItemResource;
use Core\Pagination;
use Core\Response;

class ItemController extends Controller
{

    function all()
    {
        $items = Item::all();

        $items = ItemResource::collection_resource($items);

        Response::json_response("", Pagination::paginate($items, 5));
    }

    function find($key)
    {
        $data = Item::find($key);
        if (!$data)
            Response::json_response("404 Not Found.", [], 404);

        $item = ItemResource::resource($data);
        Response::json_response("", $item);
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
        Response::json_response("Created Successfully.", ItemResource::resource($item), 201);
    }

    function update($id, $data)
    {
        $item = Item::find($id);
        if (!$item) {
            Response::json_response("404 Not Found.", [], 404);
        }
        $data['id'] = $id;
        $this->validate($data, [
            'name' => 'required|unique:items,name',
            'uom_id' => 'required'
        ]);
        //You must sent old data also with request even if it wasn't updated.
        $updated_item = Item::update($id, [
            'name' => $data['name'],
            'description' => $data['description'] ?? $item['description'],
            'uom_id' => $data['uom_id']
        ]);
        Response::json_response("Updated Successfully.", ItemResource::resource($updated_item));
    }

    function delete($id)
    {
        if (!Item::find($id)) {
            Response::json_response("404 Not Found.", [], 404);
        }
        Item::delete($id);
        Response::json_response("", [], 204);
    }
}
