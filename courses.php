<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
require_role('student');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) $errors[] = 'Your session token expired. Please try again.';
    $courseId = filter_var($_POST['course_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (!$courseId) $errors[] = 'Invalid course selected.';
    if (!$errors) {
        $courseStatement = db()->prepare("SELECT id FROM courses WHERE id = :id AND status = 'published'");
        $courseStatement->execute(['id' => $courseId]);
        if (!$courseStatement->fetch()) $errors[] = 'That course is not available for enrollment.';
    }
    if (!$errors) {
        $statement = db()->prepare('INSERT INTO enrollments (user_id, course_id) VALUES (:user_id, :course_id) ON DUPLICATE KEY UPDATE enrolled_at = enrolled_at');
        $statement->execute(['user_id' => current_user()['id'], 'course_id' => $courseId]);
        set_flash('success', 'You are now enrolled in the course.');
        redirect('student/course.php?id=' . $courseId);
    }
}

$search = trim((string)($_GET['q'] ?? ''));
$myCourses = ($_GET['my'] ?? '') === '1';
$query = "SELECT c.id, c.title, c.slug, c.description, c.image_path, CASE WHEN e.id IS NULL THEN 0 ELSE 1 END AS is_enrolled, COUNT(l.id) AS lesson_count FROM courses c LEFT JOIN enrollments e ON e.course_id = c.id AND e.user_id = :user_id LEFT JOIN lessons l ON l.course_id = c.id AND l.status = 'published' WHERE c.status = 'published'";
$params = ['user_id' => current_user()['id']];
if ($search !== '') { $query .= ' AND (c.title LIKE :search OR c.description LIKE :search)'; $params['search'] = '%' . $search . '%'; }
if ($myCourses) $query .= ' AND e.id IS NOT NULL';
$query .= ' GROUP BY c.id, c.title, c.slug, c.description, c.image_path, e.id ORDER BY c.title';
$statement = db()->prepare($query);
$statement->execute($params);
$courses = $statement->fetchAll();
$pageTitle = 'Courses';
require __DIR__ . '/../includes/header.php';
?>
<div class="py-4"><div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><h1 class="h2 fw-bold mb-1"><?= $myCourses ? 'My courses' : 'Explore courses' ?></h1><p class="text-secondary mb-0">Choose a course and build your learning momentum.</p></div><div class="d-flex gap-2"><a class="btn btn-outline-primary" href="<?= e(app_url('student/courses.php?my=1')) ?>">My courses</a><a class="btn btn-outline-secondary" href="<?= e(app_url('student/courses.php')) ?>">All courses</a></div></div><form class="row g-2 mb-4" method="get"><div class="col"><input class="form-control" name="q" value="<?= e($search) ?>" placeholder="Search courses" aria-label="Search courses"></div><?php if ($myCourses): ?><input type="hidden" name="my" value="1"><?php endif; ?><div class="col-auto"><button class="btn btn-primary" type="submit">Search</button></div></form><?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?><div class="row g-4"><?php if (!$courses): ?><div class="col-12"><div class="card p-5 text-center"><p class="text-secondary mb-0">No matching courses are available.</p></div></div><?php endif; ?><?php foreach ($courses as $course): ?><div class="col-md-6 col-xl-4"><div class="card feature-card h-100"><div class="card-body p-4 d-flex flex-column"><div class="d-flex justify-content-between gap-2 mb-3"><span class="badge text-bg-light"><?= (int)$course['lesson_count'] ?> lessons</span><?php if ($course['is_enrolled']): ?><span class="badge text-bg-success">Enrolled</span><?php endif; ?></div><h2 class="h5"><?= e($course['title']) ?></h2><p class="text-secondary flex-grow-1"><?= e($course['description']) ?></p><a class="btn btn-outline-primary" href="<?= e(app_url('student/course.php?id=' . $course['id'])) ?>"><?= $course['is_enrolled'] ? 'Open course' : 'View course' ?></a></div></div></div><?php endforeach; ?></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
