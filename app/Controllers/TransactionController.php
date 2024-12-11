<?php

namespace App\Controllers;

use App\Models\Inventory;
use App\Models\Item;
use App\Models\Transaction;
use App\Resources\TransactionResource;

class TransactionController extends Controller
{
    function all_transactions()
    {
        $transactions = Transaction::all();
        $transactions = TransactionResource::collection_resource($transactions);
        $this->response([
            'transactions' => $transactions
        ], 200);
    }

    function get_transactions_by_type($type)
    {
        if (!in_array($type, ['new', 'return', 'out_for_work', 'add']))
            $this->response(['message' => '404 Not Found.'], 404);

        $data = Transaction::findAll($type, 'type');
        if ($data) {
            $transactions = TransactionResource::collection_resource($data);
            $this->response([
                'data' => $transactions
            ], 200);
        }
        return $this->response(['message' => "No transactions found."]);
    }

    function store($data)
    {
        $this->validate($data, [
            'item_id' => 'required',
            'quantity' => 'required|number',
            'type' => 'required'
        ]);

        if ($data['type'] == 'out_for_work') {

            $this->validate($data, ['receiver' => 'required']);

            if (!Inventory::check_if_stock_available(Inventory::find($data['item_id'], 'item_id'), $data['quantity']))
                $this->response(['message' => 'Required quantity is not available now.'], 422);
        }

        $data['user_id'] = UserController::get_auth_user()['id'];

        $transaction = Transaction::insert($data);

        $this->update_stock($transaction['item_id'], $transaction['quantity'], $transaction['type']);

        $this->response([
            'message' => 'Transaction done successfully.',
            'data' => TransactionResource::resource($transaction)
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
            'user_id' => UserController::get_auth_user()['id'],
            'type' => 'add'
        ]);
        echo $this->response(['message' => 'Stock updated successfully.'], 200);
    }

    function return_item($transaction_id)
    {
        $this->validate(['transaction_id' => $transaction_id], [
            'transaction_id' => 'required',
        ]);

        $transaction = Transaction::update($transaction_id, [
            'returned_at' => date('Y-m-d H:i:s'),
            'type' => 'return'
        ]);
        $this->update_stock(Item::find($transaction['item_id'])['id'], $transaction['quantity'], 'return');

        echo $this->response(['message' => 'Stock updated successfully.'], 200);
    }

    function update_stock($item_id, $quantity,  $type)
    {
        $item = Item::find($item_id);
        $inventory_record = Inventory::find($item_id, 'item_id');

        if ($type == 'out_for_work')
            $quantity = -1 * $quantity;

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

    function get_item_transactions($item_id)
    {
        $transactions = Transaction::findAll($item_id, 'item_id');
        $transactions = TransactionResource::collection_resource($transactions);

        $this->response([
            'data' => $transactions
        ]);
    }
}
