<?php
/**
 * Database configuration (EXAMPLE)
 * -----------------------
 * Copy this file to config.php and fill in your own credentials.
 * LOCAL (XAMPP) default values can go here for local dev.
 *
 * When you deploy to free hosting (e.g. InfinityFree), replace these
 * 4 values with the credentials given in your hosting control panel.
 * Nothing else in the project needs to change.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'your_db_name');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');

// Base URL of the project (used for redirects). Change if your folder name differs.
define('BASE_URL', '');

// Max upload size for subject files (in bytes) - 10 MB
define('MAX_FILE_SIZE', 10 * 1024 * 1024);

// Allowed subject file types: extension => list of acceptable MIME types.
// Covers PDF, Word, PowerPoint and Excel (legacy + modern formats).
define('ALLOWED_FILE_TYPES', [
    'pdf'  => ['application/pdf'],
    'doc'  => ['application/msword'],
    'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
    'ppt'  => ['application/vnd.ms-powerpoint'],
    'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation'],
    'xls'  => ['application/vnd.ms-excel'],
    'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
]);

// Comma-separated "accept" attribute value for the file upload <input>.
define('ALLOWED_FILE_ACCEPT', '.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx');
