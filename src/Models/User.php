<?php

declare(strict_types=1);

namespace App\Models;

final class User
{
    private static array $users = [
        [
            'id' => 1,
            'name' => 'Diana Director',
            'email' => 'director@shift.test',
            'password' => 'password',
            'role' => 'director',
            'section' => 'all',
        ],
        [
            'id' => 2,
            'name' => 'Taylor Leader',
            'email' => 'leader@app.test',
            'password' => 'password',
            'role' => 'team_leader',
            'section' => 'App After-Sales',
        ],
        [
            'id' => 3,
            'name' => 'Sam Supervisor',
            'email' => 'supervisor@agent.test',
            'password' => 'password',
            'role' => 'supervisor',
            'section' => 'Agent After-Sales',
        ],
        [
            'id' => 4,
            'name' => 'Sydney Senior',
            'email' => 'senior@app.test',
            'password' => 'password',
            'role' => 'senior',
            'section' => 'App After-Sales',
        ],
        [
            'id' => 5,
            'name' => 'Evan Employee',
            'email' => 'employee@agent.test',
            'password' => 'password',
            'role' => 'employee',
            'section' => 'Agent After-Sales',
        ],
    ];

    public static function attempt(string $email, string $password): ?array
    {
        foreach (self::$users as $user) {
            if ($user['email'] === $email && $user['password'] === $password) {
                return $user;
            }
        }

        return null;
    }
}
