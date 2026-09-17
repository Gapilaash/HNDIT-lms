<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_admin();

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT topics.*, subjects.year AS subject_year, subjects.semester AS subject_semester
                        FROM topics
                        JOIN subjects ON subjects.id = topics.subject_id
                        WHERE topics.id = ?");
$stmt->execute([$id]);
$topic = $stmt->fetch();

if ($topic) {
    $filePath = __DIR__ . '/../' . $topic['pdf_path'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }
    $stmt = $pdo->prepare("DELETE FROM topics WHERE id = ?");
    $stmt->execute([$id]);
    flash('success', 'Topic deleted.');
    redirect('subjects.php?year=' . $topic['subject_year'] . '&semester=' . $topic['subject_semester']);
} else {
    flash('error', 'Topic not found.');
    redirect('dashboard.php');
}
