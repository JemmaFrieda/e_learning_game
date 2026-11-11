<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';

$questionSets = [
    1 => [
        ['What is the main goal of HCI?', 'Make human-computer interaction useful and understandable', 'Increase computer hardware weight', 'Remove users from the design process'],
        ['Which disciplines commonly contribute to HCI?', 'Computing, psychology, and design', 'Only accounting', 'Only network administration'],
        ['Why should HCI designers study users?', 'To understand goals, needs, and limitations', 'To avoid testing the product', 'To choose a server password'],
    ],
    2 => [
        ['What does consistency help users do?', 'Predict how controls and screens will behave', 'Install more memory', 'Change the operating system kernel'],
        ['What is an affordance in interface design?', 'A clue about how an element can be used', 'A database backup', 'A type of network cable'],
        ['Why is system feedback important?', 'It tells users what happened after an action', 'It hides system status', 'It removes navigation'],
    ],
    3 => [
        ['Which is an element of user interface design?', 'Typography, spacing, and navigation', 'Database replication only', 'Processor voltage'],
        ['Why is visual hierarchy used?', 'To show the relative importance of content', 'To make every element identical', 'To prevent users from reading'],
        ['What should responsive interface design support?', 'Usable layouts on different screen sizes', 'Desktop screens only', 'One fixed font size everywhere'],
    ],
    4 => [
        ['What does UX design consider?', 'The complete experience of using a product', 'Only the company logo', 'Only the source code length'],
        ['What is a user journey map used for?', 'Understanding steps and experiences across a task', 'Measuring CPU temperature', 'Replacing all user research'],
        ['Why are prototypes useful in UX design?', 'They allow ideas to be explored and tested early', 'They guarantee a product needs no testing', 'They replace all requirements'],
    ],
    5 => [
        ['What does usability measure?', 'How effectively and easily users complete tasks', 'The cost of computer hardware', 'The number of database tables'],
        ['What is user-centered design based on?', 'User goals, research, testing, and iteration', 'Developer assumptions only', 'Avoiding user feedback'],
        ['What is a useful result of usability testing?', 'Evidence about problems users encounter', 'A guarantee that every user is identical', 'A replacement for accessibility'],
    ],
    6 => [
        ['What does interaction design define?', 'How users and systems communicate through actions and responses', 'How servers are physically assembled', 'How files are compressed'],
        ['What is an interaction flow?', 'The sequence of steps a user takes to complete a task', 'A database password', 'A color palette only'],
        ['Why should errors be handled clearly?', 'Users need to understand and recover from problems', 'Errors should always be hidden', 'Errors should delete user data'],
    ],
    7 => [
        ['What do human factors consider?', 'Human abilities, limitations, and environment', 'Only application branding', 'Only internet speed'],
        ['Which factor can affect physical interaction?', 'Posture, motor skills, and device size', 'File naming conventions', 'Database table order'],
        ['What is an accessibility benefit?', 'More people can use the system successfully', 'The interface becomes impossible to test', 'Only administrators can use it'],
    ],
];

$pdo = db();
$pdo->beginTransaction();
try {
    $lessons = $pdo->query("SELECT l.id, l.sort_order, l.title FROM lessons l INNER JOIN courses c ON c.id = l.course_id WHERE c.slug = 'human-computer-interaction' ORDER BY l.sort_order, l.id")->fetchAll();
    $findQuiz = $pdo->prepare('SELECT id FROM quizzes WHERE lesson_id = :lesson_id ORDER BY id LIMIT 1');
    $createQuiz = $pdo->prepare("INSERT INTO quizzes (lesson_id, title, description, pass_percentage, status) VALUES (:lesson_id, :title, :description, 60, 'published')");
    $findQuestion = $pdo->prepare('SELECT id FROM questions WHERE quiz_id = :quiz_id AND question_text = :question_text LIMIT 1');
    $createQuestion = $pdo->prepare('INSERT INTO questions (quiz_id, question_text, points, sort_order) VALUES (:quiz_id, :question_text, 10, :sort_order)');
    $createOption = $pdo->prepare('INSERT INTO question_options (question_id, option_text, is_correct) VALUES (:question_id, :option_text, :is_correct)');

    foreach ($lessons as $lesson) {
        $findQuiz->execute(['lesson_id' => $lesson['id']]);
        $quizId = (int)$findQuiz->fetchColumn();
        if (!$quizId) {
            $createQuiz->execute(['lesson_id' => $lesson['id'], 'title' => 'HCI Quiz - Lesson ' . $lesson['sort_order'], 'description' => 'Three questions about ' . $lesson['title'] . '.']);
            $quizId = (int)$pdo->lastInsertId();
        }
        foreach ($questionSets[(int)$lesson['sort_order']] ?? [] as $index => $question) {
            $questionText = 'Lesson ' . $lesson['sort_order'] . ': ' . $question[0];
            $findQuestion->execute(['quiz_id' => $quizId, 'question_text' => $questionText]);
            if ($findQuestion->fetchColumn()) continue;
            $createQuestion->execute(['quiz_id' => $quizId, 'question_text' => $questionText, 'sort_order' => $index + 1]);
            $questionId = (int)$pdo->lastInsertId();
            foreach ([$question[1], $question[2], $question[3]] as $optionIndex => $optionText) {
                $createOption->execute(['question_id' => $questionId, 'option_text' => $optionText, 'is_correct' => $optionIndex === 0 ? 1 : 0]);
            }
        }
    }
    $pdo->commit();
    echo "Three questions per HCI lesson were added.\n";
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    fwrite(STDERR, "Migration failed. No changes were committed.\n");
    exit(1);
}
