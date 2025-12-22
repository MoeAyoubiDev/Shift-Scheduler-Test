<?php

declare(strict_types=1);

namespace App\Core;

final class Config
{
    private static array $values = [];

    public static function load(string $configPath): void
    {
        $envFile = $configPath . '/.env';
        if (is_readable($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (str_starts_with(trim($line), '#')) {
                    continue;
                }
                [$key, $value] = array_map('trim', explode('=', $line, 2));
                self::$values[$key] = $value;
            }
        }

        $defaults = [
            'APP_ENV' => 'local',
            'APP_NAME' => 'Shift Scheduler',
            'APP_URL' => 'http://localhost:8000',
            'DB_HOST' => '127.0.0.1',
            'DB_PORT' => '3306',
            'DB_NAME' => 'shift_scheduler',
            'DB_USER' => 'root',
            'DB_PASSWORD' => '',
            'SESSION_NAME' => 'shift_scheduler_session',
        ];

        self::$values = array_merge($defaults, self::$values);
    }

    public static function get(string $key, ?string $default = null): string
    {
        return self::$values[$key] ?? $default ?? '';
    }
}
