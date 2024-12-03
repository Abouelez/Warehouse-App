<?php

use App\Controllers\Controller;
use App\Controllers\ItemController;
use App\Controllers\UomController;
use Core\Router;

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
