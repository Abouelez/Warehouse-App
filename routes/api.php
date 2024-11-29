<?php

use App\Controllers\Controller;
use App\Controllers\UomController;
use Core\Router;

Router::put('/test', [Controller::class, 'test']);
Router::post('/uom', [UomController::class, 'create']);
Router::get('/uom', [UomController::class, 'find']);
