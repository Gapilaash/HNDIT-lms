<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_student();

$errors = [];

$stmt = $pdo->query("SELECT id, name, year, semester FROM subjects ORDER BY year, semester, name");
$subjects = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    $subjectId = $_POST['subject_id'] ?? '';
    $subjectId = ($subjectId === '' ) ? null : (int)$subjectId;

    if ($message === '') {
        $errors[] = 'Please write your feedback before submitting.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO feedback (user_id, subject_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $subjectId, $message]);
        flash('success', 'Thank you! Your feedback has been submitted.');
        redirect('feedback.php');
    }
}

$base_path = '../';
$page_title = 'Give Feedback';
include __DIR__ . '/../includes/header.php';
?>

<h1>Give Feedback</h1>
<p class="text-muted">Tell us about a specific subject's materials, or share general feedback about the LMS.</p>

<div class="card" style="max-width:600px;">
    <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?= clean($err) ?></div>
    <?php endforeach; ?>

    <form method="POST">
        <div class="field">
            <label for="subject_id">Related Subject (optional)</label>
            <select id="subject_id" name="subject_id">
                <option value="">General LMS Feedback</option>
                <?php foreach ($subjects as $s): ?>
                    <option value="<?= (int)$s['id'] ?>">
                        <?= clean(semester_label($s['year'], $s['semester'])) ?> — <?= clean($s['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label for="message">Your Feedback</label>
            <textarea id="message" name="message" required><?= clean($_POST['message'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn">Submit Feedback</button>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
