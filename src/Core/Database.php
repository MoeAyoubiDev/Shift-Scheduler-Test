<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection !== null) {
            return self::$connection;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            Config::get('DB_HOST'),
            Config::get('DB_PORT'),
            Config::get('DB_NAME')
        );

        $pdo = new PDO($dsn, Config::get('DB_USER'), Config::get('DB_PASSWORD'), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        self::$connection = $pdo;

        self::migrate($pdo);
        self::seed($pdo);

        return self::$connection;
    }

    private static function migrate(PDO $pdo): void
    {
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS sections (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL UNIQUE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
        );

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                section_id INT NULL,
                name VARCHAR(120) NOT NULL,
                email VARCHAR(120) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                role ENUM("director", "team_leader", "supervisor", "senior", "employee") NOT NULL,
                active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (section_id) REFERENCES sections(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
        );

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS shift_requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                requested_date DATE NOT NULL,
                shift_type ENUM("AM", "MID", "PM") NOT NULL,
                importance ENUM("LOW", "NORMAL", "HIGH") NOT NULL,
                pattern ENUM("5x2", "6x1") NOT NULL,
                reason VARCHAR(255) NULL,
                status ENUM("Pending", "Approved", "Declined") DEFAULT "Pending",
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
        );

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS schedules (
                id INT AUTO_INCREMENT PRIMARY KEY,
                section_id INT NOT NULL,
                week_start DATE NOT NULL,
                created_by INT NOT NULL,
                status ENUM("Draft", "Published") DEFAULT "Draft",
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (section_id) REFERENCES sections(id),
                FOREIGN KEY (created_by) REFERENCES users(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
        );

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS schedule_assignments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                schedule_id INT NOT NULL,
                user_id INT NOT NULL,
                shift_date DATE NOT NULL,
                shift_type ENUM("AM", "MID", "PM", "OFF") NOT NULL,
                FOREIGN KEY (schedule_id) REFERENCES schedules(id),
                FOREIGN KEY (user_id) REFERENCES users(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
        );

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS breaks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                shift_date DATE NOT NULL,
                break_start DATETIME NULL,
                break_end DATETIME NULL,
                delay_minutes INT DEFAULT 0,
                status ENUM("On Time", "Late", "Missed") DEFAULT "On Time",
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
        );
    }

    private static function seed(PDO $pdo): void
    {
        $count = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        if ($count > 0) {
            return;
        }

        $sections = ['App After-Sales', 'Agent After-Sales'];
        $sectionIds = [];

        $insertSection = $pdo->prepare('INSERT IGNORE INTO sections (name) VALUES (:name)');
        foreach ($sections as $section) {
            $insertSection->execute(['name' => $section]);
            if ($pdo->lastInsertId()) {
                $sectionIds[$section] = (int) $pdo->lastInsertId();
            }
        }

        if (count($sectionIds) < count($sections)) {
            $stmt = $pdo->query('SELECT id, name FROM sections');
            foreach ($stmt->fetchAll() as $row) {
                $sectionIds[$row['name']] = (int) $row['id'];
            }
        }

        $users = [
            [
                'name' => 'Diana Director',
                'email' => 'director@shift.test',
                'password' => 'password',
                'role' => 'director',
                'section_id' => null,
            ],
            [
                'name' => 'Taylor Leader',
                'email' => 'leader@app.test',
                'password' => 'password',
                'role' => 'team_leader',
                'section_id' => $sectionIds['App After-Sales'],
            ],
            [
                'name' => 'Sam Supervisor',
                'email' => 'supervisor@agent.test',
                'password' => 'password',
                'role' => 'supervisor',
                'section_id' => $sectionIds['Agent After-Sales'],
            ],
            [
                'name' => 'Sydney Senior',
                'email' => 'senior@app.test',
                'password' => 'password',
                'role' => 'senior',
                'section_id' => $sectionIds['App After-Sales'],
            ],
            [
                'name' => 'Evan Employee',
                'email' => 'employee@agent.test',
                'password' => 'password',
                'role' => 'employee',
                'section_id' => $sectionIds['Agent After-Sales'],
            ],
        ];

        $insertUser = $pdo->prepare(
            'INSERT INTO users (section_id, name, email, password_hash, role, active)
             VALUES (:section_id, :name, :email, :password_hash, :role, 1)'
        );

        foreach ($users as $user) {
            $insertUser->execute([
                'section_id' => $user['section_id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'password_hash' => password_hash($user['password'], PASSWORD_DEFAULT),
                'role' => $user['role'],
            ]);
        }
    }
}
