<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';

$pdo = db();
$pdo->beginTransaction();
try {
    $course = $pdo->query("SELECT id FROM courses WHERE slug = 'human-computer-interaction' LIMIT 1")->fetch();
    if (!$course) throw new RuntimeException('The existing course was not found.');
    $title = 'Information Communication Technology';
    $find = $pdo->prepare('SELECT id FROM lessons WHERE course_id = :course_id AND title = :title LIMIT 1');
    $find->execute(['course_id' => $course['id'], 'title' => $title]);
    $lessonId = (int)$find->fetchColumn();
    $video = 'https://www.youtube.com/watch?v=n1_Ez2h2-_k&list=PLYsKWjcnp5DJS9zW6UsjSahmd-mPUh-Js';
    if ($lessonId) {
        $update = $pdo->prepare('UPDATE lessons SET video_url = :video_url WHERE id = :id');
        $update->execute(['video_url' => $video, 'id' => $lessonId]);
    } else {
        $order = $pdo->prepare('SELECT COALESCE(MAX(sort_order), 0) + 1 FROM lessons WHERE course_id = :course_id');
        $order->execute(['course_id' => $course['id']]);
        $insert = $pdo->prepare("INSERT INTO lessons (course_id, title, content, video_url, sort_order, duration_minutes, status) VALUES (:course_id, :title, :content, :video_url, :sort_order, 30, 'published')");
        $insert->execute(['course_id' => $course['id'], 'title' => $title, 'content' => 'This lesson introduces Information Communication Technology and its role in communication, education, business, and modern digital services.', 'video_url' => $video, 'sort_order' => $order->fetchColumn()]);
        $lessonId = (int)$pdo->lastInsertId();
    }
    $pdo->commit();
    echo "ICT video lesson configured: {$lessonId}\n";
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    fwrite(STDERR, "Migration failed. No changes were committed.\n");
    exit(1);
}
