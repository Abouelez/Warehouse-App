<?php

namespace App\Resources;

use App\Models\Item;
use App\Models\Transaction;

class TransactionResource extends Resource
{

    function __construct($transaction)
    {
        parent::__construct($transaction);
    }

    static function resource($transaction)
    {

        self::unset_null($transaction);

        $item = Item::find($transaction['item_id']);

        $transaction['item'] = ItemResource::resource($item);

        unset($transaction['item_id']);

        return $transaction;
    }
}
