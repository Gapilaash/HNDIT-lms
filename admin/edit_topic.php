<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_admin();

$id = $_GET['id'] ?? ($_POST['id'] ?? 0);

$stmt = $pdo->prepare("SELECT topics.*, subjects.name AS subject_name, subjects.year AS subject_year, subjects.semester AS subject_semester
                        FROM topics
                        JOIN subjects ON subjects.id = topics.subject_id
                        WHERE topics.id = ?");
$stmt->execute([$id]);
$topic = $stmt->fetch();

if (!$topic) {
    flash('error', 'Topic not found.');
    redirect('dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topicName = trim($_POST['topic_name'] ?? '');
    if ($topicName === '') $errors[] = 'Topic name is required.';

    $pdfPathForDb = $topic['pdf_path']; // keep existing unless a new file is uploaded

    if (!empty($_FILES['pdf']) && $_FILES['pdf']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload failed. Please try again.';
        } else {
            $fileTmp = $_FILES['pdf']['tmp_name'];
            $fileName = $_FILES['pdf']['name'];
            $fileSize = $_FILES['pdf']['size'];
            $fileError = validate_subject_file($fileTmp, $fileName, $fileSize);

            if ($fileError !== null) {
                $errors[] = $fileError;
            }

            if (empty($errors)) {
                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $safeName = preg_replace('/[^A-Za-z0-9_-]/', '_', pathinfo($fileName, PATHINFO_FILENAME));
                $newFileName = $safeName . '_' . time() . '.' . $ext;
                $destination = __DIR__ . '/../uploads/' . $newFileName;

                if (move_uploaded_file($fileTmp, $destination)) {
                    $oldFile = __DIR__ . '/../' . $topic['pdf_path'];
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                    $pdfPathForDb = 'uploads/' . $newFileName;
                } else {
                    $errors[] = 'Could not save the uploaded file. Check folder permissions on uploads/.';
                }
            }
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE topics SET name = ?, pdf_path = ? WHERE id = ?");
        $stmt->execute([$topicName, $pdfPathForDb, $id]);
        flash('success', 'Topic updated successfully.');
        redirect('subjects.php?year=' . $topic['subject_year'] . '&semester=' . $topic['subject_semester']);
    }
}

$base_path = '../';
$page_title = 'Edit Topic';
include __DIR__ . '/../includes/header.php';
?>

<p><a href="dashboard.php">&larr; Back to dashboard</a></p>
<h1>Edit Topic</h1>
<p class="text-muted">Subject: <?= clean($topic['subject_name']) ?></p>

<div class="card" style="max-width:600px;">
    <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?= clean($err) ?></div>
    <?php endforeach; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= (int)$topic['id'] ?>">
        <div class="field">
            <label for="topic_name">Topic Name</label>
            <input type="text" id="topic_name" name="topic_name"
                   value="<?= clean($_POST['topic_name'] ?? $topic['name']) ?>" required>
        </div>
        <div class="field">
            <label>Current File</label>
            <p><a href="../<?= clean($topic['pdf_path']) ?>" target="_blank">View current file</a></p>
        </div>
        <div class="field">
            <label for="pdf">Replace File (optional; PDF, Word, PowerPoint, or Excel; max 10MB)</label>
            <input type="file" id="pdf" name="pdf" accept="<?= ALLOWED_FILE_ACCEPT ?>">
        </div>
        <button type="submit" class="btn">Save Changes</button>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
