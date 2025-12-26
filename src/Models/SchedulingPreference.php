<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class SchedulingPreference
{
    public static function findByCompany(int $companyId): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT default_view, week_start_day, lead_time_weeks, send_notifications, require_confirmations, ai_scheduling
             FROM scheduling_preferences
             WHERE company_id = :company_id'
        );
        $stmt->execute(['company_id' => $companyId]);
        $prefs = $stmt->fetch();
        return $prefs ?: null;
    }

    public static function upsert(int $companyId, array $data): void
    {
        $pdo = Database::connection();
        $existing = self::findByCompany($companyId);

        if ($existing) {
            $stmt = $pdo->prepare(
                'UPDATE scheduling_preferences
                 SET default_view = :default_view,
                     week_start_day = :week_start_day,
                     lead_time_weeks = :lead_time_weeks,
                     send_notifications = :send_notifications,
                     require_confirmations = :require_confirmations,
                     ai_scheduling = :ai_scheduling
                 WHERE company_id = :company_id'
            );
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO scheduling_preferences (company_id, default_view, week_start_day, lead_time_weeks, send_notifications, require_confirmations, ai_scheduling)
                 VALUES (:company_id, :default_view, :week_start_day, :lead_time_weeks, :send_notifications, :require_confirmations, :ai_scheduling)'
            );
        }

        $stmt->execute([
            'company_id' => $companyId,
            'default_view' => $data['default_view'],
            'week_start_day' => $data['week_start_day'],
            'lead_time_weeks' => $data['lead_time_weeks'],
            'send_notifications' => $data['send_notifications'],
            'require_confirmations' => $data['require_confirmations'],
            'ai_scheduling' => $data['ai_scheduling'],
        ]);
    }
}
