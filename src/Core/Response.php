<?php

declare(strict_types=1);

namespace App\Core;

final class Response
{
    public function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload, JSON_PRETTY_PRINT);
    }

    public function redirect(string $location): void
    {
        header('Location: ' . $location);
        exit;
    }
}
