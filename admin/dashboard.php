<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_admin();

$counts = ['1-1' => 0, '1-2' => 0, '2-1' => 0, '2-2' => 0];
$countStmt = $pdo->query("SELECT year, semester, COUNT(*) AS c FROM subjects GROUP BY year, semester");
foreach ($countStmt->fetchAll() as $row) {
    $counts[$row['year'] . '-' . $row['semester']] = (int)$row['c'];
}

$base_path = '../';
$page_title = 'Manage Subjects';
include __DIR__ . '/../includes/header.php';
?>

<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
    <h1>Manage Subjects</h1>
    <div class="actions">
        <a href="add_subject.php" class="btn">+ Add New Subject</a>
        <a href="add_topic.php" class="btn">+ Add Topic</a>
    </div>
</div>
<p class="text-muted">Select a year and semester to view and manage its subjects and topics.</p>

<h2 style="margin-top:30px;">1st Year</h2>
<div class="grid">
    <a class="category-card" href="subjects.php?year=1&semester=1">1st Year – 1st Semester <span class="text-muted">(<?= $counts['1-1'] ?> subjects)</span></a>
    <a class="category-card" href="subjects.php?year=1&semester=2">1st Year – 2nd Semester <span class="text-muted">(<?= $counts['1-2'] ?> subjects)</span></a>
</div>

<h2 style="margin-top:30px;">2nd Year</h2>
<div class="grid">
    <a class="category-card" href="subjects.php?year=2&semester=1">2nd Year – 1st Semester <span class="text-muted">(<?= $counts['2-1'] ?> subjects)</span></a>
    <a class="category-card" href="subjects.php?year=2&semester=2">2nd Year – 2nd Semester <span class="text-muted">(<?= $counts['2-2'] ?> subjects)</span></a>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
