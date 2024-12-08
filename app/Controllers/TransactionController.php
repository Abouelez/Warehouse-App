<?php

namespace App\Controllers;

use App\Models\Inventory;
use App\Models\Item;
use App\Models\Transaction;

class TransactionController extends Controller
{

    function store($data)
    {
        $this->validate($data, [
            'item_id' => 'required',
            'quantity' => 'required|number',
            'user_id' => 'required',
            'type' => 'required'
        ]);

        if (
            $data['type'] == 'out_for_work' &&
            !Inventory::check_if_stock_available(Inventory::find($data['item_id'], 'item_id'), $data['quantity'])
        )
            echo $this->response(['message' => 'Required quantity is not available now.'], 422);

        $transaction = Transaction::insert($data);

        $this->update_stock($transaction['item_id'], $transaction['quantity'], $transaction['type']);

        echo $this->response([
            'message' => 'Transaction done successfully.',
            'data' => $transaction
        ], 201);
    }

    function add_to_current_item($item_id, $data = [])
    {
        $data['item_id'] = $item_id;
        // var_dump($data);
        // die();
        $this->validate($data, [
            'item_id' => 'required',
            'quantity' => 'required'
        ]);
        $this->store([
            'item_id' => $item_id,
            'quantity' => $data['quantity'],
            'user_id' => '4',
            'type' => 'add'
        ]);
        echo $this->response(['message' => 'Stock updated successfully.'], 200);
    }

    function return_item($transaction_id)
    {
        $this->validate(['transaction_id' => $transaction_id], [
            'transaction_id' => 'required',
        ]);

        $transaction = Transaction::update($transaction_id, ['returned_at' => date('Y-m-d H:i:s')]);
        $this->update_stock(Item::find($transaction['item_id'])['id'], $transaction['quantity'], 'return');

        echo $this->response(['message' => 'Stock updated successfully.'], 200);
    }

    function update_stock($item_id, $quantity,  $type)
    {
        $item = Item::find($item_id);
        $inventory_record = Inventory::find($item_id, 'item_id');

        if ($type == 'out_of_work')
            $quantity = -$quantity;

        if ($type == 'add') {
            Item::update(
                $item_id,
                ['stock' => $item['stock'] + $quantity]
            );
        }

        Inventory::update(
            $inventory_record['id'],
            ['available_stock' => $inventory_record['available_stock'] + $quantity]
        );
    }
}
