<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class User
{
    public static function emailExists(string $email): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public static function create(
        string $name,
        string $email,
        string $password,
        int $sectionId,
        string $role
    ): array {
        $pdo = Database::connection();

        $stmt = $pdo->prepare(
            'INSERT INTO users (section_id, name, email, password_hash, role, active)
             VALUES (:section_id, :name, :email, :password_hash, :role, 1)'
        );
        $stmt->execute([
            'section_id' => $sectionId,
            'name' => $name,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
        ]);

        $userId = (int) $pdo->lastInsertId();
        return self::fetchById($userId);
    }

    public static function attempt(string $email, string $password): ?array
    {
        $pdo = Database::connection();

        $stmt = $pdo->prepare(
            'SELECT users.id,
                    users.name,
                    users.email,
                    users.password_hash,
                    users.role,
                    sections.name AS section
             FROM users
             LEFT JOIN sections ON users.section_id = sections.id
             WHERE users.email = :email AND users.active = 1
             LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return null;
        }

        return [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'section' => $user['section'] ?? 'All Sections',
        ];
    }

    private static function fetchById(int $userId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT users.id,
                    users.name,
                    users.email,
                    users.role,
                    sections.name AS section
             FROM users
             LEFT JOIN sections ON users.section_id = sections.id
             WHERE users.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();

        if (!$user) {
            return [];
        }

        return [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'section' => $user['section'] ?? 'All Sections',
        ];
    }

    public static function bySection(?string $sectionName): array
    {
        $pdo = Database::connection();

        if ($sectionName === null || strtolower($sectionName) === 'all sections') {
            $stmt = $pdo->query(
                'SELECT users.id, users.name, users.role, users.email, users.section_id, sections.name AS section
                 FROM users
                 LEFT JOIN sections ON users.section_id = sections.id
                 WHERE users.active = 1
                 ORDER BY users.name'
            );
            return $stmt->fetchAll();
        }

        $stmt = $pdo->prepare(
            'SELECT users.id, users.name, users.role, users.email, users.section_id, sections.name AS section
             FROM users
             INNER JOIN sections ON users.section_id = sections.id
             WHERE sections.name = :section AND users.active = 1
             ORDER BY users.name'
        );
        $stmt->execute(['section' => $sectionName]);
        return $stmt->fetchAll();
    }
}
