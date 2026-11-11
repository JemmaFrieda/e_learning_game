<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
require_role('student');
$userId = (int)current_user()['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) { set_flash('danger', 'Your session token expired.'); redirect('student/notifications.php'); }
    $notificationId = filter_var($_POST['notification_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($notificationId) { $statement = db()->prepare('UPDATE notifications SET read_at = COALESCE(read_at, CURRENT_TIMESTAMP) WHERE id = :id AND user_id = :user_id'); $statement->execute(['id' => $notificationId, 'user_id' => $userId]); }
    else { $statement = db()->prepare('UPDATE notifications SET read_at = COALESCE(read_at, CURRENT_TIMESTAMP) WHERE user_id = :user_id'); $statement->execute(['user_id' => $userId]); }
    redirect('student/notifications.php');
}
$statement = db()->prepare('SELECT id, title, message, read_at, created_at FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 50'); $statement->execute(['user_id' => $userId]); $notifications = $statement->fetchAll();
$pageTitle = 'Notifications'; require __DIR__ . '/../includes/header.php';
?>
<div class="py-4"><div class="d-flex justify-content-between align-items-center mb-4"><div><h1 class="h2 fw-bold mb-1">Notifications</h1><p class="text-secondary mb-0">Updates about your learning journey.</p></div><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="btn btn-outline-secondary" type="submit">Mark all read</button></form></div><div class="card"><div class="list-group list-group-flush"><?php if (!$notifications): ?><div class="p-5 text-center text-secondary">You have no notifications.</div><?php endif; ?><?php foreach ($notifications as $notification): ?><div class="list-group-item <?= $notification['read_at'] ? '' : 'bg-primary-subtle' ?> p-4"><div class="d-flex justify-content-between gap-3"><div><strong><?= e($notification['title']) ?></strong><p class="mb-1 text-secondary"><?= e($notification['message']) ?></p><small class="text-secondary"><?= e($notification['created_at']) ?></small></div><?php if (!$notification['read_at']): ?><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="notification_id" value="<?= (int)$notification['id'] ?>"><button class="btn btn-sm btn-outline-primary" type="submit">Mark read</button></form><?php endif; ?></div></div><?php endforeach; ?></div></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
