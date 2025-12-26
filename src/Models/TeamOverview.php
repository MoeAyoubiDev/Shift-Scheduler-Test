<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use DateTimeImmutable;

final class TeamOverview
{
    public static function forLeader(array $user): array
    {
        $pdo = Database::connection();
        $sectionName = $user['section'] ?? 'All Sections';

        $users = User::bySection($sectionName);
        $teamMemberIds = array_map(static fn ($row) => (int) $row['id'], $users);
        $teamMemberCount = count($users);

        $today = (new DateTimeImmutable('today'))->format('Y-m-d');
        $onShiftCount = 0;
        if ($teamMemberIds) {
            $placeholders = implode(',', array_fill(0, count($teamMemberIds), '?'));
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM schedule_assignments
                 WHERE user_id IN ({$placeholders}) AND shift_date = ? AND shift_type != 'OFF'"
            );
            $stmt->execute(array_merge($teamMemberIds, [$today]));
            $onShiftCount = (int) $stmt->fetchColumn();
        }

        $pendingRequests = ShiftRequest::forSection($user);
        $pendingCount = count(array_filter($pendingRequests, static fn ($request) => $request['status'] === 'PENDING'));

        $coverageRate = self::coverageRate($teamMemberIds);

        return [
            'team_members' => $teamMemberCount,
            'on_shift' => $onShiftCount,
            'pending_requests' => $pendingCount,
            'coverage_rate' => $coverageRate,
            'upcoming_shifts' => self::upcomingShifts($teamMemberIds),
            'pending_queue' => array_slice(array_filter($pendingRequests, static fn ($request) => $request['status'] === 'PENDING'), 0, 3),
            'team_statuses' => self::teamStatuses($teamMemberIds),
            'performance' => self::performance($teamMemberIds),
            'alerts' => self::alerts($coverageRate),
        ];
    }

    private static function coverageRate(array $teamMemberIds): int
    {
        if (!$teamMemberIds) {
            return 0;
        }

        $pdo = Database::connection();
        $placeholders = implode(',', array_fill(0, count($teamMemberIds), '?'));
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM schedule_assignments WHERE user_id IN ({$placeholders})"
        );
        $stmt->execute($teamMemberIds);
        $assigned = (int) $stmt->fetchColumn();

        $expected = count($teamMemberIds) * 7;
        if ($expected === 0) {
            return 0;
        }

        return (int) min(100, round(($assigned / $expected) * 100));
    }

    private static function upcomingShifts(array $teamMemberIds): array
    {
        if (!$teamMemberIds) {
            return [];
        }

        $pdo = Database::connection();
        $placeholders = implode(',', array_fill(0, count($teamMemberIds), '?'));
        $stmt = $pdo->prepare(
            "SELECT schedule_assignments.shift_date,
                    schedule_assignments.shift_type,
                    users.name AS employee
             FROM schedule_assignments
             INNER JOIN users ON schedule_assignments.user_id = users.id
             WHERE schedule_assignments.user_id IN ({$placeholders})
               AND schedule_assignments.shift_date >= ?
             ORDER BY schedule_assignments.shift_date ASC
             LIMIT 4"
        );
        $today = (new DateTimeImmutable('today'))->format('Y-m-d');
        $stmt->execute(array_merge($teamMemberIds, [$today]));
        return $stmt->fetchAll();
    }

    private static function teamStatuses(array $teamMemberIds): array
    {
        if (!$teamMemberIds) {
            return [];
        }

        $pdo = Database::connection();
        $placeholders = implode(',', array_fill(0, count($teamMemberIds), '?'));
        $stmt = $pdo->prepare(
            "SELECT users.id, users.name, users.role,
                    MAX(CASE WHEN breaks.status = 'ON_BREAK' THEN 1 ELSE 0 END) AS on_break,
                    MAX(CASE WHEN schedule_assignments.shift_date = ? AND schedule_assignments.shift_type != 'OFF' THEN 1 ELSE 0 END) AS on_shift
             FROM users
             LEFT JOIN breaks ON users.id = breaks.user_id
             LEFT JOIN schedule_assignments ON users.id = schedule_assignments.user_id
             WHERE users.id IN ({$placeholders})
             GROUP BY users.id, users.name, users.role
             ORDER BY users.name"
        );
        $today = (new DateTimeImmutable('today'))->format('Y-m-d');
        $stmt->execute(array_merge([$today], $teamMemberIds));
        $rows = $stmt->fetchAll();

        return array_map(static function (array $row): array {
            $status = 'off-duty';
            if ((int) $row['on_break'] === 1) {
                $status = 'on-break';
            } elseif ((int) $row['on_shift'] === 1) {
                $status = 'on-shift';
            }

            return [
                'name' => $row['name'],
                'role' => $row['role'],
                'status' => $status,
            ];
        }, $rows);
    }

    private static function performance(array $teamMemberIds): array
    {
        if (!$teamMemberIds) {
            return [
                'attendance' => 0,
                'on_time' => 0,
                'coverage' => 0,
            ];
        }

        $pdo = Database::connection();
        $placeholders = implode(',', array_fill(0, count($teamMemberIds), '?'));
        $stmt = $pdo->prepare(
            "SELECT status, COUNT(*) AS total FROM breaks
             WHERE user_id IN ({$placeholders})
             GROUP BY status"
        );
        $stmt->execute($teamMemberIds);
        $rows = $stmt->fetchAll();

        $completed = 0;
        $delayed = 0;
        foreach ($rows as $row) {
            if ($row['status'] === 'COMPLETED') {
                $completed = (int) $row['total'];
            }
            if ($row['status'] === 'DELAYED') {
                $delayed = (int) $row['total'];
            }
        }

        $total = max(1, $completed + $delayed);
        $onTime = (int) round(($completed / $total) * 100);

        return [
            'attendance' => min(100, 85 + (int) round($completed * 2)),
            'on_time' => $onTime,
            'coverage' => self::coverageRate($teamMemberIds),
        ];
    }

    private static function alerts(int $coverageRate): array
    {
        $alerts = [];
        if ($coverageRate < 90) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Coverage Gap',
                'detail' => 'Coverage is below target for this week.',
            ];
        }
        $alerts[] = [
            'type' => 'info',
            'title' => 'Overtime Warning',
            'detail' => 'Monitor overtime hours for high-volume teams.',
        ];

        return $alerts;
    }
}
