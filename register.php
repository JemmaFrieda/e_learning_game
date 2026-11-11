<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
require_guest();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) $errors[] = 'Your session token expired. Please try again.';
    $firstName = post_string('first_name'); $lastName = post_string('last_name'); $email = filter_var(post_string('email'), FILTER_VALIDATE_EMAIL); $password = (string)($_POST['password'] ?? ''); $confirm = (string)($_POST['password_confirmation'] ?? '');
    if ($firstName === '' || mb_strlen($firstName) > 80) $errors[] = 'Enter a valid first name.';
    if ($lastName === '' || mb_strlen($lastName) > 80) $errors[] = 'Enter a valid last name.';
    if (!$email) $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';
    if (!$errors) {
        $check = db()->prepare('SELECT id FROM users WHERE email = :email LIMIT 1'); $check->execute(['email' => strtolower((string)$email)]);
        if ($check->fetch()) $errors[] = 'An account with this email already exists.';
    }
    if (!$errors) { $insert = db()->prepare('INSERT INTO users (first_name, last_name, email, password_hash, role) VALUES (:first_name, :last_name, :email, :password_hash, \'student\')'); $insert->execute(['first_name' => $firstName, 'last_name' => $lastName, 'email' => strtolower((string)$email), 'password_hash' => password_hash($password, PASSWORD_DEFAULT)]); $userId = (int)db()->lastInsertId(); $profile = db()->prepare('INSERT INTO student_profiles (user_id) VALUES (:user_id)'); $profile->execute(['user_id' => $userId]); set_flash('success', 'Your account was created. You can now sign in.'); redirect('auth/login.php'); }
}
$pageTitle = 'Create a student account'; require __DIR__ . '/../includes/header.php';
?>
<div class="card auth-card"><div class="card-body p-4 p-md-5"><h1 class="h3 fw-bold mb-2">Create your account</h1><p class="text-secondary mb-4">Start your learning journey with Emma Learn.</p><?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?><form method="post" data-disable-submit novalidate><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><div class="row g-3"><div class="col-sm-6"><label class="form-label" for="first_name">First name</label><input class="form-control" id="first_name" name="first_name" value="<?= e(post_string('first_name')) ?>" required></div><div class="col-sm-6"><label class="form-label" for="last_name">Last name</label><input class="form-control" id="last_name" name="last_name" value="<?= e(post_string('last_name')) ?>" required></div><div class="col-12"><label class="form-label" for="email">Email address</label><input class="form-control" id="email" name="email" type="email" value="<?= e(post_string('email')) ?>" required autocomplete="email"></div><div class="col-sm-6"><label class="form-label" for="password">Password</label><input class="form-control" id="password" name="password" type="password" minlength="8" required autocomplete="new-password"></div><div class="col-sm-6"><label class="form-label" for="password_confirmation">Confirm password</label><input class="form-control" id="password_confirmation" name="password_confirmation" type="password" minlength="8" required autocomplete="new-password"></div></div><button class="btn btn-primary w-100 mt-4" type="submit">Create account</button></form><p class="text-center text-secondary mt-4 mb-0">Already registered? <a href="<?= e(app_url('auth/login.php')) ?>">Sign in</a></p></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
