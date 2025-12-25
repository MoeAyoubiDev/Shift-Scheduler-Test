-- Shift Scheduler schema and stored procedure stubs

CREATE TABLE sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_id INT NULL,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('director', 'team_leader', 'supervisor', 'senior', 'employee') NOT NULL,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (section_id) REFERENCES sections(id)
);

CREATE TABLE shift_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    requested_date DATE NOT NULL,
    shift_type ENUM('AM', 'MID', 'PM', 'NIGHT', 'DEFAULT') NULL,
    is_day_off TINYINT(1) DEFAULT 0,
    importance ENUM('LOW', 'MEDIUM', 'HIGH') NOT NULL,
    pattern ENUM('5x2', '4x3', 'ROTATING') NULL,
    reason TEXT NOT NULL,
    status ENUM('PENDING', 'APPROVED', 'DECLINED') DEFAULT 'PENDING',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_id INT NOT NULL,
    week_start DATE NOT NULL,
    created_by INT NOT NULL,
    status ENUM('Draft', 'Published') DEFAULT 'Draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (section_id) REFERENCES sections(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE schedule_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    schedule_id INT NOT NULL,
    user_id INT NOT NULL,
    shift_date DATE NOT NULL,
    shift_type ENUM('AM', 'MID', 'PM', 'NIGHT', 'DEFAULT', 'OFF') NOT NULL,
    FOREIGN KEY (schedule_id) REFERENCES schedules(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE breaks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    shift_date DATE NOT NULL,
    break_start DATETIME NULL,
    break_end DATETIME NULL,
    delay_minutes INT DEFAULT 0,
    break_type ENUM('REGULAR', 'LUNCH', 'EMERGENCY') NOT NULL DEFAULT 'REGULAR',
    status ENUM('ON_BREAK', 'COMPLETED', 'DELAYED') DEFAULT 'ON_BREAK',
    FOREIGN KEY (user_id) REFERENCES users(id)
);

DELIMITER $$

CREATE PROCEDURE sp_submit_shift_request(
    IN p_user_id INT,
    IN p_requested_date DATE,
    IN p_shift_type VARCHAR(10),
    IN p_importance VARCHAR(10),
    IN p_pattern VARCHAR(5),
    IN p_is_day_off TINYINT(1),
    IN p_reason TEXT
)
BEGIN
    INSERT INTO shift_requests (user_id, requested_date, shift_type, importance, pattern, is_day_off, reason)
    VALUES (p_user_id, p_requested_date, p_shift_type, p_importance, p_pattern, p_is_day_off, p_reason);
END$$

CREATE PROCEDURE sp_generate_schedule(IN p_section_id INT, IN p_week_start DATE)
BEGIN
    -- TODO: Implement scheduling algorithm based on approved requests and coverage requirements.
END$$

CREATE PROCEDURE sp_record_break(IN p_user_id INT, IN p_shift_date DATE, IN p_break_start DATETIME)
BEGIN
    -- TODO: Implement break tracking logic.
END$$

DELIMITER ;
