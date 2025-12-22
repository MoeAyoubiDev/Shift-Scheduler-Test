<?php

declare(strict_types=1);

namespace App\Models;

final class Schedule
{
    public static function weekPreview(string $section): array
    {
        return [
            ['day' => 'Monday', 'am' => 3, 'mid' => 2, 'pm' => 2],
            ['day' => 'Tuesday', 'am' => 4, 'mid' => 2, 'pm' => 2],
            ['day' => 'Wednesday', 'am' => 3, 'mid' => 3, 'pm' => 2],
            ['day' => 'Thursday', 'am' => 3, 'mid' => 2, 'pm' => 3],
            ['day' => 'Friday', 'am' => 4, 'mid' => 3, 'pm' => 2],
            ['day' => 'Saturday', 'am' => 2, 'mid' => 2, 'pm' => 1],
        ];
    }

    public static function forSection(string $section): array
    {
        return [
            [
                'employee' => 'Evan Employee',
                'role' => 'Employee',
                'pattern' => '5x2',
                'week' => [
                    'Mon' => 'AM',
                    'Tue' => 'AM',
                    'Wed' => 'MID',
                    'Thu' => 'PM',
                    'Fri' => 'AM',
                    'Sat' => 'OFF',
                ],
            ],
            [
                'employee' => 'Sydney Senior',
                'role' => 'Senior',
                'pattern' => '6x1',
                'week' => [
                    'Mon' => 'AM',
                    'Tue' => 'AM',
                    'Wed' => 'AM',
                    'Thu' => 'MID',
                    'Fri' => 'PM',
                    'Sat' => 'AM',
                ],
            ],
        ];
    }
}
