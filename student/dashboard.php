<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_student();

$base_path = '../';
$page_title = 'Browse Subjects';
include __DIR__ . '/../includes/header.php';
?>

<h1>Welcome, <?= clean($_SESSION['name']) ?></h1>
<p class="text-muted">Select your academic year and semester to view subjects.</p>

<h2 style="margin-top:30px;">1st Year</h2>
<div class="grid">
    <a class="category-card" href="subjects.php?year=1&semester=1">1st Year – 1st Semester</a>
    <a class="category-card" href="subjects.php?year=1&semester=2">1st Year – 2nd Semester</a>
</div>

<h2 style="margin-top:30px;">2nd Year</h2>
<div class="grid">
    <a class="category-card" href="subjects.php?year=2&semester=1">2nd Year – 1st Semester</a>
    <a class="category-card" href="subjects.php?year=2&semester=2">2nd Year – 2nd Semester</a>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
