<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Company
{
    public static function create(string $name, string $contactEmail): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'INSERT INTO companies (name, contact_email, setup_completed)
             VALUES (:name, :contact_email, 0)'
        );
        $stmt->execute([
            'name' => $name,
            'contact_email' => $contactEmail,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function find(int $companyId): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT id, name, industry, size, timezone, address, contact_email, contact_phone, setup_completed
             FROM companies
             WHERE id = :id'
        );
        $stmt->execute(['id' => $companyId]);
        $company = $stmt->fetch();
        return $company ?: null;
    }

    public static function updateProfile(int $companyId, array $data): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'UPDATE companies
             SET name = :name,
                 industry = :industry,
                 size = :size,
                 timezone = :timezone,
                 address = :address,
                 contact_email = :contact_email,
                 contact_phone = :contact_phone
             WHERE id = :id'
        );
        $stmt->execute([
            'id' => $companyId,
            'name' => $data['name'],
            'industry' => $data['industry'],
            'size' => $data['size'],
            'timezone' => $data['timezone'],
            'address' => $data['address'],
            'contact_email' => $data['contact_email'],
            'contact_phone' => $data['contact_phone'],
        ]);
    }

    public static function markSetupComplete(int $companyId): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE companies SET setup_completed = 1 WHERE id = :id');
        $stmt->execute(['id' => $companyId]);
    }
}
