<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_student();

$subjectId = $_GET['subject_id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM subjects WHERE id = ?");
$stmt->execute([$subjectId]);
$subject = $stmt->fetch();

if (!$subject) {
    flash('error', 'Subject not found.');
    redirect('dashboard.php');
}

$topicStmt = $pdo->prepare("SELECT * FROM topics WHERE subject_id = ? ORDER BY id ASC");
$topicStmt->execute([$subjectId]);
$topics = $topicStmt->fetchAll();

$base_path = '../';
$page_title = $subject['name'];
include __DIR__ . '/../includes/header.php';
?>

<p><a href="subjects.php?year=<?= urlencode($subject['year']) ?>&semester=<?= urlencode($subject['semester']) ?>">&larr; Back to <?= clean(semester_label($subject['year'], $subject['semester'])) ?></a></p>
<h1><?= clean($subject['name']) ?></h1>

<?php if (empty($topics)): ?>
    <div class="card">
        <p class="text-muted">No topics have been uploaded for this subject yet. Please check back later.</p>
    </div>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Topic</th>
                <th>Added</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($topics as $topic): ?>
            <tr>
                <td><?= clean($topic['name']) ?></td>
                <td><?= clean(date('d M Y', strtotime($topic['created_at']))) ?></td>
                <td class="actions">
                    <a class="btn" href="download.php?id=<?= (int)$topic['id'] ?>">Download File</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
