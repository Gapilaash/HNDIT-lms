<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

if (is_student()) redirect('dashboard.php');
if (is_admin()) redirect('../admin/dashboard.php');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = 'Please enter both email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'student'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = 'student';
            redirect('dashboard.php');
        } else {
            $errors[] = 'Invalid email or password.';
        }
    }
}

$base_path = '../';
$page_title = 'Student Login';
include __DIR__ . '/../includes/header.php';
?>

<div class="auth-box card">
    <h2>Student Login</h2>

    <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?= clean($err) ?></div>
    <?php endforeach; ?>

    <form method="POST" novalidate>
        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= clean($_POST['email'] ?? '') ?>" required>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn">Login</button>
    </form>
    <p class="text-muted" style="margin-top:14px;">Don't have an account? <a href="register.php">Register here</a></p>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
