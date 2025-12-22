<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $template, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $templatePath = __DIR__ . '/../Views/' . $template . '.php';
        if (!file_exists($templatePath)) {
            http_response_code(500);
            echo 'View not found.';
            return;
        }
        require $templatePath;
    }
}
