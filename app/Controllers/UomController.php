<?php

namespace App\Controllers;

use App\Models\Item;
use App\Models\Uom;
use App\Resources\ItemResource;
use Core\Pagination;
use Core\Response;

class UomController extends Controller
{
    function all()
    {
        $all = Uom::all();
        //Get all UOM items :)
        foreach ($all as &$uom) {

            $items = ItemResource::collection_resource(Item::findAll($uom['id'], 'uom_id'));

            $uom['items'] = $items ? $items : [];
        }
        Response::json_response("", Pagination::paginate($all, 5));
    }


    function store($data)
    {
        $this->validate($data, ['name' => 'required|string|unique:UOM,name']);
        $uom = Uom::insert($data);
        Response::json_response("Created Successfully.", $uom, 201);
    }

    function update($id, $data)
    {
        $uom = Uom::find($id);
        if (!$uom) {
            Response::json_response("404 Not Found.", 404);
        }
        $data['id'] = $id;
        $this->validate($data, ['name' => 'required|unique:UOM,name']);

        $updated_uom = Uom::update($id, [
            'name' => $data['name'],
            'description' => $data['description'] ?? $uom['description']
        ]);

        Response::json_response("Updated Successfully.", $updated_uom);
    }

    function delete($id)
    {
        if (!Uom::find($id)) {
            Response::json_response("404 Not Found.", [], 404);
        }
        Uom::delete($id);
        Response::json_response("", [], 204);
    }

    function find($key)
    {
        $uom = Uom::find($key);
        if (!$uom) {
            Response::json_response("404 Not Found.", 404);
        }
        $items = ItemResource::collection_resource(Item::findAll($key, 'uom_id'));
        $uom['items'] = $items ? $items : [];
        Response::json_response("", $uom);
    }
}
