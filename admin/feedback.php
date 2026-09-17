<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_admin();

$stmt = $pdo->query("
    SELECT f.*, u.name AS student_name, u.email AS student_email, s.name AS subject_name, s.year, s.semester
    FROM feedback f
    JOIN users u ON f.user_id = u.id
    LEFT JOIN subjects s ON f.subject_id = s.id
    ORDER BY f.created_at DESC
");
$feedbackList = $stmt->fetchAll();

$base_path = '../';
$page_title = 'Student Feedback';
include __DIR__ . '/../includes/header.php';
?>

<h1>Student Feedback</h1>

<?php if (empty($feedbackList)): ?>
    <div class="card">
        <p class="text-muted">No feedback has been submitted yet.</p>
    </div>
<?php else: ?>
    <?php foreach ($feedbackList as $fb): ?>
        <div class="card">
            <p style="margin:0 0 8px;">
                <strong><?= clean($fb['student_name']) ?></strong>
                <span class="text-muted">(<?= clean($fb['student_email']) ?>)</span>
                &middot;
                <span class="text-muted"><?= clean(date('d M Y, H:i', strtotime($fb['created_at']))) ?></span>
            </p>
            <?php if ($fb['subject_name']): ?>
                <p class="text-muted" style="margin:0 0 10px;">
                    Re: <?= clean(semester_label($fb['year'], $fb['semester'])) ?> — <?= clean($fb['subject_name']) ?>
                </p>
            <?php else: ?>
                <p class="text-muted" style="margin:0 0 10px;">General LMS Feedback</p>
            <?php endif; ?>
            <p style="margin:0;"><?= nl2br(clean($fb['message'])) ?></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
