<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_admin();

$year = $_GET['year'] ?? '';
$semester = $_GET['semester'] ?? '';

if (!in_array($year, ['1', '2']) || !in_array($semester, ['1', '2'])) {
    flash('error', 'Invalid year/semester selection.');
    redirect('dashboard.php');
}

$stmt = $pdo->prepare("SELECT * FROM subjects WHERE year = ? AND semester = ? ORDER BY id ASC");
$stmt->execute([$year, $semester]);
$subjects = $stmt->fetchAll();

$topicsBySubject = [];
if (!empty($subjects)) {
    $subjectIds = array_column($subjects, 'id');
    $placeholders = implode(',', array_fill(0, count($subjectIds), '?'));
    $topicStmt = $pdo->prepare("SELECT * FROM topics WHERE subject_id IN ($placeholders) ORDER BY id ASC");
    $topicStmt->execute($subjectIds);
    foreach ($topicStmt->fetchAll() as $topic) {
        $topicsBySubject[$topic['subject_id']][] = $topic;
    }
}

// A small, fixed color palette so each subject gets a distinct accent line.
// Picked by subject id (stable across page loads), not by list position.
$accentColors = ['#2856c4', '#7c3aed', '#0d9488', '#c2410c', '#be185d', '#ca8a04'];

$base_path = '../';
$page_title = semester_label($year, $semester);
include __DIR__ . '/../includes/header.php';
?>

<style>
    /* Scoped to this page only - does not touch assets/style.css */
    .collapsible-toggle { list-style: none; cursor: pointer; }
    .collapsible-toggle::-webkit-details-marker { display: none; }
    .collapsible-toggle::marker { content: ""; }
    .toggle-chevron {
        display: inline-block;
        margin-right: 8px;
        color: var(--muted);
        transition: transform 0.15s ease;
    }
    details[open] > .collapsible-toggle .toggle-chevron { transform: rotate(90deg); }
</style>

<p><a href="dashboard.php">&larr; Back to Manage Subjects</a></p>
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
    <h1><?= clean(semester_label($year, $semester)) ?></h1>
    <div class="actions">
        <a href="add_subject.php" class="btn">+ Add New Subject</a>
        <a href="add_topic.php?year=<?= $year ?>&semester=<?= $semester ?>" class="btn">+ Add Topic</a>
    </div>
</div>

<?php if (empty($subjects)): ?>
    <p class="text-muted" style="margin-top:20px;">No subjects added yet for this semester.</p>
<?php else: ?>
    <?php foreach ($subjects as $subject):
        $topics = $topicsBySubject[$subject['id']] ?? [];
        $accentColor = $accentColors[$subject['id'] % count($accentColors)];
        $topicCount = count($topics);
    ?>
        <div class="card" style="margin-top:14px; border-left:4px solid <?= $accentColor ?>;">
            <details>
                <summary class="collapsible-toggle">
                    <h3 style="margin:0; display:inline;">
                        <span class="toggle-chevron">&#9656;</span><?= clean($subject['name']) ?>
                        <span class="text-muted" style="font-size:14px;font-weight:normal;">
                            (<?= $topicCount ?> topic<?= $topicCount === 1 ? '' : 's' ?>)
                        </span>
                    </h3>
                </summary>

                <?php if (empty($topics)): ?>
                    <p class="text-muted" style="margin-top:10px;">No topics added yet for this subject.</p>
                <?php else: ?>
                    <table style="margin-top:10px;">
                        <thead>
                            <tr>
                                <th>Topic</th>
                                <th>File</th>
                                <th>Added On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($topics as $topic): ?>
                            <tr>
                                <td><?= clean($topic['name']) ?></td>
                                <td><a href="../<?= clean($topic['pdf_path']) ?>" target="_blank">View File</a></td>
                                <td><?= clean(date('d M Y', strtotime($topic['created_at']))) ?></td>
                                <td class="actions">
                                    <a class="btn" href="edit_topic.php?id=<?= (int)$topic['id'] ?>">Edit</a>
                                    <a class="btn btn-danger" href="delete_topic.php?id=<?= (int)$topic['id'] ?>"
                                       onclick="return confirm('Delete this topic? This cannot be undone.');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </details>

            <div class="actions" style="margin-top:12px;">
                <a class="btn" href="add_topic.php?subject_id=<?= (int)$subject['id'] ?>">+ Add Topic</a>
                <a class="btn" href="edit_subject.php?id=<?= (int)$subject['id'] ?>">Edit Subject</a>
                <a class="btn btn-danger" href="delete_subject.php?id=<?= (int)$subject['id'] ?>"
                   onclick="return confirm('Delete this subject and ALL its topics? This cannot be undone.');">Delete Subject</a>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
