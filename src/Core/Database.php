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
            'CREATE TABLE IF NOT EXISTS companies (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(150) NOT NULL,
                industry VARCHAR(120) NULL,
                size INT NULL,
                timezone VARCHAR(80) NULL,
                address VARCHAR(255) NULL,
                contact_email VARCHAR(150) NULL,
                contact_phone VARCHAR(50) NULL,
                setup_completed TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
        );

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS sections (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL UNIQUE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
        );

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                company_id INT NULL,
                section_id INT NULL,
                name VARCHAR(120) NOT NULL,
                email VARCHAR(120) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                role ENUM("director", "team_leader", "supervisor", "senior", "employee") NOT NULL,
                active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (section_id) REFERENCES sections(id),
                FOREIGN KEY (company_id) REFERENCES companies(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
        );

        try {
            $pdo->exec('ALTER TABLE users ADD COLUMN company_id INT NULL');
            $pdo->exec('ALTER TABLE users ADD CONSTRAINT fk_users_company FOREIGN KEY (company_id) REFERENCES companies(id)');
        } catch (\Throwable $e) {
        }

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS shift_requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                requested_date DATE NOT NULL,
                shift_type ENUM("AM", "MID", "PM", "NIGHT", "DEFAULT") NULL,
                is_day_off TINYINT(1) DEFAULT 0,
                importance ENUM("LOW", "MEDIUM", "HIGH") NOT NULL,
                pattern ENUM("5x2", "4x3", "ROTATING") NULL,
                reason TEXT NOT NULL,
                status ENUM("PENDING", "APPROVED", "DECLINED") DEFAULT "PENDING",
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
                shift_type ENUM("AM", "MID", "PM", "NIGHT", "DEFAULT", "OFF") NOT NULL,
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
                break_type ENUM("REGULAR", "LUNCH", "EMERGENCY") NOT NULL DEFAULT "REGULAR",
                status ENUM("ON_BREAK", "COMPLETED", "DELAYED") DEFAULT "ON_BREAK",
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
        );

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS work_rules (
                id INT AUTO_INCREMENT PRIMARY KEY,
                company_id INT NOT NULL,
                standard_shift_hours INT DEFAULT 8,
                max_consecutive_days INT DEFAULT 6,
                min_hours_between_shifts INT DEFAULT 12,
                overtime_threshold INT DEFAULT 40,
                auto_overtime TINYINT(1) DEFAULT 1,
                enforce_rest TINYINT(1) DEFAULT 1,
                allow_shift_swapping TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (company_id) REFERENCES companies(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
        );

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS scheduling_preferences (
                id INT AUTO_INCREMENT PRIMARY KEY,
                company_id INT NOT NULL,
                default_view ENUM("Weekly", "Bi-Weekly", "Monthly") DEFAULT "Weekly",
                week_start_day VARCHAR(20) DEFAULT "Sunday",
                lead_time_weeks INT DEFAULT 2,
                send_notifications TINYINT(1) DEFAULT 1,
                require_confirmations TINYINT(1) DEFAULT 1,
                ai_scheduling TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (company_id) REFERENCES companies(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
        );
    }

    private static function seed(PDO $pdo): void
    {
        $count = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        if ($count > 0) {
            return;
        }

        $pdo->exec(
            'INSERT INTO companies (name, industry, size, timezone, address, contact_email, contact_phone, setup_completed)
             VALUES ("Acme Corporation", "Healthcare", 50, "Eastern Time (ET)", "123 Main Street, New York, NY 10001", "admin@company.com", "+1 (555) 123-4567", 1)'
        );
        $companyId = (int) $pdo->lastInsertId();

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
                'company_id' => $companyId,
            ],
            [
                'name' => 'Taylor Leader',
                'email' => 'leader@app.test',
                'password' => 'password',
                'role' => 'team_leader',
                'section_id' => $sectionIds['App After-Sales'],
                'company_id' => $companyId,
            ],
            [
                'name' => 'Sam Supervisor',
                'email' => 'supervisor@agent.test',
                'password' => 'password',
                'role' => 'supervisor',
                'section_id' => $sectionIds['Agent After-Sales'],
                'company_id' => $companyId,
            ],
            [
                'name' => 'Sydney Senior',
                'email' => 'senior@app.test',
                'password' => 'password',
                'role' => 'senior',
                'section_id' => $sectionIds['App After-Sales'],
                'company_id' => $companyId,
            ],
            [
                'name' => 'Evan Employee',
                'email' => 'employee@agent.test',
                'password' => 'password',
                'role' => 'employee',
                'section_id' => $sectionIds['Agent After-Sales'],
                'company_id' => $companyId,
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@shift.test',
                'password' => 'password',
                'role' => 'employee',
                'section_id' => $sectionIds['Agent After-Sales'],
                'company_id' => $companyId,
            ],
            [
                'name' => 'Michael Chen',
                'email' => 'michael.chen@shift.test',
                'password' => 'password',
                'role' => 'employee',
                'section_id' => $sectionIds['App After-Sales'],
                'company_id' => $companyId,
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@shift.test',
                'password' => 'password',
                'role' => 'employee',
                'section_id' => $sectionIds['App After-Sales'],
                'company_id' => $companyId,
            ],
            [
                'name' => 'James Wilson',
                'email' => 'james.wilson@shift.test',
                'password' => 'password',
                'role' => 'employee',
                'section_id' => $sectionIds['Agent After-Sales'],
                'company_id' => $companyId,
            ],
            [
                'name' => 'Lisa Anderson',
                'email' => 'lisa.anderson@shift.test',
                'password' => 'password',
                'role' => 'employee',
                'section_id' => $sectionIds['Agent After-Sales'],
                'company_id' => $companyId,
            ],
        ];

        $insertUser = $pdo->prepare(
            'INSERT INTO users (company_id, section_id, name, email, password_hash, role, active)
             VALUES (:company_id, :section_id, :name, :email, :password_hash, :role, 1)'
        );

        foreach ($users as $user) {
            $insertUser->execute([
                'company_id' => $user['company_id'],
                'section_id' => $user['section_id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'password_hash' => password_hash($user['password'], PASSWORD_DEFAULT),
                'role' => $user['role'],
            ]);
        }

        $pdo->prepare(
            'INSERT INTO work_rules (company_id, standard_shift_hours, max_consecutive_days, min_hours_between_shifts, overtime_threshold, auto_overtime, enforce_rest, allow_shift_swapping)
             VALUES (:company_id, 8, 6, 12, 40, 1, 1, 1)'
        )->execute(['company_id' => $companyId]);

        $pdo->prepare(
            'INSERT INTO scheduling_preferences (company_id, default_view, week_start_day, lead_time_weeks, send_notifications, require_confirmations, ai_scheduling)
             VALUES (:company_id, "Weekly", "Sunday", 2, 1, 1, 0)'
        )->execute(['company_id' => $companyId]);

        $userIds = $pdo->query('SELECT id FROM users WHERE role = "employee"')->fetchAll();
        $today = new \DateTimeImmutable('today');
        $weekStart = $today->modify('monday this week')->format('Y-m-d');
        $creatorId = (int) $pdo->query('SELECT id FROM users WHERE role = "team_leader" LIMIT 1')->fetchColumn();

        $pdo->prepare(
            'INSERT INTO schedules (section_id, week_start, created_by, status)
             VALUES (:section_id, :week_start, :created_by, "Published")'
        )->execute([
            'section_id' => $sectionIds['App After-Sales'],
            'week_start' => $weekStart,
            'created_by' => $creatorId,
        ]);
        $scheduleId = (int) $pdo->lastInsertId();

        $insertAssignment = $pdo->prepare(
            'INSERT INTO schedule_assignments (schedule_id, user_id, shift_date, shift_type)
             VALUES (:schedule_id, :user_id, :shift_date, :shift_type)'
        );

        foreach ($userIds as $index => $userRow) {
            $userId = (int) $userRow['id'];
            $date = $today->modify('monday this week');
            for ($i = 0; $i < 5; $i++) {
                $shiftType = match ($i % 3) {
                    0 => 'AM',
                    1 => 'PM',
                    default => 'MID',
                };
                $insertAssignment->execute([
                    'schedule_id' => $scheduleId,
                    'user_id' => $userId,
                    'shift_date' => $date->format('Y-m-d'),
                    'shift_type' => $shiftType,
                ]);
                $date = $date->modify('+1 day');
            }
        }

        $pdo->prepare(
            'INSERT INTO shift_requests (user_id, requested_date, shift_type, is_day_off, importance, pattern, reason, status)
             VALUES (:user_id, :requested_date, :shift_type, :is_day_off, :importance, :pattern, :reason, :status)'
        )->execute([
            'user_id' => (int) $pdo->query('SELECT id FROM users WHERE email = "sarah.johnson@shift.test"')->fetchColumn(),
            'requested_date' => $today->modify('+2 days')->format('Y-m-d'),
            'shift_type' => 'PM',
            'is_day_off' => 0,
            'importance' => 'HIGH',
            'pattern' => '5x2',
            'reason' => 'Medical appointment requiring a late shift.',
            'status' => 'PENDING',
        ]);

        $pdo->prepare(
            'INSERT INTO shift_requests (user_id, requested_date, shift_type, is_day_off, importance, pattern, reason, status)
             VALUES (:user_id, :requested_date, :shift_type, :is_day_off, :importance, :pattern, :reason, :status)'
        )->execute([
            'user_id' => (int) $pdo->query('SELECT id FROM users WHERE email = "michael.chen@shift.test"')->fetchColumn(),
            'requested_date' => $today->modify('+3 days')->format('Y-m-d'),
            'shift_type' => 'AM',
            'is_day_off' => 0,
            'importance' => 'MEDIUM',
            'pattern' => '4x3',
            'reason' => 'Need morning shift for training session.',
            'status' => 'PENDING',
        ]);

        $pdo->prepare(
            'INSERT INTO breaks (user_id, shift_date, break_start, break_end, delay_minutes, break_type, status)
             VALUES (:user_id, :shift_date, :break_start, :break_end, :delay_minutes, :break_type, :status)'
        )->execute([
            'user_id' => (int) $pdo->query('SELECT id FROM users WHERE email = "emily.davis@shift.test"')->fetchColumn(),
            'shift_date' => $today->format('Y-m-d'),
            'break_start' => $today->setTime(12, 0)->format('Y-m-d H:i:s'),
            'break_end' => $today->setTime(12, 30)->format('Y-m-d H:i:s'),
            'delay_minutes' => 0,
            'break_type' => 'LUNCH',
            'status' => 'COMPLETED',
        ]);
    }
}
