<?php

declare(strict_types=1);

namespace App\Core;

class ActionHandler
{
    public function handle($handler): void
    {
        if (is_callable($handler)) {
            $handler();
            return;
        }

        if (is_string($handler) && str_contains($handler, '@')) {
            [$controller, $method] = explode('@', $handler, 2);
            $class = 'App\\Controllers\\' . $controller;

            if (!class_exists($class)) {
                http_response_code(500);
                echo "Controller {$class} not found";
                return;
            }

            $instance = new $class();

            if (!method_exists($instance, $method)) {
                http_response_code(500);
                echo "Method {$method} not found";
                return;
            }

            $instance->{$method}();
            return;
        }

        http_response_code(500);
        echo 'Invalid route handler.';
    }
}
