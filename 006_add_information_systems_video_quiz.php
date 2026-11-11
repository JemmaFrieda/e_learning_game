<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';

$pdo = db();
$pdo->beginTransaction();
try {
    $lessonStatement = $pdo->prepare("SELECT l.id, l.title FROM lessons l INNER JOIN courses c ON c.id = l.course_id WHERE c.slug = 'human-computer-interaction' AND l.sort_order = 1 LIMIT 1");
    $lessonStatement->execute();
    $lesson = $lessonStatement->fetch();
    if (!$lesson) throw new RuntimeException('The introductory lesson was not found.');

    $video = $pdo->prepare('UPDATE lessons SET video_url = :video_url WHERE id = :lesson_id');
    $video->execute(['video_url' => 'https://www.youtube.com/watch?v=05zDouhy3Sk&list=PLPmcSYOBjl0LI-LW0Rlqp62Ev5vSAEaBf', 'lesson_id' => $lesson['id']]);

    $findQuiz = $pdo->prepare('SELECT id FROM quizzes WHERE lesson_id = :lesson_id AND title = :title LIMIT 1');
    $findQuiz->execute(['lesson_id' => $lesson['id'], 'title' => 'Introduction to Information Systems Quiz']);
    $quizId = (int)$findQuiz->fetchColumn();
    if (!$quizId) {
        $createQuiz = $pdo->prepare("INSERT INTO quizzes (lesson_id, title, description, pass_percentage, status) VALUES (:lesson_id, :title, :description, 60, 'published')");
        $createQuiz->execute(['lesson_id' => $lesson['id'], 'title' => 'Introduction to Information Systems Quiz', 'description' => 'Check your understanding of the introductory Information Systems video.']);
        $quizId = (int)$pdo->lastInsertId();
    }

    $questions = [
        ['What is an information system?', 'A combination of people, processes, technology, and data that produces useful information', 'A computer monitor only', 'A network cable only'],
        ['How can information systems support a business?', 'They support operations, communication, and decision making', 'They remove the need for business processes', 'They are used only for entertainment'],
        ['Which group contains common information system components?', 'Hardware, software, data, people, and procedures', 'Furniture, buildings, and vehicles only', 'Colors, fonts, and logos only'],
    ];
    $findQuestion = $pdo->prepare('SELECT id FROM questions WHERE quiz_id = :quiz_id AND question_text = :question_text LIMIT 1');
    $createQuestion = $pdo->prepare('INSERT INTO questions (quiz_id, question_text, points, sort_order) VALUES (:quiz_id, :question_text, 10, :sort_order)');
    $createOption = $pdo->prepare('INSERT INTO question_options (question_id, option_text, is_correct) VALUES (:question_id, :option_text, :is_correct)');
    foreach ($questions as $index => $question) {
        $findQuestion->execute(['quiz_id' => $quizId, 'question_text' => $question[0]]);
        if ($findQuestion->fetchColumn()) continue;
        $createQuestion->execute(['quiz_id' => $quizId, 'question_text' => $question[0], 'sort_order' => $index + 1]);
        $questionId = (int)$pdo->lastInsertId();
        foreach ([$question[1], $question[2], $question[3]] as $optionIndex => $optionText) $createOption->execute(['question_id' => $questionId, 'option_text' => $optionText, 'is_correct' => $optionIndex === 0 ? 1 : 0]);
    }
    $pdo->commit();
    echo "Information Systems video and quiz configured.\n";
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    fwrite(STDERR, "Migration failed. No changes were committed.\n");
    exit(1);
}
