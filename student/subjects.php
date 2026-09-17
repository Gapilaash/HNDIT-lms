<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_student();

$year = $_GET['year'] ?? '';
$semester = $_GET['semester'] ?? '';

if (!in_array($year, ['1', '2']) || !in_array($semester, ['1', '2'])) {
    flash('error', 'Invalid year/semester selection.');
    redirect('dashboard.php');
}

$stmt = $pdo->prepare("SELECT * FROM subjects WHERE year = ? AND semester = ? ORDER BY id ASC");
$stmt->execute([$year, $semester]);
$subjects = $stmt->fetchAll();

$base_path = '../';
$page_title = semester_label($year, $semester);
include __DIR__ . '/../includes/header.php';
?>

<p><a href="dashboard.php">&larr; Back to categories</a></p>
<h1><?= clean(semester_label($year, $semester)) ?></h1>

<?php if (empty($subjects)): ?>
    <div class="card">
        <p class="text-muted">No subjects have been uploaded for this semester yet. Please check back later.</p>
    </div>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($subjects as $subject): ?>
            <tr>
                <td><?= clean($subject['name']) ?></td>
                <td class="actions">
                    <a class="btn" href="topics.php?subject_id=<?= (int)$subject['id'] ?>">View Topics</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
