<?php

use App\Controllers\Controller;
use Core\Router;

Router::post('/test', [Controller::class, 'test']);
