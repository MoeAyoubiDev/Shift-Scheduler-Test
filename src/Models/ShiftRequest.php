<?php

declare(strict_types=1);

namespace App\Models;

final class ShiftRequest
{
    public static function forSection(string $section): array
    {
        return [
            [
                'employee' => 'Evan Employee',
                'date' => '2024-12-30',
                'shift' => 'AM',
                'importance' => 'HIGH',
                'pattern' => '5x2',
                'status' => 'Pending',
            ],
            [
                'employee' => 'Pat Agent',
                'date' => '2025-01-02',
                'shift' => 'MID',
                'importance' => 'NORMAL',
                'pattern' => '6x1',
                'status' => 'Approved',
            ],
        ];
    }
}
