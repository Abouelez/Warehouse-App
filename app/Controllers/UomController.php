<?php

namespace App\Controllers;

use App\Models\Item;
use App\Models\Uom;

class UomController extends Controller
{
    function all()
    {
        $all = Uom::all();
        //Get all UOM items :)
        foreach ($all as &$uom) {

            $items = Item::find($uom['id'], 'uom_id');
            $uom['items'] = $items ? $items : [];
        }

        $this->response(['data' => $all], 200);
    }


    function store($data)
    {
        $this->validate($data, ['name' => 'required|string|unique:UOM,name']);
        $uom = Uom::insert($data);
        $this->response([
            'message' => 'Inserted Successfully',
            'data' => $uom
        ], 201);
    }

    function update($id, $data)
    {
        if (!Uom::find($id)) {
            $this->response(['message' => '404 Not Found.'], 404);
        }
        $data['id'] = $id;
        $this->validate($data, ['name' => 'unique:UOM,name']);

        $uom = Uom::update($id, $data);

        $this->response([
            'message' => 'Updated Successfully.',
            'data' => $uom
        ], 200);
    }

    function delete($id)
    {
        if (!Uom::find($id)) {
            $this->response(['message' => '404 Not Found.'], 404);
        }
        Uom::delete($id);

        $this->response([], 204);
    }

    function find($key)
    {
        $uom = Uom::find($key);
        $items = Item::find($key, 'uom_id');
        $uom['items'] = $items ? $items : [];
        $this->response(['data' => $uom], 200);
    }
}
