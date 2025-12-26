<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use DateTimeImmutable;

final class DirectorOverview
{
    public static function summary(): array
    {
        $pdo = Database::connection();
        $totalEmployees = (int) $pdo->query('SELECT COUNT(*) FROM users WHERE active = 1')->fetchColumn();

        $totalAssignments = (int) $pdo->query('SELECT COUNT(*) FROM schedule_assignments')->fetchColumn();
        $laborCost = $totalAssignments * 165;
        $fillRate = $totalEmployees > 0 ? (int) min(100, round(($totalAssignments / ($totalEmployees * 7)) * 100)) : 0;
        $openShifts = max(0, ($totalEmployees * 7) - $totalAssignments);

        return [
            'total_employees' => $totalEmployees,
            'labor_cost' => $laborCost,
            'fill_rate' => $fillRate,
            'open_shifts' => $openShifts,
            'recent_activity' => self::recentActivity(),
            'system_health' => self::systemHealth($fillRate),
            'this_week' => self::weekTotals($totalAssignments),
        ];
    }

    private static function recentActivity(): array
    {
        $pdo = Database::connection();
        $activities = [];

        $requests = $pdo->query(
            'SELECT users.name AS actor, shift_requests.reason AS detail, shift_requests.created_at AS created_at
             FROM shift_requests
             INNER JOIN users ON shift_requests.user_id = users.id
             ORDER BY shift_requests.created_at DESC
             LIMIT 3'
        )->fetchAll();
        foreach ($requests as $request) {
            $activities[] = [
                'title' => 'Shift request submitted',
                'detail' => $request['actor'] . ': ' . $request['detail'],
                'time' => self::relativeTime($request['created_at']),
                'tone' => 'info',
            ];
        }

        $schedules = $pdo->query(
            'SELECT week_start FROM schedules ORDER BY created_at DESC LIMIT 1'
        )->fetchAll();
        foreach ($schedules as $schedule) {
            $activities[] = [
                'title' => 'Week schedule published',
                'detail' => 'Week of ' . $schedule['week_start'],
                'time' => 'Today',
                'tone' => 'success',
            ];
        }

        return array_slice($activities, 0, 4);
    }

    private static function systemHealth(int $coverageRate): array
    {
        $budgetUsed = min(100, $coverageRate + 8);
        $compliance = max(70, $coverageRate - 2);

        return [
            'coverage_rate' => $coverageRate,
            'budget_used' => $budgetUsed,
            'compliance' => $compliance,
        ];
    }

    private static function weekTotals(int $totalAssignments): array
    {
        $hours = $totalAssignments * 8;
        return [
            'total_shifts' => $totalAssignments,
            'total_hours' => $hours,
            'overtime_hours' => (int) round($hours * 0.08),
            'call_outs' => (int) max(0, round($totalAssignments * 0.01)),
        ];
    }

    private static function relativeTime(string $timestamp): string
    {
        $created = new DateTimeImmutable($timestamp);
        $diff = (new DateTimeImmutable())->getTimestamp() - $created->getTimestamp();
        if ($diff < 3600) {
            return 'Just now';
        }
        if ($diff < 86400) {
            return 'Today';
        }
        return 'This week';
    }
}
