<?php
declare(strict_types=1);

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function require_guest(): void
{
    if (is_logged_in()) {
        redirect(current_user()['role'] === 'admin' ? 'admin/dashboard.php' : 'student/dashboard.php');
    }
}

function require_role(string $role): void
{
    if (!is_logged_in() || (current_user()['role'] ?? '') !== $role) {
        set_flash('login', 'Please log in with an authorized account to continue.');
        redirect('auth/login.php');
    }
}

function authenticate(string $email, string $password, string $role): bool
{
    $email = strtolower(trim($email));

    if ($role === 'admin') {
        $statement = db()->prepare(
            'SELECT id, name, email, password_hash
             FROM admins WHERE email = :email LIMIT 1'
        );
        $statement->execute(['email' => $email]);
        $admin = $statement->fetch();

        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $admin['id'],
            'first_name' => $admin['name'],
            'last_name' => '',
            'name' => $admin['name'],
            'email' => $admin['email'],
            'role' => 'admin',
            'status' => 'active',
        ];
        return true;
    }

    $statement = db()->prepare(
        'SELECT id, first_name, last_name, email, password_hash, role, status
         FROM users WHERE email = :email AND role = :role LIMIT 1'
    );
    $statement->execute(['email' => $email, 'role' => 'student']);
    $user = $statement->fetch();

    if (!$user || $user['status'] !== 'active' || !password_verify($password, $user['password_hash'])) {
        return false;
    }

    session_regenerate_id(true);
    unset($user['password_hash']);
    $_SESSION['user'] = $user;
    record_daily_activity((int)$user['id']);
    sync_achievements_and_badges((int)$user['id']);
    return true;
}
