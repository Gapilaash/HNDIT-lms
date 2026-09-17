<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_admin();

$id = $_GET['id'] ?? ($_POST['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM subjects WHERE id = ?");
$stmt->execute([$id]);
$subject = $stmt->fetch();

if (!$subject) {
    flash('error', 'Subject not found.');
    redirect('dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $year = $_POST['year'] ?? '';
    $semester = $_POST['semester'] ?? '';

    if ($name === '') $errors[] = 'Subject name is required.';
    if (!in_array($year, ['1', '2'])) $errors[] = 'Please select a valid year.';
    if (!in_array($semester, ['1', '2'])) $errors[] = 'Please select a valid semester.';

    if (empty($errors)) {
        $check = $pdo->prepare("SELECT id FROM subjects WHERE name = ? AND year = ? AND semester = ? AND id != ?");
        $check->execute([$name, $year, $semester, $id]);
        if ($check->fetch()) {
            $errors[] = 'Another subject with this name already exists for the selected year and semester.';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE subjects SET name = ?, year = ?, semester = ? WHERE id = ?");
        $stmt->execute([$name, $year, $semester, $id]);
        flash('success', 'Subject updated successfully.');
        redirect("subjects.php?year=$year&semester=$semester");
    }
}

$base_path = '../';
$page_title = 'Edit Subject';
include __DIR__ . '/../includes/header.php';
?>

<p><a href="dashboard.php">&larr; Back to dashboard</a></p>
<h1>Edit Subject</h1>

<div class="card" style="max-width:600px;">
    <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?= clean($err) ?></div>
    <?php endforeach; ?>

    <form method="POST">
        <input type="hidden" name="id" value="<?= (int)$subject['id'] ?>">
        <div class="field">
            <label for="name">Subject Name</label>
            <input type="text" id="name" name="name" value="<?= clean($_POST['name'] ?? $subject['name']) ?>" required>
        </div>
        <div class="field">
            <label for="year">Academic Year</label>
            <select id="year" name="year" required>
                <option value="1" <?= ($_POST['year'] ?? $subject['year']) === '1' ? 'selected' : '' ?>>1st Year</option>
                <option value="2" <?= ($_POST['year'] ?? $subject['year']) === '2' ? 'selected' : '' ?>>2nd Year</option>
            </select>
        </div>
        <div class="field">
            <label for="semester">Semester</label>
            <select id="semester" name="semester" required>
                <option value="1" <?= ($_POST['semester'] ?? $subject['semester']) === '1' ? 'selected' : '' ?>>1st Semester</option>
                <option value="2" <?= ($_POST['semester'] ?? $subject['semester']) === '2' ? 'selected' : '' ?>>2nd Semester</option>
            </select>
        </div>
        <button type="submit" class="btn">Save Changes</button>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
