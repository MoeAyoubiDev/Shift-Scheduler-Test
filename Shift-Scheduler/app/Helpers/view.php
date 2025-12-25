<?php

declare(strict_types=1);

function view(string $template, array $data = []): void
{
    $path = base_path('app/Views/' . trim($template, '/'));

    if (!str_ends_with($path, '.php')) {
        $path .= '.php';
    }

    if (!file_exists($path)) {
        http_response_code(500);
        echo "View {$template} not found";
        return;
    }

    extract($data, EXTR_SKIP);
    require $path;
}
