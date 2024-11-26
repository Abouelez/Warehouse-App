<?php

use App\Controllers\Controller;
use Core\Router;

Router::get('/test', [Controller::class, 'test']);
