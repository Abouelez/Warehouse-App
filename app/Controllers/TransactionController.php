<?php

namespace App\Controllers;

use App\Models\Inventory;
use App\Models\Item;
use App\Models\Transaction;
use App\Resources\TransactionResource;
use Core\Pagination;
use Core\Response;

class TransactionController extends Controller
{
    function all_transactions()
    {
        $transactions = Transaction::all();
        $transactions = TransactionResource::collection_resource($transactions);
        Response::json_response("", Pagination::paginate($transactions, 5));
    }

    function get_transactions_by_type($type)
    {
        if (!in_array($type, ['new', 'return', 'out_for_work', 'add']))
            Response::json_response("404 Not Found.", [], 404);

        $data = Transaction::findAll($type, 'type');
        if ($data) {
            $transactions = TransactionResource::collection_resource($data);
            Response::json_response("", Pagination::paginate($transactions, 5));
        }
        Response::json_response("No Transactions Found.");
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
                Response::json_response('Required Quantity is not Available Now.', [], 422);
        }

        $data['user_id'] = UserController::get_auth_user()['id'];

        $transaction = Transaction::insert($data);

        $this->update_stock($transaction['item_id'], $transaction['quantity'], $transaction['type']);
        Response::json_response('Transaction Done Successfully.', TransactionResource::resource($transaction), 201);
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
        Response::json_response('Stock Updated Successfully.');
    }

    function return_item($transaction_id)
    {
        $this->validate(['transaction_id' => $transaction_id], [
            'transaction_id' => 'required',
        ]);
        $transaction = Transaction::find($transaction_id);
        if (!$transaction || $transaction['type'] != 'out_for_work') {
            Response::json_response("Invalid Transaction!.", [], 422);
        }

        $transaction = Transaction::update($transaction_id, [
            'returned_at' => date('Y-m-d H:i:s'),
            'type' => 'return'
        ]);
        $this->update_stock(Item::find($transaction['item_id'])['id'], $transaction['quantity'], 'return');

        Response::json_response('Stock Updated Successfully.');
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
        Response::json_response("", Pagination::paginate($transactions, 5));
    }
}
