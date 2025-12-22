<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Metrics
{
    public static function overview(string $section): array
    {
        $pdo = Database::connection();

        $sectionId = null;
        if (strtolower($section) !== 'all sections') {
            $stmt = $pdo->prepare('SELECT id FROM sections WHERE name = :name');
            $stmt->execute(['name' => $section]);
            $sectionId = $stmt->fetchColumn();
        }

        $pendingQuery = 'SELECT COUNT(*) FROM shift_requests';
        $pendingParams = [];
        if ($sectionId) {
            $pendingQuery .= ' INNER JOIN users ON shift_requests.user_id = users.id WHERE users.section_id = :section_id AND shift_requests.status = "Pending"';
            $pendingParams['section_id'] = $sectionId;
        } else {
            $pendingQuery .= ' WHERE shift_requests.status = "Pending"';
        }

        $stmt = $pdo->prepare($pendingQuery);
        $stmt->execute($pendingParams);
        $pending = (int) $stmt->fetchColumn();

        $breakQuery = 'SELECT status, COUNT(*) AS total FROM breaks';
        $breakParams = [];
        if ($sectionId) {
            $breakQuery .= ' INNER JOIN users ON breaks.user_id = users.id WHERE users.section_id = :section_id GROUP BY status';
            $breakParams['section_id'] = $sectionId;
        } else {
            $breakQuery .= ' GROUP BY status';
        }

        $stmt = $pdo->prepare($breakQuery);
        $stmt->execute($breakParams);
        $breaks = $stmt->fetchAll();

        $breakCounts = ['On Time' => 0, 'Late' => 0, 'Missed' => 0];
        foreach ($breaks as $break) {
            $breakCounts[$break['status']] = (int) $break['total'];
        }

        $breakTotal = array_sum($breakCounts) ?: 1;
        $onTime = (int) round(($breakCounts['On Time'] / $breakTotal) * 100);
        $late = (int) round(($breakCounts['Late'] / $breakTotal) * 100);

        $coverage = self::coverageRate($sectionId);
        $overtimeRisk = self::overtimeRisk($sectionId);

        return [
            'section' => $section,
            'coverage' => $coverage,
            'pending_requests' => $pending,
            'on_time_breaks' => $onTime,
            'late_breaks' => $late,
            'overtime_risk' => $overtimeRisk,
        ];
    }

    private static function coverageRate(?int $sectionId): int
    {
        $pdo = Database::connection();

        $query = 'SELECT COUNT(*) FROM schedule_assignments';
        $params = [];
        if ($sectionId) {
            $query .= ' INNER JOIN schedules ON schedule_assignments.schedule_id = schedules.id WHERE schedules.section_id = :section_id';
            $params['section_id'] = $sectionId;
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $assigned = (int) $stmt->fetchColumn();

        $userQuery = 'SELECT COUNT(*) FROM users WHERE active = 1';
        $userParams = [];
        if ($sectionId) {
            $userQuery .= ' AND section_id = :section_id';
            $userParams['section_id'] = $sectionId;
        }

        $stmt = $pdo->prepare($userQuery);
        $stmt->execute($userParams);
        $users = (int) $stmt->fetchColumn();

        if ($users === 0) {
            return 0;
        }

        $expected = $users * 6;
        return (int) min(100, round(($assigned / $expected) * 100));
    }

    private static function overtimeRisk(?int $sectionId): int
    {
        $pdo = Database::connection();
        $query = 'SELECT user_id, COUNT(*) AS total FROM schedule_assignments WHERE shift_type != "OFF"';
        $params = [];
        if ($sectionId) {
            $query .= ' AND schedule_id IN (SELECT id FROM schedules WHERE section_id = :section_id)';
            $params['section_id'] = $sectionId;
        }
        $query .= ' GROUP BY user_id HAVING total > 5';

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return count($stmt->fetchAll());
    }
}
