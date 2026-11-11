<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Learn with purpose';
require __DIR__ . '/includes/header.php';
?>
<section class="hero rounded-4 p-4 p-md-5 mb-5">
    <div class="row align-items-center g-4">
        <div class="col-lg-7">
            <span class="badge text-bg-primary mb-3">Learning made rewarding</span>
            <h1 class="display-5 fw-bold">Build skills. Earn XP. Reach your next level.</h1>
            <p class="lead text-secondary mt-3">Emma Learn combines structured courses with motivating challenges, progress tracking, and achievements.</p>
            <div class="d-flex flex-wrap gap-2 mt-4">
                <a class="btn btn-primary btn-lg" href="<?= e(app_url('auth/register.php')) ?>">Start learning</a>
                <a class="btn btn-outline-primary btn-lg" href="<?= e(app_url('auth/login.php')) ?>">Sign in</a>
            </div>
        </div>
        <div class="col-lg-5"><div class="hero-card bg-white p-4"><p class="text-uppercase text-primary fw-semibold small">Your learning journey</p><div class="d-flex justify-content-between mb-2"><span>Weekly progress</span><strong>72%</strong></div><div class="progress mb-4" role="progressbar" aria-label="Weekly progress" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar" style="width: 72%"></div></div><div class="d-flex justify-content-between"><span class="text-secondary">Current level</span><strong>Level 1</strong></div><div class="d-flex justify-content-between mt-2"><span class="text-secondary">Daily streak</span><strong>1 day</strong></div></div></div>
    </div>
</section>
<section class="mb-4"><div class="text-center mb-4"><h2 class="fw-bold">Everything you need to keep moving</h2><p class="text-secondary">A clear, encouraging space for consistent learning.</p></div><div class="row g-4"><div class="col-md-4"><div class="card feature-card p-4"><h3 class="h5">Structured courses</h3><p class="text-secondary mb-0">Move through focused lessons at your own pace.</p></div></div><div class="col-md-4"><div class="card feature-card p-4"><h3 class="h5">Rewarded progress</h3><p class="text-secondary mb-0">Turn completed learning into XP, points, and badges.</p></div></div><div class="col-md-4"><div class="card feature-card p-4"><h3 class="h5">Healthy competition</h3><p class="text-secondary mb-0">Use streaks and leaderboards to stay motivated.</p></div></div></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
