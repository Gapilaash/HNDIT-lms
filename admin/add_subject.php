<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_admin();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $year = $_POST['year'] ?? '';
    $semester = $_POST['semester'] ?? '';

    if ($name === '') $errors[] = 'Subject name is required.';
    if (!in_array($year, ['1', '2'])) $errors[] = 'Please select a valid year.';
    if (!in_array($semester, ['1', '2'])) $errors[] = 'Please select a valid semester.';

    if (empty($errors)) {
        $check = $pdo->prepare("SELECT id FROM subjects WHERE name = ? AND year = ? AND semester = ?");
        $check->execute([$name, $year, $semester]);
        if ($check->fetch()) {
            $errors[] = 'This subject already exists for the selected year and semester.';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            "INSERT INTO subjects (name, year, semester, uploaded_by) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$name, $year, $semester, $_SESSION['user_id']]);
        $newSubjectId = $pdo->lastInsertId();
        flash('success', 'Subject added. Now add topics and upload files under it.');
        redirect('add_topic.php?subject_id=' . $newSubjectId);
    }
}

$base_path = '../';
$page_title = 'Add Subject';
include __DIR__ . '/../includes/header.php';
?>

<p><a href="dashboard.php">&larr; Back to dashboard</a></p>
<h1>Add New Subject</h1>

<div class="card" style="max-width:600px;">
    <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?= clean($err) ?></div>
    <?php endforeach; ?>

    <form method="POST">
        <div class="field">
            <label for="name">Subject Name</label>
            <input type="text" id="name" name="name" value="<?= clean($_POST['name'] ?? '') ?>" placeholder="e.g. Fundamentals of Programming" required>
        </div>
        <div class="field">
            <label for="year">Academic Year</label>
            <select id="year" name="year" required>
                <option value="">-- Select Year --</option>
                <option value="1" <?= ($_POST['year'] ?? '') === '1' ? 'selected' : '' ?>>1st Year</option>
                <option value="2" <?= ($_POST['year'] ?? '') === '2' ? 'selected' : '' ?>>2nd Year</option>
            </select>
        </div>
        <div class="field">
            <label for="semester">Semester</label>
            <select id="semester" name="semester" required>
                <option value="">-- Select Semester --</option>
                <option value="1" <?= ($_POST['semester'] ?? '') === '1' ? 'selected' : '' ?>>1st Semester</option>
                <option value="2" <?= ($_POST['semester'] ?? '') === '2' ? 'selected' : '' ?>>2nd Semester</option>
            </select>
        </div>
        <button type="submit" class="btn">Add Subject</button>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
