<?php $user = current_user(); $unreadNotifications = 0; if ($user && $user['role'] === 'student') { $notificationCount = db()->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND read_at IS NULL'); $notificationCount->execute(['user_id' => $user['id']]); $unreadNotifications = (int)$notificationCount->fetchColumn(); } ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= e(app_url()) ?>">Emma Learn</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <?php if ($user): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= e(app_url($user['role'] === 'admin' ? 'admin/dashboard.php' : 'student/dashboard.php')) ?>">Dashboard</a></li>
                    <?php if ($user['role'] === 'student'): ?><li class="nav-item"><a class="nav-link" href="<?= e(app_url('student/courses.php')) ?>">Courses</a></li><li class="nav-item"><a class="nav-link" href="<?= e(app_url('student/quizzes.php')) ?>">Quizzes</a></li><?php endif; ?>
                    <?php if ($user['role'] === 'student'): ?><li class="nav-item"><a class="nav-link" href="<?= e(app_url('student/profile.php')) ?>">Profile</a></li><li class="nav-item"><a class="nav-link" href="<?= e(app_url('student/notifications.php')) ?>">Notifications<?php if ($unreadNotifications): ?> <span class="badge text-bg-warning"><?= $unreadNotifications ?></span><?php endif; ?></a></li><?php endif; ?>
                    <?php if ($user['role'] === 'admin'): ?><li class="nav-item"><a class="nav-link" href="<?= e(app_url('admin/courses.php')) ?>">Courses</a></li><li class="nav-item"><a class="nav-link" href="<?= e(app_url('admin/quizzes.php')) ?>">Quizzes</a></li><li class="nav-item"><a class="nav-link" href="<?= e(app_url('admin/students.php')) ?>">Students</a></li><li class="nav-item"><a class="nav-link" href="<?= e(app_url('admin/leaderboard.php')) ?>">Leaderboard</a></li><?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="<?= e(app_url('auth/logout.php')) ?>">Sign out</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= e(app_url('auth/login.php')) ?>">Student sign in</a></li>
                    <li class="nav-item"><a class="btn btn-light btn-sm px-3" href="<?= e(app_url('auth/register.php')) ?>">Join free</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
