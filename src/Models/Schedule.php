<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;

final class Schedule
{
    public static function weekPreview(string $section): array
    {
        $pdo = Database::connection();
        $sectionId = self::resolveSectionId($section);
        if ($sectionId === null) {
            return [];
        }

        $schedule = self::latestSchedule($sectionId);
        if (!$schedule) {
            return [];
        }

        $stmt = $pdo->prepare(
            'SELECT shift_date,
                    SUM(shift_type = "AM") AS am,
                    SUM(shift_type = "MID") AS mid,
                    SUM(shift_type = "PM") AS pm,
                    SUM(shift_type = "NIGHT") AS night,
                    SUM(shift_type = "DEFAULT") AS default_shift
             FROM schedule_assignments
             WHERE schedule_id = :schedule_id
             GROUP BY shift_date
             ORDER BY shift_date'
        );
        $stmt->execute(['schedule_id' => $schedule['id']]);
        $rows = $stmt->fetchAll();

        $preview = [];
        foreach ($rows as $row) {
            $date = new DateTimeImmutable($row['shift_date']);
            $preview[] = [
                'day' => $date->format('l'),
                'am' => (int) $row['am'],
                'mid' => (int) $row['mid'],
                'pm' => (int) $row['pm'],
                'night' => (int) $row['night'],
                'default' => (int) $row['default_shift'],
            ];
        }

        return $preview;
    }

    public static function forSection(string $section): array
    {
        $pdo = Database::connection();
        $sectionId = self::resolveSectionId($section);
        if ($sectionId === null) {
            return [];
        }

        $schedule = self::latestSchedule($sectionId);
        if (!$schedule) {
            return [];
        }

        $stmt = $pdo->prepare(
            'SELECT users.name AS employee,
                    users.role,
                    users.id AS user_id,
                    schedule_assignments.shift_date,
                    schedule_assignments.shift_type
             FROM schedule_assignments
             INNER JOIN users ON schedule_assignments.user_id = users.id
             WHERE schedule_assignments.schedule_id = :schedule_id
             ORDER BY users.name, schedule_assignments.shift_date'
        );
        $stmt->execute(['schedule_id' => $schedule['id']]);

        $rows = $stmt->fetchAll();
        $scheduleRows = [];

        foreach ($rows as $row) {
            $userId = (int) $row['user_id'];
            if (!isset($scheduleRows[$userId])) {
                $scheduleRows[$userId] = [
                    'employee' => $row['employee'],
                    'role' => ucfirst(str_replace('_', ' ', $row['role'])),
                    'pattern' => self::resolvePattern($userId),
                    'week' => [],
                ];
            }
            $date = new DateTimeImmutable($row['shift_date']);
            $scheduleRows[$userId]['week'][$date->format('D')] = $row['shift_type'];
        }

        $orderedDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        foreach ($scheduleRows as &$row) {
            $orderedWeek = [];
            foreach ($orderedDays as $day) {
                $orderedWeek[$day] = $row['week'][$day] ?? 'OFF';
            }
            $row['week'] = $orderedWeek;
        }
        unset($row);

        return array_values($scheduleRows);
    }

    public static function generate(string $section, string $weekStart, int $createdBy): void
    {
        $pdo = Database::connection();
        $sectionId = self::resolveSectionId($section);
        if ($sectionId === null) {
            return;
        }

        $scheduleId = self::findOrCreateSchedule($sectionId, $weekStart, $createdBy);
        $pdo->prepare('DELETE FROM schedule_assignments WHERE schedule_id = :schedule_id')
            ->execute(['schedule_id' => $scheduleId]);

        $users = User::bySection($section);
        $weekStartDate = new DateTimeImmutable($weekStart);
        $weekEnd = $weekStartDate->add(new DateInterval('P6D'))->format('Y-m-d');
        $approvedRequests = ShiftRequest::approvedForWeek($sectionId, $weekStart, $weekEnd);

        $requestsByUser = [];
        foreach ($approvedRequests as $request) {
            $requestsByUser[$request['user_id']][$request['requested_date']] = [
                'shift_type' => $request['shift_type'],
                'is_day_off' => (int) $request['is_day_off'] === 1,
            ];
        }

        $insertAssignment = $pdo->prepare(
            'INSERT INTO schedule_assignments (schedule_id, user_id, shift_date, shift_type)
             VALUES (:schedule_id, :user_id, :shift_date, :shift_type)'
        );

        foreach ($users as $user) {
            $pattern = self::resolvePattern($user['id']);
            $daysOff = match ($pattern) {
                '4x3' => ['Friday', 'Saturday', 'Sunday'],
                default => ['Saturday', 'Sunday'],
            };
            $period = new DatePeriod($weekStartDate, new DateInterval('P1D'), 7);
            foreach ($period as $day) {
                $dayName = $day->format('l');
                $dateString = $day->format('Y-m-d');
                $request = $requestsByUser[$user['id']][$dateString] ?? null;
                $shiftType = $request['shift_type'] ?? 'DEFAULT';

                if (in_array($dayName, $daysOff, true)) {
                    $shiftType = 'OFF';
                }

                if ($request && $request['is_day_off']) {
                    $shiftType = 'OFF';
                }

                $insertAssignment->execute([
                    'schedule_id' => $scheduleId,
                    'user_id' => $user['id'],
                    'shift_date' => $dateString,
                    'shift_type' => $shiftType,
                ]);
            }
        }
    }

    private static function resolvePattern(int $userId): string
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT pattern FROM shift_requests WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 1');
        $stmt->execute(['user_id' => $userId]);
        $pattern = $stmt->fetchColumn();
        return $pattern ?: '5x2';
    }

    private static function resolveSectionId(string $section): ?int
    {
        $pdo = Database::connection();

        if (strtolower($section) === 'all sections') {
            $stmt = $pdo->query('SELECT id FROM sections ORDER BY id LIMIT 1');
            $id = $stmt->fetchColumn();
            return $id ? (int) $id : null;
        }

        $stmt = $pdo->prepare('SELECT id FROM sections WHERE name = :name');
        $stmt->execute(['name' => $section]);
        $id = $stmt->fetchColumn();
        return $id ? (int) $id : null;
    }

    private static function findOrCreateSchedule(int $sectionId, string $weekStart, int $createdBy): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id FROM schedules WHERE section_id = :section_id AND week_start = :week_start LIMIT 1');
        $stmt->execute([
            'section_id' => $sectionId,
            'week_start' => $weekStart,
        ]);
        $existing = $stmt->fetchColumn();
        if ($existing) {
            return (int) $existing;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO schedules (section_id, week_start, created_by, status)
             VALUES (:section_id, :week_start, :created_by, "Draft")'
        );
        $stmt->execute([
            'section_id' => $sectionId,
            'week_start' => $weekStart,
            'created_by' => $createdBy,
        ]);

        return (int) $pdo->lastInsertId();
    }

    private static function latestSchedule(int $sectionId): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT id, week_start, status
             FROM schedules
             WHERE section_id = :section_id
             ORDER BY week_start DESC
             LIMIT 1'
        );
        $stmt->execute(['section_id' => $sectionId]);
        $schedule = $stmt->fetch();
        return $schedule ?: null;
    }
}
