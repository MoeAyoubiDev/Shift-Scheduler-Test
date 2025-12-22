<?php

declare(strict_types=1);

namespace App\Core;

final class Request
{
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly array $query,
        public readonly array $post,
        public readonly array $server,
        public readonly array $session
    ) {
    }

    public static function fromGlobals(): self
    {
        $sessionName = Config::get('SESSION_NAME', 'shift_scheduler_session');
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_name($sessionName);
            session_start();
        }

        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        return new self(
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            $path,
            $_GET,
            $_POST,
            $_SERVER,
            $_SESSION
        );
    }
}
