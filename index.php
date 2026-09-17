<?php
require_once __DIR__ . '/includes/functions.php';

if (is_admin()) redirect('admin/dashboard.php');
if (is_student()) redirect('student/dashboard.php');

$base_path = '';
$page_title = 'Welcome';
include __DIR__ . '/includes/header.php';
?>

<div class="hero">
    <h1>HNDIT Learning Management System</h1>
    <p class="text-muted">Access your subject materials organized by academic year and semester.</p>
</div>

<div class="grid" style="max-width:600px;margin:0 auto;">
    <a href="student/login.php" class="category-card">Student Login</a>
    <a href="student/register.php" class="category-card">Student Register</a>
    <a href="admin/login.php" class="category-card">Admin Login</a>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
