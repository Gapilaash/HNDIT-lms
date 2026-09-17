-- ===================================================
-- Migration: Subject -> Topics hierarchy
-- Run this ONCE on your existing database (via phpMyAdmin
-- or `mysql -u USER -p DBNAME < migration_add_topics.sql`)
-- ===================================================

-- 1. Create the topics table
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

-- 2. Move any file already attached directly to a subject into a topic
--    called "Overview", so no existing uploads are lost.
INSERT INTO topics (subject_id, name, pdf_path, uploaded_by, created_at)
SELECT id, 'Overview', pdf_path, uploaded_by, created_at
FROM subjects
WHERE pdf_path IS NOT NULL AND pdf_path <> '';

-- 3. Subjects no longer hold a file directly - remove the column.
ALTER TABLE subjects DROP COLUMN pdf_path;
