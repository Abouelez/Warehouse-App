<?php

use App\Controllers\AuthController;
use App\Controllers\Controller;
use App\Controllers\ItemController;
use App\Controllers\TransactionController;
use App\Controllers\UomController;
use App\Models\Transaction;
use Core\Router;


// Router::get('test', [Controller::class, 'test'], 'auth');

//Api routes for UOM "Unit Of Measurement".
Router::get('/all_uom', [UomController::class, 'all'], 'auth');
Router::post('/uom', [UomController::class, 'store'], 'auth');
Router::put('/uom/{id}', [UomController::class, 'update'], 'auth');
Router::get('/find_uom/{id}', [UomController::class, 'find'], 'auth');
Router::delete('/uom/{id}', [UomController::class, 'delete'], 'auth');
//Api routes for Items.
Router::get('items', [ItemController::class, 'all'], 'auth');
Router::get('find_item/{id}', [ItemController::class, 'find'], 'auth');
Router::post('/items', [ItemController::class, 'store'], 'auth');
Router::put('items/{id}', [ItemController::class, 'update'], 'auth');
Router::delete('items/{id}', [ItemController::class, 'delete'], 'auth');
//Authentication routes
Router::post('/login', [AuthController::class, 'login']);
Router::post('/register', [AuthController::class, 'register']);
Router::post('logout', [AuthController::class, 'logout'], 'auth',);
//Transaction routes
Router::post('transactions', [TransactionController::class, 'store'], 'auth');
Router::post('return_item/{id}', [TransactionController::class, 'return_item'], 'auth');
Router::post('add_to_item/{id}', [TransactionController::class, 'add_to_current_item'], 'auth');
Router::get('transactions', [TransactionController::class, 'all_transactions'], 'auth');
Router::get('get_transactions/{type}', [TransactionController::class, 'get_transactions_by_type'], 'auth');
