<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';

function add_lesson_quiz(PDO $pdo, int $courseId, string $lessonTitle, string $content, array $questions): void
{
    $findLesson = $pdo->prepare('SELECT id FROM lessons WHERE course_id = :course_id AND title = :title LIMIT 1');
    $findLesson->execute(['course_id' => $courseId, 'title' => $lessonTitle]);
    $lessonId = (int)$findLesson->fetchColumn();
    if (!$lessonId) {
        $order = $pdo->prepare('SELECT COALESCE(MAX(sort_order), 0) + 1 FROM lessons WHERE course_id = :course_id');
        $order->execute(['course_id' => $courseId]);
        $createLesson = $pdo->prepare("INSERT INTO lessons (course_id, title, content, sort_order, duration_minutes, status) VALUES (:course_id, :title, :content, :sort_order, 35, 'published')");
        $createLesson->execute(['course_id' => $courseId, 'title' => $lessonTitle, 'content' => $content, 'sort_order' => $order->fetchColumn()]);
        $lessonId = (int)$pdo->lastInsertId();
    }
    $quizTitle = $lessonTitle . ' Quiz';
    $findQuiz = $pdo->prepare('SELECT id FROM quizzes WHERE lesson_id = :lesson_id AND title = :title LIMIT 1');
    $findQuiz->execute(['lesson_id' => $lessonId, 'title' => $quizTitle]);
    $quizId = (int)$findQuiz->fetchColumn();
    if (!$quizId) { $createQuiz = $pdo->prepare("INSERT INTO quizzes (lesson_id, title, description, pass_percentage, status) VALUES (:lesson_id, :title, :description, 60, 'published')"); $createQuiz->execute(['lesson_id' => $lessonId, 'title' => $quizTitle, 'description' => 'Test your understanding of ' . $lessonTitle . '.']); $quizId = (int)$pdo->lastInsertId(); }
    $findQuestion = $pdo->prepare('SELECT id FROM questions WHERE quiz_id = :quiz_id AND question_text = :question_text LIMIT 1'); $createQuestion = $pdo->prepare('INSERT INTO questions (quiz_id, question_text, points, sort_order) VALUES (:quiz_id, :question_text, 10, :sort_order)'); $createOption = $pdo->prepare('INSERT INTO question_options (question_id, option_text, is_correct) VALUES (:question_id, :option_text, :is_correct)');
    foreach ($questions as $index => $question) { $findQuestion->execute(['quiz_id' => $quizId, 'question_text' => $question[0]]); if ($findQuestion->fetchColumn()) continue; $createQuestion->execute(['quiz_id' => $quizId, 'question_text' => $question[0], 'sort_order' => $index + 1]); $questionId = (int)$pdo->lastInsertId(); foreach ([$question[1], $question[2], $question[3]] as $optionIndex => $option) $createOption->execute(['question_id' => $questionId, 'option_text' => $option, 'is_correct' => $optionIndex === 0 ? 1 : 0]); }
}

$pdo = db(); $pdo->beginTransaction();
try {
    $course = $pdo->query("SELECT id FROM courses WHERE slug = 'human-computer-interaction' LIMIT 1")->fetch(); if (!$course) throw new RuntimeException('Course not found.'); $courseId = (int)$course['id'];
    add_lesson_quiz($pdo, $courseId, 'Introduction to Programming', 'Programming is the process of solving problems by writing instructions that a computer can execute. This lesson introduces algorithms, source code, programming languages, variables, and clear problem-solving steps.', [
        ['What is programming?', 'Writing instructions that a computer can execute', 'Repairing a monitor', 'Connecting a printer only'], ['What is an algorithm?', 'A clear sequence of steps for solving a problem', 'A type of computer screen', 'A password storage device'], ['What is source code?', 'Human-readable instructions written in a programming language', 'A power cable', 'A computer chair'], ['What does a variable store?', 'A value that can be used and changed by a program', 'Only a monitor image', 'A physical keyboard'], ['Why are programming languages used?', 'To express instructions in a form people and computers can process', 'To remove all logic', 'To replace electricity'], ['What is debugging?', 'Finding and fixing problems in a program', 'Designing a logo', 'Deleting every program'], ['What is a programming statement?', 'An instruction that performs an action', 'A building floor', 'A network socket only'], ['Why should code be organized?', 'To make it easier to read, test, and maintain', 'To make errors invisible', 'To stop all users'], ['What is input in a program?', 'Data provided to a program', 'The final printed result only', 'A broken file'], ['What is output in a program?', 'The result produced after processing data', 'A programming language name', 'A keyboard key only']]);
    add_lesson_quiz($pdo, $courseId, 'Programming Fundamentals: Variables and Control Flow', 'Variables hold data and control flow determines which instructions run and how often. Conditions, loops, and functions help programmers build reusable and reliable solutions.', [
        ['What is a data type?', 'A description of the kind of value being stored', 'A monitor model', 'A user password'], ['What does an if statement do?', 'It runs instructions when a condition is true', 'It always repeats forever', 'It turns off a computer'], ['What is a loop used for?', 'Repeating instructions while a condition or sequence requires it', 'Creating a physical cable', 'Deleting variables'], ['What is a function?', 'A reusable block of code that performs a task', 'A computer battery', 'A screen resolution'], ['What is a Boolean value?', 'A value that is true or false', 'A value that is always text', 'A network address only'], ['Why validate input?', 'To check that data is acceptable before processing it', 'To prevent all data entry', 'To remove program logic'], ['What is an array commonly used for?', 'Storing a collection of values', 'Displaying only one pixel', 'Changing a keyboard'], ['What does a return statement do?', 'It sends a result back from a function', 'It deletes the function', 'It starts the computer'], ['Why use comments in code?', 'To explain code for people reading or maintaining it', 'To make code execute twice', 'To replace testing'], ['What is a syntax error?', 'A violation of the rules of a programming language', 'A hardware color', 'A successful quiz result']]);
    $pdo->commit(); echo "Programming lessons and quizzes configured.\n";
} catch (Throwable $exception) { if ($pdo->inTransaction()) $pdo->rollBack(); fwrite(STDERR, "Migration failed. No changes were committed.\n"); exit(1); }
