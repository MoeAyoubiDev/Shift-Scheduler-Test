<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class ShiftRequest
{
    public static function forSection(array $user): array
    {
        $pdo = Database::connection();

        $params = [];
        $conditions = [];

        if (in_array($user['role'], ['employee', 'senior'], true)) {
            $conditions[] = 'users.id = :user_id';
            $params['user_id'] = $user['id'];
        } elseif (strtolower($user['section']) !== 'all sections') {
            $conditions[] = 'sections.name = :section';
            $params['section'] = $user['section'];
        }

        $where = $conditions ? ('WHERE ' . implode(' AND ', $conditions)) : '';

        $stmt = $pdo->prepare(
            "SELECT shift_requests.id,
                    users.name AS employee,
                    shift_requests.requested_date AS date,
                    shift_requests.shift_type AS shift,
                    shift_requests.is_day_off AS is_day_off,
                    shift_requests.importance,
                    shift_requests.pattern,
                    shift_requests.status,
                    shift_requests.reason
             FROM shift_requests
             INNER JOIN users ON shift_requests.user_id = users.id
             LEFT JOIN sections ON users.section_id = sections.id
             {$where}
             ORDER BY shift_requests.created_at DESC"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function create(int $userId, array $data): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'INSERT INTO shift_requests (user_id, requested_date, shift_type, is_day_off, importance, pattern, reason)
             VALUES (:user_id, :requested_date, :shift_type, :is_day_off, :importance, :pattern, :reason)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'requested_date' => $data['requested_date'],
            'shift_type' => $data['shift_type'],
            'is_day_off' => $data['is_day_off'],
            'importance' => $data['importance'],
            'pattern' => $data['pattern'],
            'reason' => $data['reason'],
        ]);
    }

    public static function updateStatus(int $requestId, string $status): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE shift_requests SET status = :status WHERE id = :id AND status = "PENDING"');
        $stmt->execute([
            'status' => $status,
            'id' => $requestId,
        ]);
    }

    public static function approvedForWeek(int $sectionId, string $weekStart, string $weekEnd): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT shift_requests.*
             FROM shift_requests
             INNER JOIN users ON shift_requests.user_id = users.id
             WHERE users.section_id = :section_id
               AND shift_requests.status = "APPROVED"
               AND shift_requests.requested_date BETWEEN :week_start AND :week_end'
        );
        $stmt->execute([
            'section_id' => $sectionId,
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
        ]);
        return $stmt->fetchAll();
    }

    public static function hasRequestForDate(int $userId, string $requestedDate): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT 1 FROM shift_requests WHERE user_id = :user_id AND requested_date = :requested_date LIMIT 1'
        );
        $stmt->execute([
            'user_id' => $userId,
            'requested_date' => $requestedDate,
        ]);
        return (bool) $stmt->fetchColumn();
    }
}
