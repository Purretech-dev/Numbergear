-- ============================================================
--  Number Gear — Database Schema (MySQL / XAMPP / phpMyAdmin)
-- ============================================================
--  How to use:
--    1. Open phpMyAdmin -> SQL tab (or use the mysql CLI).
--    2. Run this whole file. It creates the `number_gear`
--       database and all required tables.
--    3. Make sure config/db.php credentials match (default
--       XAMPP: user "root", empty password).
-- ============================================================

CREATE DATABASE IF NOT EXISTS number_gear
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE number_gear;

-- ------------------------------------------------------------
-- USERS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name             VARCHAR(120)        NOT NULL,
    email            VARCHAR(150)        NOT NULL UNIQUE,
    password         VARCHAR(255)        NOT NULL,
    role             ENUM('learner','instructor','admin') NOT NULL DEFAULT 'learner',
    learning_mode    ENUM('institution','self_paced')     NOT NULL DEFAULT 'self_paced',
    institution_name VARCHAR(150)        NULL,
    instructor_id    INT UNSIGNED        NULL,   -- which instructor this learner is assigned to
    created_at       DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_role (role),
    INDEX idx_institution (institution_name),
    INDEX idx_instructor (instructor_id),
    CONSTRAINT fk_users_instructor FOREIGN KEY (instructor_id)
        REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- PROGRESS  (one row per learner per level)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS progress (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED  NOT NULL,
    level       TINYINT UNSIGNED NOT NULL,     -- 1 .. 7
    score       TINYINT UNSIGNED NOT NULL DEFAULT 0, -- 0 .. 100 (% complete)
    details     TEXT NULL,                      -- optional JSON (e.g. learned numbers list)
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                          ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uniq_user_level (user_id, level),
    CONSTRAINT fk_progress_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE CASCADE,
    INDEX idx_level (level)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Optional: bring in the two accounts that already existed in
-- data/users.json before the switch to MySQL. Their passwords
-- are already bcrypt-hashed, so they can be copied in as-is.
-- Remove or edit this block if you don't need it.
-- ------------------------------------------------------------
-- INSERT INTO users (name, email, password, role, learning_mode, institution_name, created_at) VALUES
-- ('Gear Admin',   'gear.admin@gmail.com', '$2y$10$BtgJhPYmTDH6eBDcLjfEc.XJtUCTmEPsx76mclvzt/NROhfIpZ17i', 'admin', 'self_paced', NULL, '2026-06-18 21:07:16'),
-- ('Paul Kitonyi', 'paul@gmail.com',        '$2y$10$yfXFGBVSFLzmpzhgpiluNu5sXStwtIh/EnDUuyA9ICgd8ZCohbRNe', 'admin', 'self_paced', NULL, '2026-06-19 08:44:40');
