<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
require_role('student');

$lessonId = filter_var($_GET['id'] ?? $_POST['lesson_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$lessonId) { set_flash('danger', 'Invalid lesson selected.'); redirect('student/courses.php'); }
$statement = db()->prepare("SELECT l.id, l.course_id, l.title, l.content, l.video_url, l.duration_minutes, l.sort_order, c.title AS course_title FROM lessons l INNER JOIN courses c ON c.id = l.course_id INNER JOIN enrollments e ON e.course_id = l.course_id AND e.user_id = :user_id WHERE l.id = :lesson_id AND l.status = 'published' AND c.status = 'published'");
$statement->execute(['user_id' => current_user()['id'], 'lesson_id' => $lessonId]);
$lesson = $statement->fetch();
if (!$lesson) { set_flash('danger', 'Enroll in this course to access the lesson.'); redirect('student/courses.php'); }
$progressCheck = db()->prepare('SELECT completed_at FROM lesson_progress WHERE user_id = :user_id AND lesson_id = :lesson_id');
$progressCheck->execute(['user_id' => current_user()['id'], 'lesson_id' => $lessonId]);
$alreadyCompleted = (bool)$progressCheck->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) { set_flash('danger', 'Your session token expired. Please try again.'); redirect('student/lesson.php?id=' . $lessonId); }
    $progressStatement = db()->prepare('INSERT INTO lesson_progress (user_id, lesson_id, completed_at) VALUES (:user_id, :lesson_id, CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE completed_at = COALESCE(completed_at, CURRENT_TIMESTAMP)');
    $progressStatement->execute(['user_id' => current_user()['id'], 'lesson_id' => $lessonId]);
    if (!$alreadyCompleted) { record_daily_activity((int)current_user()['id']); award_xp((int)current_user()['id'], 10, 'lesson_complete', $lessonId); }
    $counts = db()->prepare("SELECT COUNT(*) AS total, SUM(CASE WHEN lp.completed_at IS NOT NULL THEN 1 ELSE 0 END) AS completed FROM lessons l LEFT JOIN lesson_progress lp ON lp.lesson_id = l.id AND lp.user_id = :user_id WHERE l.course_id = :course_id AND l.status = 'published'");
    $counts->execute(['user_id' => current_user()['id'], 'course_id' => $lesson['course_id']]);
    $courseProgress = $counts->fetch();
    if ((int)$courseProgress['total'] > 0 && (int)$courseProgress['total'] === (int)$courseProgress['completed']) { $complete = db()->prepare('UPDATE enrollments SET completed_at = COALESCE(completed_at, CURRENT_TIMESTAMP) WHERE user_id = :user_id AND course_id = :course_id'); $complete->execute(['user_id' => current_user()['id'], 'course_id' => $lesson['course_id']]); if ($complete->rowCount() > 0) { award_xp((int)current_user()['id'], 50, 'course_complete', (int)$lesson['course_id']); notify_user((int)current_user()['id'], 'Course completed', 'You completed ' . $lesson['course_title'] . '.'); } }
    sync_achievements_and_badges((int)current_user()['id']);
    set_flash('success', 'Lesson marked as complete.');
    redirect('student/course.php?id=' . $lesson['course_id']);
}
$pageTitle = $lesson['title'];
$videoUrl = youtube_embed_url($lesson['video_url'] ?? null);
$navigation = db()->prepare("SELECT id, title FROM lessons WHERE course_id = :course_id AND status = 'published' AND sort_order < :sort_order ORDER BY sort_order DESC, id DESC LIMIT 1");
$navigation->execute(['course_id' => $lesson['course_id'], 'sort_order' => $lesson['sort_order'] ?? 999999]);
$previousLesson = $navigation->fetch();
$navigation = db()->prepare("SELECT id, title FROM lessons WHERE course_id = :course_id AND status = 'published' AND sort_order > :sort_order ORDER BY sort_order, id LIMIT 1");
$navigation->execute(['course_id' => $lesson['course_id'], 'sort_order' => $lesson['sort_order'] ?? 0]);
$nextLesson = $navigation->fetch();
require __DIR__ . '/../includes/header.php';
?>
<div class="py-4"><a class="text-decoration-none" href="<?= e(app_url('student/course.php?id=' . $lesson['course_id'])) ?>">&larr; <?= e($lesson['course_title']) ?></a><article class="card mt-3"><div class="card-body p-4 p-md-5"><div class="d-flex flex-wrap justify-content-between gap-2 mb-4"><h1 class="h2 fw-bold mb-0"><?= e($lesson['title']) ?></h1><?php if ($lesson['duration_minutes'] !== null): ?><span class="badge text-bg-light align-self-start"><?= (int)$lesson['duration_minutes'] ?> minutes</span><?php endif; ?></div><?php if ($videoUrl): ?><div class="ratio ratio-16x9 rounded overflow-hidden mb-4"><iframe src="<?= e($videoUrl) ?>" title="<?= e($lesson['title']) ?> video" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div><?php endif; ?><div class="lesson-content" style="white-space: pre-line;"><?= e($lesson['content']) ?></div><form method="post" class="mt-5"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="lesson_id" value="<?= (int)$lessonId ?>"><button class="btn btn-primary" type="submit" <?= $alreadyCompleted ? 'disabled' : '' ?>><?= $alreadyCompleted ? 'Lesson completed' : 'Mark lesson complete' ?></button></form><div class="d-flex justify-content-between gap-3 mt-4"><div><?php if ($previousLesson): ?><a class="btn btn-outline-secondary" href="<?= e(app_url('student/lesson.php?id=' . $previousLesson['id'])) ?>">&larr; Previous</a><?php endif; ?></div><div><?php if ($nextLesson): ?><a class="btn btn-outline-primary" href="<?= e(app_url('student/lesson.php?id=' . $nextLesson['id'])) ?>">Next &rarr;</a><?php endif; ?></div></div></div></article></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
