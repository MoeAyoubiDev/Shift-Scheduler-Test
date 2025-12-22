<?php

declare(strict_types=1);

namespace App\Models;

final class Metrics
{
    public static function overview(string $section): array
    {
        return [
            'section' => $section,
            'coverage' => 92,
            'pending_requests' => 14,
            'on_time_breaks' => 87,
            'late_breaks' => 6,
            'overtime_risk' => 3,
        ];
    }
}
