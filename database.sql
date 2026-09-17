-- ===================================================
-- HNDIT LMS - Database Schema
-- Import this file via phpMyAdmin (or `mysql -u root -p < database.sql`)
-- ===================================================

CREATE DATABASE IF NOT EXISTS hndit_lms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hndit_lms;

-- ---------------------------------------------------
-- Users table (both students and admin share this table, differentiated by "role")
-- ---------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------
-- Subjects table
-- ---------------------------------------------------
CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    year ENUM('1', '2') NOT NULL,
    semester ENUM('1', '2') NOT NULL,
    uploaded_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ---------------------------------------------------
-- Topics table (each topic belongs to a subject and holds one file)
-- ---------------------------------------------------
CREATE TABLE IF NOT EXISTS topics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    pdf_path VARCHAR(255) NOT NULL,
    uploaded_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ---------------------------------------------------
-- Feedback table (subject_id is NULL for general LMS feedback)
-- ---------------------------------------------------
CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject_id INT DEFAULT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL
);

-- ---------------------------------------------------
-- Seed default admin account
-- Email: admin@hndit.lk
-- Password: admin123   (CHANGE THIS after first login / before deployment)
-- ---------------------------------------------------
INSERT INTO users (name, email, password, role)
VALUES ('Administrator', 'admin@hndit.lk', '$2b$12$vHn8gfi/vdScbCMn8ob7fOcO1NBwsEOIF3NDT2sdjr/yoC3YfqXGy', 'admin');
