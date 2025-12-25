<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class BaseModel
{
    protected static ?PDO $db = null;
    protected string $table = '';

    protected function db(): PDO
    {
        if (static::$db === null) {
            $config = require __DIR__ . '/../../config/database.php';
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $config['host'],
                $config['database'],
                $config['charset']
            );
            static::$db = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }

        return static::$db;
    }

    public function all(): array
    {
        if ($this->table === '') {
            return [];
        }

        $statement = $this->db()->query("SELECT * FROM {$this->table}");
        return $statement->fetchAll();
    }
}
