<?php
// $base_path must be defined by the including page:
//   '' for pages in the project root
//   '../' for pages inside /student or /admin
$base_path = $base_path ?? '';
$page_title = $page_title ?? 'HNDIT LMS';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= clean($page_title) ?> | HNDIT LMS</title>
<link rel="stylesheet" href="<?= $base_path ?>assets/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="<?= $base_path ?>index.php">HNDIT LMS</a>
        <nav>
            <?php if (is_admin()): ?>
                <a href="<?= $base_path ?>admin/dashboard.php">Manage Subjects</a>
                <a href="<?= $base_path ?>admin/feedback.php">Feedback</a>
                <a href="<?= $base_path ?>admin/logout.php">Logout (<?= clean($_SESSION['name']) ?>)</a>
            <?php elseif (is_student()): ?>
                <a href="<?= $base_path ?>student/dashboard.php">Browse Subjects</a>
                <a href="<?= $base_path ?>student/feedback.php">Give Feedback</a>
                <a href="<?= $base_path ?>student/logout.php">Logout (<?= clean($_SESSION['name']) ?>)</a>
            <?php else: ?>
                <a href="<?= $base_path ?>student/login.php">Student Login</a>
                <a href="<?= $base_path ?>student/register.php">Register</a>
                <a href="<?= $base_path ?>admin/login.php">Admin Login</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container">
<?php
$success = flash('success');
$error = flash('error');
if ($success): ?>
    <div class="alert alert-success"><?= clean($success) ?></div>
<?php endif;
if ($error): ?>
    <div class="alert alert-error"><?= clean($error) ?></div>
<?php endif; ?>
