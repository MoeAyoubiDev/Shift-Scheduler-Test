<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Section
{
    public static function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query('SELECT id, name FROM sections ORDER BY name');
        return $stmt->fetchAll();
    }
}
