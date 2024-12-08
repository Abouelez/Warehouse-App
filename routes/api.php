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
Router::get('/all_uom', [UomController::class, 'all']);
Router::post('/uom', [UomController::class, 'store']);
Router::put('/uom/{id}', [UomController::class, 'update']);
Router::get('/find_uom/{id}', [UomController::class, 'find']);
Router::delete('/uom/{id}', [UomController::class, 'delete']);
//Api routes for Items.
Router::get('items', [ItemController::class, 'all']);
Router::get('find_item/{id}', [ItemController::class, 'find']);
Router::post('/items', [ItemController::class, 'store']);
Router::put('items/{id}', [ItemController::class, 'update']);
Router::delete('items/{id}', [ItemController::class, 'delete']);
//Authentication routes
Router::post('/login', [AuthController::class, 'login']);
Router::post('/register', [AuthController::class, 'register']);
Router::post('logout', [AuthController::class, 'logout'], 'auth');
//Transaction routes
Router::post('transactions', [TransactionController::class, 'store']);
Router::post('return_item/{id}', [TransactionController::class, 'return_item']);
Router::post('add_to_item/{id}', [TransactionController::class, 'add_to_current_item']);
