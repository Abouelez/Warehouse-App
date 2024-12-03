<?php

namespace App\Controllers;

use App\Models\Item;
use App\Models\Uom;

class ItemController extends Controller
{

    function all()
    {
        $items = Item::all();

        foreach ($items as &$item) {
            $uom = Uom::find($item['uom_id']);
            $item['uom'] = $uom;
            unset($item['uom_id']);
        }

        $this->response(['data' => $items], 200);
    }

    function find($key)
    {
        $data = Item::find($key);
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
        $this->response([
            'Message' => 'Item Created Successfully',
            'data' => $item
        ], 200);
    }

    function update($id, $data)
    {
        $this->validate($data, ['name' => 'unique:items,name']);
        $item_instance = new Item();
        $item = $item_instance->update($id, $data);
        $this->response([
            "Message" => "Item Updated Successfully",
            'data' => $item
        ], 200);
    }

    function delete($id)
    {
        $item_instance = new Item();
        $item_instance->delete($id);

        $this->response([], 204);
    }
}
