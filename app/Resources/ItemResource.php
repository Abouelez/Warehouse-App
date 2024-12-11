<?php

namespace App\Resources;

use App\Models\Inventory;
use App\Models\Uom;

class ItemResource extends Resource
{


    static function resource($item)
    {

        self::unset_null($item);

        $item['available_stock'] = Inventory::find($item['id'], 'item_id')['available_stock'];
        $item['outside_stock'] = $item['stock'] - $item['available_stock'];
        $item['uom'] = Uom::find($item['uom_id']);

        unset($item['uom_id']);

        return $item;
    }
}
