<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class BreakLog
{
    public static function record(int $userId, string $shiftDate, ?string $breakStart, ?string $breakEnd, int $delayMinutes): void
    {
        $pdo = Database::connection();
        $status = self::resolveStatus($delayMinutes);

        $stmt = $pdo->prepare(
            'INSERT INTO breaks (user_id, shift_date, break_start, break_end, delay_minutes, status)
             VALUES (:user_id, :shift_date, :break_start, :break_end, :delay_minutes, :status)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'shift_date' => $shiftDate,
            'break_start' => $breakStart,
            'break_end' => $breakEnd,
            'delay_minutes' => $delayMinutes,
            'status' => $status,
        ]);
    }

    public static function recentForUser(int $userId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT shift_date, break_start, break_end, delay_minutes, status
             FROM breaks
             WHERE user_id = :user_id
             ORDER BY created_at DESC
             LIMIT 5'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    private static function resolveStatus(int $delayMinutes): string
    {
        if ($delayMinutes <= 0) {
            return 'On Time';
        }

        if ($delayMinutes <= 10) {
            return 'Late';
        }

        return 'Missed';
    }
}
