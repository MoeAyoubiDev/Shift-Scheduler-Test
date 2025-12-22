<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Request;
use App\Core\Response;
use App\Core\Config;

Config::load(__DIR__ . '/../config');

$request = Request::fromGlobals();
$response = new Response();

$router = new Router($request, $response);
$router->registerRoutes();
$router->dispatch();
