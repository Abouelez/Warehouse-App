<?php

use App\Controllers\AuthController;
use App\Controllers\ItemController;
use App\Controllers\TransactionController;
use App\Controllers\UomController;
use App\Controllers\UserController;
use Core\Router;



//Api routes for UOM "Unit Of Measurement".
Router::get('all_uom', [UomController::class, 'all'], 'auth'); //ok
Router::post('uom', [UomController::class, 'store'], 'auth'); //ok
Router::put('uom/{id}', [UomController::class, 'update'], 'auth');
Router::get('find_uom/{id}', [UomController::class, 'find'], 'auth');
Router::delete('uom/{id}', [UomController::class, 'delete'], 'auth');
//Api routes for Items.
Router::get('items', [ItemController::class, 'all'], 'auth'); //ok
Router::get('find_item/{id}', [ItemController::class, 'find'], 'auth'); //ok
Router::post('items', [ItemController::class, 'store'], 'auth'); //ok
Router::put('items/{id}', [ItemController::class, 'update'], 'auth'); //ok
Router::delete('items/{id}', [ItemController::class, 'delete'], 'auth');
//Authentication routes
Router::post('login', [AuthController::class, 'login']); //ok
Router::post('register', [AuthController::class, 'register']); //ok
Router::post('logout', [AuthController::class, 'logout'], 'auth'); //ok
//Transaction routes
Router::post('transactions', [TransactionController::class, 'store'], 'auth'); //ok
Router::post('return_item/{id}', [TransactionController::class, 'return_item'], 'auth'); //ok
Router::post('add_to_item/{id}', [TransactionController::class, 'add_to_current_item'], 'auth'); //ok
Router::get('transactions', [TransactionController::class, 'all_transactions'], 'auth'); //ok
Router::get('get_transactions/{type}', [TransactionController::class, 'get_transactions_by_type'], 'auth'); //ok
Router::get('item_transactions/{id}', [TransactionController::class, 'get_item_transactions'], 'auth');

Router::put('user', [UserController::class, 'update'], 'auth');
Router::get('user', [UserController::class, 'get_user'], 'auth');
    