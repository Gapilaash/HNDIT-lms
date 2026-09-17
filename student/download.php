<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_student();

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT topics.*, subjects.name AS subject_name FROM topics
                        JOIN subjects ON subjects.id = topics.subject_id
                        WHERE topics.id = ?");
$stmt->execute([$id]);
$topic = $stmt->fetch();

if (!$topic) {
    flash('error', 'Topic not found.');
    redirect('dashboard.php');
}

$filePath = __DIR__ . '/../' . $topic['pdf_path'];

if (!file_exists($filePath)) {
    flash('error', 'The file for this topic is missing. Please contact the admin.');
    redirect('dashboard.php');
}

$ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
$contentTypes = [
    'pdf'  => 'application/pdf',
    'doc'  => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'ppt'  => 'application/vnd.ms-powerpoint',
    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'xls'  => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
];
$contentType = $contentTypes[$ext] ?? 'application/octet-stream';

$downloadName = preg_replace('/[^A-Za-z0-9 _-]/', '', $topic['subject_name'] . ' - ' . $topic['name']) . '.' . $ext;

header('Content-Description: File Transfer');
header('Content-Type: ' . $contentType);
header('Content-Disposition: attachment; filename="' . $downloadName . '"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: must-revalidate');
readfile($filePath);
exit;
