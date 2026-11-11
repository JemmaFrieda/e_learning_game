<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
require_guest();
$errors = [];
$requestedRole = $_SERVER['REQUEST_METHOD'] === 'POST' ? post_string('role') : (string)($_GET['role'] ?? '');
$role = $requestedRole === 'admin' ? 'admin' : 'student';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) $errors[] = 'Your session token expired. Please try again.';
    $email = filter_var(post_string('email'), FILTER_VALIDATE_EMAIL);
    $password = (string)($_POST['password'] ?? '');
    if (!$email) $errors[] = 'Enter a valid email address.';
    if ($password === '') $errors[] = 'Enter your password.';
    if (!$errors && authenticate((string)$email, $password, $role)) redirect($role === 'admin' ? 'admin/dashboard.php' : 'student/dashboard.php');
    if (!$errors) $errors[] = 'The email, password, or account type is incorrect.';
}
$pageTitle = $role === 'admin' ? 'Admin sign in' : 'Student sign in';
require __DIR__ . '/../includes/header.php';
?>
<div class="card auth-card"><div class="card-body p-4 p-md-5"><h1 class="h3 fw-bold mb-2"><?= e($pageTitle) ?></h1><p class="text-secondary mb-4">Access your Emma Learn account.</p><?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?><form method="post" data-disable-submit novalidate><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="role" value="<?= e($role) ?>"><div class="mb-3"><label class="form-label" for="email">Email address</label><input class="form-control" id="email" name="email" type="email" value="<?= e(post_string('email')) ?>" required autocomplete="email"></div><div class="mb-4"><label class="form-label" for="password">Password</label><input class="form-control" id="password" name="password" type="password" required autocomplete="current-password"></div><button class="btn btn-primary w-100" type="submit">Sign in</button></form><?php if ($role === 'student'): ?><p class="text-center text-secondary mt-4 mb-0">New here? <a href="<?= e(app_url('auth/register.php')) ?>">Create a student account</a></p><p class="text-center small mt-2 mb-0"><a href="<?= e(app_url('auth/login.php?role=admin')) ?>">Admin sign in</a></p><?php endif; ?></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
