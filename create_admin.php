<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This setup script must be run from the command line.');
}

require_once __DIR__ . '/../config/database.php';

$name = 'Emma Admin';
$email = 'Emma123@gmail.com';

try {

    $pdo = db();

    // Check if the admin already exists
    $check = $pdo->prepare(
        'SELECT id FROM admins WHERE email = :email LIMIT 1'
    );

    $check->execute(['email' => strtolower($email)]);

    if ($check->fetch()) {
        exit("Admin already exists for {$email}. No changes were made.\n");
    }

    fwrite(STDOUT, 'Admin password: ');
    $password = trim((string)fgets(STDIN));
    if ($password === '') {
        exit("A password is required. No changes were made.\n");
    }
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Create admin account
    $stmt = $pdo->prepare(
        'INSERT INTO admins (name, email, password_hash)
         VALUES (:name, :email, :password_hash)'
    );

    $stmt->execute(['name' => $name, 'email' => strtolower($email), 'password_hash' => $passwordHash]);

    echo "Admin account created successfully.\n";

} catch (PDOException $e) {

    fwrite(STDERR, "Database error while creating the admin account.\n");
    exit(1);

}
