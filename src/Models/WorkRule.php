<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class WorkRule
{
    public static function findByCompany(int $companyId): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT standard_shift_hours, max_consecutive_days, min_hours_between_shifts,
                    overtime_threshold, auto_overtime, enforce_rest, allow_shift_swapping
             FROM work_rules
             WHERE company_id = :company_id'
        );
        $stmt->execute(['company_id' => $companyId]);
        $rules = $stmt->fetch();
        return $rules ?: null;
    }

    public static function upsert(int $companyId, array $data): void
    {
        $pdo = Database::connection();
        $existing = self::findByCompany($companyId);

        if ($existing) {
            $stmt = $pdo->prepare(
                'UPDATE work_rules
                 SET standard_shift_hours = :standard_shift_hours,
                     max_consecutive_days = :max_consecutive_days,
                     min_hours_between_shifts = :min_hours_between_shifts,
                     overtime_threshold = :overtime_threshold,
                     auto_overtime = :auto_overtime,
                     enforce_rest = :enforce_rest,
                     allow_shift_swapping = :allow_shift_swapping
                 WHERE company_id = :company_id'
            );
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO work_rules (company_id, standard_shift_hours, max_consecutive_days, min_hours_between_shifts, overtime_threshold, auto_overtime, enforce_rest, allow_shift_swapping)
                 VALUES (:company_id, :standard_shift_hours, :max_consecutive_days, :min_hours_between_shifts, :overtime_threshold, :auto_overtime, :enforce_rest, :allow_shift_swapping)'
            );
        }

        $stmt->execute([
            'company_id' => $companyId,
            'standard_shift_hours' => $data['standard_shift_hours'],
            'max_consecutive_days' => $data['max_consecutive_days'],
            'min_hours_between_shifts' => $data['min_hours_between_shifts'],
            'overtime_threshold' => $data['overtime_threshold'],
            'auto_overtime' => $data['auto_overtime'],
            'enforce_rest' => $data['enforce_rest'],
            'allow_shift_swapping' => $data['allow_shift_swapping'],
        ]);
    }
}
