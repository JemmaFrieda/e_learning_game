<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';

$pdo = db();
$pdo->beginTransaction();
try {
    $course = $pdo->query("SELECT id FROM courses WHERE slug = 'human-computer-interaction' LIMIT 1")->fetch();
    if (!$course) throw new RuntimeException('The existing course was not found.');

    $findLesson = $pdo->prepare('SELECT id FROM lessons WHERE course_id = :course_id AND title = :title LIMIT 1');
    $findLesson->execute(['course_id' => $course['id'], 'title' => 'Number Bases - Introduction, Types, Operations and Conversion']);
    $lessonId = (int)$findLesson->fetchColumn();
    if (!$lessonId) {
        $maxOrder = $pdo->prepare('SELECT COALESCE(MAX(sort_order), 0) + 1 FROM lessons WHERE course_id = :course_id');
        $maxOrder->execute(['course_id' => $course['id']]);
        $createLesson = $pdo->prepare("INSERT INTO lessons (course_id, title, content, video_url, sort_order, duration_minutes, status) VALUES (:course_id, :title, :content, :video_url, :sort_order, 35, 'published')");
        $createLesson->execute(['course_id' => $course['id'], 'title' => 'Number Bases - Introduction, Types, Operations and Conversion', 'content' => 'This lesson introduces number bases, including decimal, binary, octal, and hexadecimal systems. Study the video, then complete the quiz to practise conversions and basic operations.', 'video_url' => 'https://www.youtube.com/watch?v=QpZIUDBMhg8&list=PLxjh_hednj-75i4Cnzdo92VjHAPN9L5zO&index=3', 'sort_order' => $maxOrder->fetchColumn()]);
        $lessonId = (int)$pdo->lastInsertId();
    }

    $findQuiz = $pdo->prepare('SELECT id FROM quizzes WHERE lesson_id = :lesson_id AND title = :title LIMIT 1');
    $findQuiz->execute(['lesson_id' => $lessonId, 'title' => 'Number Bases Quiz']);
    $quizId = (int)$findQuiz->fetchColumn();
    if (!$quizId) {
        $createQuiz = $pdo->prepare("INSERT INTO quizzes (lesson_id, title, description, pass_percentage, status) VALUES (:lesson_id, 'Number Bases Quiz', 'Test your understanding of number bases and conversion operations.', 60, 'published')");
        $createQuiz->execute(['lesson_id' => $lessonId]);
        $quizId = (int)$pdo->lastInsertId();
    }

    $questions = [
        ['Which number system uses base 10?', 'Decimal', 'Binary', 'Hexadecimal'],
        ['Which digits are used in the binary number system?', '0 and 1', '1 and 2', '0 through 9'],
        ['What is the decimal value of binary 1010?', '10', '8', '12'],
        ['What is the binary representation of decimal 5?', '101', '110', '111'],
        ['Which prefix is commonly used for hexadecimal notation?', '0x', '0b', '0d'],
        ['What is the decimal value of hexadecimal A?', '10', '11', '16'],
        ['How many different symbols are used in the octal system?', '8', '10', '16'],
        ['What is binary 1111 in hexadecimal?', 'F', 'E', '10'],
        ['What is the result of binary 0011 + 0001?', '0100', '0010', '0111'],
        ['Why are number-base conversions useful in computing?', 'They help represent and interpret data in different forms', 'They remove the need for algorithms', 'They make every number decimal'],
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
    echo "Number Bases lesson and quiz configured.\n";
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    fwrite(STDERR, "Migration failed. No changes were committed.\n");
    exit(1);
}
