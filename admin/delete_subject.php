<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_admin();

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM subjects WHERE id = ?");
$stmt->execute([$id]);
$subject = $stmt->fetch();

if ($subject) {
    // Remove the files for every topic under this subject first
    $topicStmt = $pdo->prepare("SELECT pdf_path FROM topics WHERE subject_id = ?");
    $topicStmt->execute([$id]);
    foreach ($topicStmt->fetchAll() as $topic) {
        $filePath = __DIR__ . '/../' . $topic['pdf_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // Deleting the subject cascades to its topics (FK ON DELETE CASCADE)
    $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = ?");
    $stmt->execute([$id]);
    flash('success', 'Subject and all its topics deleted.');
    redirect('subjects.php?year=' . $subject['year'] . '&semester=' . $subject['semester']);
} else {
    flash('error', 'Subject not found.');
    redirect('dashboard.php');
}
