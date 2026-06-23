-- ============================================================
--  Migration: add learner → instructor assignment
-- ============================================================
--  Run this ONLY if you already imported the original
--  database/schema.sql and now want to add instructor
--  assignment without losing your existing data.
--
--  How to use:
--    1. Open phpMyAdmin -> select the `number_gear` database
--       -> SQL tab.
--    2. Paste this whole file and click Go.
--
--  (If you are setting up the database for the very first
--  time, just use database/schema.sql instead — it already
--  includes this column.)
-- ============================================================

USE number_gear;

ALTER TABLE users
    ADD COLUMN instructor_id INT UNSIGNED NULL AFTER institution_name,
    ADD INDEX idx_instructor (instructor_id),
    ADD CONSTRAINT fk_users_instructor FOREIGN KEY (instructor_id)
        REFERENCES users (id) ON DELETE SET NULL;
