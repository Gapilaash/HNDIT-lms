<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_admin();

$errors = [];

// Optional pre-fill (e.g. when coming from a "+ Add Topic" link under a subject)
$prefillSubjectId = $_GET['subject_id'] ?? ($_POST['subject_id'] ?? '');
$prefillYear = $_GET['year'] ?? '';
$prefillSemester = $_GET['semester'] ?? '';

if ($prefillSubjectId !== '') {
    $stmt = $pdo->prepare("SELECT * FROM subjects WHERE id = ?");
    $stmt->execute([$prefillSubjectId]);
    $prefillSubject = $stmt->fetch();
    if ($prefillSubject) {
        $prefillYear = $prefillSubject['year'];
        $prefillSemester = $prefillSubject['semester'];
    }
}

// All subjects, used to build the client-side year/semester filter
$allSubjects = $pdo->query("SELECT id, name, year, semester FROM subjects ORDER BY id ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subjectId = $_POST['subject_id'] ?? '';
    $topicName = trim($_POST['topic_name'] ?? '');

    $subjectStmt = $pdo->prepare("SELECT * FROM subjects WHERE id = ?");
    $subjectStmt->execute([$subjectId]);
    $subject = $subjectStmt->fetch();

    if (!$subject) $errors[] = 'Please select a valid subject.';
    if ($topicName === '') $errors[] = 'Topic name is required.';

    if (empty($_FILES['pdf']) || $_FILES['pdf']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Please upload a file.';
    } elseif ($_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload failed. Please try again.';
    } else {
        $fileTmp = $_FILES['pdf']['tmp_name'];
        $fileName = $_FILES['pdf']['name'];
        $fileSize = $_FILES['pdf']['size'];
        $fileError = validate_subject_file($fileTmp, $fileName, $fileSize);
        if ($fileError !== null) {
            $errors[] = $fileError;
        }
    }

    if (empty($errors)) {
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $safeName = preg_replace('/[^A-Za-z0-9_-]/', '_', pathinfo($fileName, PATHINFO_FILENAME));
        $newFileName = $safeName . '_' . time() . '.' . $ext;
        $destination = __DIR__ . '/../uploads/' . $newFileName;

        if (move_uploaded_file($fileTmp, $destination)) {
            $pdfPathForDb = 'uploads/' . $newFileName;
            $stmt = $pdo->prepare(
                "INSERT INTO topics (subject_id, name, pdf_path, uploaded_by) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$subject['id'], $topicName, $pdfPathForDb, $_SESSION['user_id']]);
            flash('success', 'Topic added successfully.');
            redirect('subjects.php?year=' . $subject['year'] . '&semester=' . $subject['semester']);
        } else {
            $errors[] = 'Could not save the uploaded file. Check folder permissions on uploads/.';
        }
    } else {
        // keep the user's selections on error
        $prefillSubjectId = $subjectId;
        $prefillYear = $_POST['year'] ?? $prefillYear;
        $prefillSemester = $_POST['semester'] ?? $prefillSemester;
    }
}

$base_path = '../';
$page_title = 'Add Topic';
include __DIR__ . '/../includes/header.php';
?>

<p><a href="dashboard.php">&larr; Back to dashboard</a></p>
<h1>Add Topic</h1>

<div class="card" style="max-width:600px;">
    <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?= clean($err) ?></div>
    <?php endforeach; ?>

    <?php if (empty($allSubjects)): ?>
        <p class="text-muted">No subjects exist yet. <a href="add_subject.php">Add a subject first</a>.</p>
    <?php else: ?>
        <form method="POST" enctype="multipart/form-data" id="addTopicForm">
            <div class="field">
                <label for="year">Academic Year</label>
                <select id="year" name="year" required>
                    <option value="">-- Select Year --</option>
                    <option value="1" <?= $prefillYear === '1' ? 'selected' : '' ?>>1st Year</option>
                    <option value="2" <?= $prefillYear === '2' ? 'selected' : '' ?>>2nd Year</option>
                </select>
            </div>
            <div class="field">
                <label for="semester">Semester</label>
                <select id="semester" name="semester" required>
                    <option value="">-- Select Semester --</option>
                    <option value="1" <?= $prefillSemester === '1' ? 'selected' : '' ?>>1st Semester</option>
                    <option value="2" <?= $prefillSemester === '2' ? 'selected' : '' ?>>2nd Semester</option>
                </select>
            </div>
            <div class="field">
                <label for="subject_id">Subject Name</label>
                <select id="subject_id" name="subject_id" required>
                    <option value="">-- Select Year &amp; Semester First --</option>
                </select>
                <p class="text-muted" id="noSubjectsMsg" style="display:none;">
                    No subjects found for this year/semester. <a href="add_subject.php">Add a subject first</a>.
                </p>
            </div>
            <div class="field">
                <label for="topic_name">Topic Name</label>
                <input type="text" id="topic_name" name="topic_name"
                       value="<?= clean($_POST['topic_name'] ?? '') ?>"
                       placeholder="e.g. Introduction, Variables, Data Types" required>
            </div>
            <div class="field">
                <label for="pdf">Topic File (PDF, Word, PowerPoint, or Excel; max 10MB)</label>
                <input type="file" id="pdf" name="pdf" accept="<?= ALLOWED_FILE_ACCEPT ?>" required>
            </div>
            <button type="submit" class="btn">Add Topic</button>
        </form>
    <?php endif; ?>
</div>

<script>
    var allSubjects = <?= json_encode($allSubjects) ?>;
    var preselectSubjectId = <?= json_encode((string)$prefillSubjectId) ?>;

    var yearSelect = document.getElementById('year');
    var semesterSelect = document.getElementById('semester');
    var subjectSelect = document.getElementById('subject_id');
    var noSubjectsMsg = document.getElementById('noSubjectsMsg');

    function refreshSubjectOptions() {
        var year = yearSelect.value;
        var semester = semesterSelect.value;

        subjectSelect.innerHTML = '';

        if (!year || !semester) {
            subjectSelect.appendChild(new Option('-- Select Year & Semester First --', ''));
            noSubjectsMsg.style.display = 'none';
            return;
        }

        var matches = allSubjects.filter(function (s) {
            return String(s.year) === String(year) && String(s.semester) === String(semester);
        });

        if (matches.length === 0) {
            subjectSelect.appendChild(new Option('-- No Subjects Found --', ''));
            noSubjectsMsg.style.display = 'block';
            return;
        }

        noSubjectsMsg.style.display = 'none';
        subjectSelect.appendChild(new Option('-- Select Subject --', ''));
        matches.forEach(function (s) {
            var opt = new Option(s.name, s.id);
            if (preselectSubjectId && String(s.id) === String(preselectSubjectId)) {
                opt.selected = true;
            }
            subjectSelect.appendChild(opt);
        });
    }

    yearSelect.addEventListener('change', refreshSubjectOptions);
    semesterSelect.addEventListener('change', refreshSubjectOptions);

    // Run once on load in case year/semester were pre-filled server-side
    refreshSubjectOptions();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
