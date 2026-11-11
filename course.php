<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
require_role('student');

$courseId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$courseId) { set_flash('danger', 'Invalid course selected.'); redirect('student/courses.php'); }
$courseStatement = db()->prepare("SELECT id, title, description FROM courses WHERE id = :id AND status = 'published'");
$courseStatement->execute(['id' => $courseId]);
$course = $courseStatement->fetch();
if (!$course) { set_flash('danger', 'Course not found.'); redirect('student/courses.php'); }
$enrollmentStatement = db()->prepare('SELECT id FROM enrollments WHERE user_id = :user_id AND course_id = :course_id');
$enrollmentStatement->execute(['user_id' => current_user()['id'], 'course_id' => $courseId]);
$isEnrolled = (bool)$enrollmentStatement->fetch();

$lessons = [];
if ($isEnrolled) {
    $lessonStatement = db()->prepare("SELECT l.id, l.title, l.duration_minutes, COALESCE(lp.completed_at IS NOT NULL, 0) AS is_completed FROM lessons l LEFT JOIN lesson_progress lp ON lp.lesson_id = l.id AND lp.user_id = :user_id WHERE l.course_id = :course_id AND l.status = 'published' ORDER BY l.sort_order, l.id");
    $lessonStatement->execute(['user_id' => current_user()['id'], 'course_id' => $courseId]);
    $lessons = $lessonStatement->fetchAll();
}
$completedLessons = count(array_filter($lessons, static fn(array $lesson): bool => (bool)$lesson['is_completed']));
$progress = count($lessons) ? (int)round(($completedLessons / count($lessons)) * 100) : 0;
$quizzes = [];
if ($isEnrolled) { $quizStatement = db()->prepare("SELECT q.id, q.title, l.title AS lesson_title FROM quizzes q INNER JOIN lessons l ON l.id = q.lesson_id WHERE l.course_id = :course_id AND q.status = 'published' AND l.status = 'published' ORDER BY l.sort_order, q.id"); $quizStatement->execute(['course_id' => $courseId]); $quizzes = $quizStatement->fetchAll(); }
$pageTitle = $course['title'];
require __DIR__ . '/../includes/header.php';
?>
<div class="py-4">
    <a class="text-decoration-none" href="<?= e(app_url('student/courses.php')) ?>">&larr; All courses</a>
    <div class="mt-3 mb-4"><h1 class="h2 fw-bold"><?= e($course['title']) ?></h1><p class="lead text-secondary mb-0"><?= e($course['description']) ?></p></div>
    <?php if (!$isEnrolled): ?>
        <div class="card border-primary mb-4"><div class="card-body p-4 d-flex flex-wrap justify-content-between align-items-center gap-3"><div><h2 class="h5">Ready to start?</h2><p class="text-secondary mb-0">Enroll to access lessons, videos, and quizzes.</p></div><form method="post" action="<?= e(app_url('student/courses.php')) ?>"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="course_id" value="<?= (int)$courseId ?>"><button class="btn btn-primary" type="submit">Enroll now</button></form></div></div>
    <?php else: ?>
        <div class="card mb-4"><div class="card-body"><div class="d-flex justify-content-between mb-2"><strong>Course progress</strong><span><?= $progress ?>%</span></div><div class="progress" role="progressbar" aria-label="Course progress" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar" style="width: <?= $progress ?>%"></div></div><div class="small text-secondary mt-2"><?= $completedLessons ?> of <?= count($lessons) ?> lessons completed</div></div></div>
        <div class="card mb-4"><div class="card-body p-0"><div class="p-4 pb-2"><h2 class="h5">Lessons</h2></div><div class="list-group list-group-flush"><?php if (!$lessons): ?><div class="p-4 text-secondary">Lessons will be available soon.</div><?php endif; ?><?php foreach ($lessons as $index => $lesson): ?><a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center gap-3 px-4 py-3" href="<?= e(app_url('student/lesson.php?id=' . $lesson['id'])) ?>"><span><span class="text-secondary me-2"><?= $index + 1 ?>.</span><strong><?= e($lesson['title']) ?></strong><?php if ($lesson['duration_minutes'] !== null): ?><span class="small text-secondary ms-2"><?= (int)$lesson['duration_minutes'] ?> min</span><?php endif; ?></span><?php if ($lesson['is_completed']): ?><span class="badge text-bg-success">Completed</span><?php else: ?><span class="badge text-bg-light">Start</span><?php endif; ?></a><?php endforeach; ?></div></div></div>
        <div class="card"><div class="card-body"><h2 class="h5">Quizzes</h2><?php if (!$quizzes): ?><p class="text-secondary mb-0">No quizzes are available yet.</p><?php endif; ?><?php foreach ($quizzes as $quiz): ?><a class="d-flex justify-content-between align-items-center border-bottom py-3 text-decoration-none" href="<?= e(app_url('student/quiz.php?id=' . $quiz['id'])) ?>"><span><strong><?= e($quiz['title']) ?></strong><small class="d-block text-secondary"><?= e($quiz['lesson_title']) ?></small></span><span>&rarr;</span></a><?php endforeach; ?></div></div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
