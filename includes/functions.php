<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Escape output safely */
function clean($str) {
    return htmlspecialchars(trim($str ?? ''), ENT_QUOTES, 'UTF-8');
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return is_logged_in() && $_SESSION['role'] === 'admin';
}

function is_student() {
    return is_logged_in() && $_SESSION['role'] === 'student';
}

/** Redirect helper */
function redirect($path) {
    header("Location: $path");
    exit;
}

/** Call at the top of every student-only page */
function require_student() {
    if (!is_student()) {
        redirect('login.php');
    }
}

/** Call at the top of every admin-only page */
function require_admin() {
    if (!is_admin()) {
        redirect('login.php');
    }
}

function flash($key, $msg = null) {
    if ($msg !== null) {
        $_SESSION['flash'][$key] = $msg;
        return;
    }
    if (!empty($_SESSION['flash'][$key])) {
        $out = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $out;
    }
    return null;
}

/**
 * Validate an uploaded subject file against ALLOWED_FILE_TYPES / MAX_FILE_SIZE.
 * Returns null if the file is valid, or an error message string if not.
 *
 * Note: .docx/.pptx/.xlsx files are actually ZIP archives, so on some servers
 * mime_content_type() reports them as 'application/zip' or
 * 'application/octet-stream' instead of their proper Office MIME type. We
 * accept that fallback for those extensions so valid uploads aren't rejected.
 */
function validate_subject_file($fileTmp, $fileName, $fileSize) {
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!array_key_exists($ext, ALLOWED_FILE_TYPES)) {
        return 'Only PDF, Word, PowerPoint, and Excel files are allowed.';
    }

    $mime = mime_content_type($fileTmp);
    $allowedMimes = ALLOWED_FILE_TYPES[$ext];
    $zipBasedFallbacks = ['application/zip', 'application/octet-stream'];
    $isZipBasedExt = in_array($ext, ['docx', 'pptx', 'xlsx'], true);

    if (!in_array($mime, $allowedMimes, true) && !($isZipBasedExt && in_array($mime, $zipBasedFallbacks, true))) {
        return 'Only PDF, Word, PowerPoint, and Excel files are allowed.';
    }

    if ($fileSize > MAX_FILE_SIZE) {
        return 'File is too large. Maximum size is ' . (MAX_FILE_SIZE / 1024 / 1024) . ' MB.';
    }

    return null;
}

function semester_label($year, $semester) {
    $y = $year == '1' ? '1st Year' : '2nd Year';
    $s = $semester == '1' ? '1st Semester' : '2nd Semester';
    return "$y - $s";
}
