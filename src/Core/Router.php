<?php

declare(strict_types=1);

namespace App\Core;

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\RequestController;
use App\Controllers\ScheduleController;

final class Router
{
    private array $routes = [];

    public function __construct(private Request $request, private Response $response)
    {
    }

    public function registerRoutes(): void
    {
        $this->routes = [
            'GET' => [
                '/' => [DashboardController::class, 'home'],
                '/login' => [AuthController::class, 'showLogin'],
                '/dashboard' => [DashboardController::class, 'dashboard'],
                '/requests' => [RequestController::class, 'index'],
                '/schedule' => [ScheduleController::class, 'index'],
            ],
            'POST' => [
                '/login' => [AuthController::class, 'login'],
                '/logout' => [AuthController::class, 'logout'],
            ],
        ];
    }

    public function dispatch(): void
    {
        $method = $this->request->method;
        $path = rtrim($this->request->path, '/') ?: '/';

        if (!isset($this->routes[$method][$path])) {
            http_response_code(404);
            echo 'Page not found.';
            return;
        }

        [$controllerClass, $action] = $this->routes[$method][$path];
        $controller = new $controllerClass($this->request, $this->response);
        $controller->$action();
    }
}
