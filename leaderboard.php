<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
require_role('admin');
$statement = db()->query("SELECT u.first_name, u.last_name, u.email, p.total_xp, p.level, p.current_streak FROM users u INNER JOIN student_profiles p ON p.user_id = u.id WHERE u.role = 'student' AND u.status = 'active' ORDER BY p.total_xp DESC, p.level DESC, u.first_name ASC LIMIT 100");
$leaders = $statement->fetchAll();
$rank = 0;
$pageTitle = 'Leaderboard';
require __DIR__ . '/../includes/header.php';
?>
<div class="py-4"><div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><h1 class="h2 fw-bold">Student Leaderboard</h1><p class="text-secondary mb-0">Review student rankings by XP and learning streak.</p></div><a class="btn btn-outline-primary" href="<?= e(app_url('admin/dashboard.php')) ?>">Dashboard</a></div><div class="card"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th class="ps-4">#</th><th>Student</th><th>Email</th><th>Level</th><th>XP</th><th>Streak</th></tr></thead><tbody><?php if (!$leaders): ?><tr><td colspan="6" class="text-center text-secondary p-4">No student scores yet.</td></tr><?php endif; ?><?php foreach ($leaders as $leader): $rank++; ?><tr><td class="ps-4 fw-bold"><?= $rank ?></td><td><?= e($leader['first_name'] . ' ' . $leader['last_name']) ?></td><td><?= e($leader['email']) ?></td><td><?= (int)$leader['level'] ?></td><td><?= (int)$leader['total_xp'] ?></td><td><?= (int)$leader['current_streak'] ?> days</td></tr><?php endforeach; ?></tbody></table></div></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
