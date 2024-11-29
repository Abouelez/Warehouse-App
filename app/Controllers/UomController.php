<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\Uom;

class UomController extends Controller
{
    function create($data)
    {
        $this->validate($data, ['name' => 'required|string']);
        $this->response(Uom::insert($data), 200);
    }
}
