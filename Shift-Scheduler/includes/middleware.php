<?php

declare(strict_types=1);

function with_csrf(callable $handler): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf($token)) {
            http_response_code(403);
            echo 'Invalid CSRF token.';
            exit;
        }
    }

    $handler();
}
