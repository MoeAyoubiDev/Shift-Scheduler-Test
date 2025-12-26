<?php

declare(strict_types=1);

namespace App\Core;

use App\Controllers\AuthController;
use App\Controllers\BreakController;
use App\Controllers\DashboardController;
use App\Controllers\ManagementController;
use App\Controllers\RequestController;
use App\Controllers\ScheduleController;
use App\Controllers\SetupController;

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
                '/register' => [AuthController::class, 'showRegister'],
                '/dashboard' => [DashboardController::class, 'dashboard'],
                '/requests' => [RequestController::class, 'index'],
                '/schedule' => [ScheduleController::class, 'index'],
                '/setup/company' => [SetupController::class, 'company'],
                '/setup/work-rules' => [SetupController::class, 'workRules'],
                '/setup/employees' => [SetupController::class, 'employees'],
                '/setup/preferences' => [SetupController::class, 'preferences'],
                '/setup/review' => [SetupController::class, 'review'],
                '/analytics' => [ManagementController::class, 'analytics'],
                '/time-tracking' => [ManagementController::class, 'timeTracking'],
                '/locations' => [ManagementController::class, 'locations'],
                '/settings' => [ManagementController::class, 'settings'],
            ],
            'POST' => [
                '/login' => [AuthController::class, 'login'],
                '/register' => [AuthController::class, 'register'],
                '/logout' => [AuthController::class, 'logout'],
                '/requests/submit' => [RequestController::class, 'submit'],
                '/requests/update' => [RequestController::class, 'updateStatus'],
                '/schedule/generate' => [ScheduleController::class, 'generate'],
                '/breaks/log' => [BreakController::class, 'log'],
                '/setup/company' => [SetupController::class, 'saveCompany'],
                '/setup/work-rules' => [SetupController::class, 'saveWorkRules'],
                '/setup/employees' => [SetupController::class, 'addEmployee'],
                '/setup/preferences' => [SetupController::class, 'savePreferences'],
                '/setup/complete' => [SetupController::class, 'complete'],
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
