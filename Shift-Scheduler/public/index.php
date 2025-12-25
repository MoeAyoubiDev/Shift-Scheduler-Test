<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = str_replace('App\\', '', $class);
    $path = __DIR__ . '/../app/' . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

use App\Core\Router;

$router = new Router();
$router->get('/', 'AdminController@dashboard');
$router->get('/login', 'AuthController@showLogin');
$router->get('/director', 'DirectorController@dashboard');
$router->get('/employee', 'EmployeeController@dashboard');
$router->get('/senior', 'SeniorController@dashboard');
$router->get('/supervisor', 'SupervisorController@dashboard');
$router->get('/teamleader', 'TeamLeaderController@dashboard');

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
