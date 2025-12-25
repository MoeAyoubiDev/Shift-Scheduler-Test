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

    public static function exists(int $sectionId): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM sections WHERE id = :id');
        $stmt->execute(['id' => $sectionId]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
