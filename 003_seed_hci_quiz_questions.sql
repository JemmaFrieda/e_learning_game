USE emma_elearning;

INSERT INTO questions (quiz_id, question_text, points, sort_order)
SELECT q.id, 'What does Human Computer Interaction primarily study?', 10, 1
FROM quizzes q WHERE q.title = 'HCI Quiz 1'
AND NOT EXISTS (SELECT 1 FROM questions x WHERE x.quiz_id = q.id AND x.question_text = 'What does Human Computer Interaction primarily study?');

INSERT INTO questions (quiz_id, question_text, points, sort_order)
SELECT q.id, 'Which quality describes how easily users can complete tasks?', 10, 2
FROM quizzes q WHERE q.title = 'HCI Quiz 1'
AND NOT EXISTS (SELECT 1 FROM questions x WHERE x.quiz_id = q.id AND x.question_text = 'Which quality describes how easily users can complete tasks?');

INSERT INTO questions (quiz_id, question_text, points, sort_order)
SELECT q.id, 'Which item is part of User Interface design?', 10, 3
FROM quizzes q WHERE q.title = 'HCI Quiz 1'
AND NOT EXISTS (SELECT 1 FROM questions x WHERE x.quiz_id = q.id AND x.question_text = 'Which item is part of User Interface design?');

INSERT INTO questions (quiz_id, question_text, points, sort_order)
SELECT q.id, 'What is the main focus of User Experience design?', 10, 4
FROM quizzes q WHERE q.title = 'HCI Quiz 1'
AND NOT EXISTS (SELECT 1 FROM questions x WHERE x.quiz_id = q.id AND x.question_text = 'What is the main focus of User Experience design?');

INSERT INTO questions (quiz_id, question_text, points, sort_order)
SELECT q.id, 'What is a key practice in user-centered design?', 10, 5
FROM quizzes q WHERE q.title = 'HCI Quiz 1'
AND NOT EXISTS (SELECT 1 FROM questions x WHERE x.quiz_id = q.id AND x.question_text = 'What is a key practice in user-centered design?');

INSERT INTO questions (quiz_id, question_text, points, sort_order)
SELECT q.id, 'Interaction design defines how users and systems do what?', 10, 6
FROM quizzes q WHERE q.title = 'HCI Quiz 1'
AND NOT EXISTS (SELECT 1 FROM questions x WHERE x.quiz_id = q.id AND x.question_text = 'Interaction design defines how users and systems do what?');

INSERT INTO questions (quiz_id, question_text, points, sort_order)
SELECT q.id, 'Human factors consider which aspect of system use?', 10, 7
FROM quizzes q WHERE q.title = 'HCI Quiz 1'
AND NOT EXISTS (SELECT 1 FROM questions x WHERE x.quiz_id = q.id AND x.question_text = 'Human factors consider which aspect of system use?');

INSERT INTO questions (quiz_id, question_text, points, sort_order)
SELECT q.id, 'Why is feedback important in an interface?', 10, 8
FROM quizzes q WHERE q.title = 'HCI Quiz 1'
AND NOT EXISTS (SELECT 1 FROM questions x WHERE x.quiz_id = q.id AND x.question_text = 'Why is feedback important in an interface?');

INSERT INTO questions (quiz_id, question_text, points, sort_order)
SELECT q.id, 'What is the purpose of usability testing?', 10, 9
FROM quizzes q WHERE q.title = 'HCI Quiz 1'
AND NOT EXISTS (SELECT 1 FROM questions x WHERE x.quiz_id = q.id AND x.question_text = 'What is the purpose of usability testing?');

INSERT INTO questions (quiz_id, question_text, points, sort_order)
SELECT q.id, 'Which design approach improves accessibility?', 10, 10
FROM quizzes q WHERE q.title = 'HCI Quiz 1'
AND NOT EXISTS (SELECT 1 FROM questions x WHERE x.quiz_id = q.id AND x.question_text = 'Which design approach improves accessibility?');

INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'How people interact with computer systems', 1 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='What does Human Computer Interaction primarily study?' AND NOT EXISTS (SELECT 1 FROM question_options o WHERE o.question_id=x.id);
INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'The speed of a computer processor', 0 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='What does Human Computer Interaction primarily study?';
INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'The cost of internet service', 0 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='What does Human Computer Interaction primarily study?';

INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'Usability', 1 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='Which quality describes how easily users can complete tasks?' AND NOT EXISTS (SELECT 1 FROM question_options o WHERE o.question_id=x.id);
INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'File size', 0 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='Which quality describes how easily users can complete tasks?';
INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'Processor temperature', 0 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='Which quality describes how easily users can complete tasks?';

INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'Layout, typography, and controls', 1 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='Which item is part of User Interface design?' AND NOT EXISTS (SELECT 1 FROM question_options o WHERE o.question_id=x.id);
INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'Database server hardware only', 0 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='Which item is part of User Interface design?';
INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'Network cable length', 0 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='Which item is part of User Interface design?';

INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'The complete experience of using a product', 1 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='What is the main focus of User Experience design?' AND NOT EXISTS (SELECT 1 FROM question_options o WHERE o.question_id=x.id);
INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'Only the product logo', 0 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='What is the main focus of User Experience design?';
INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'Only the database structure', 0 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='What is the main focus of User Experience design?';

INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'Testing designs with real users', 1 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='What is a key practice in user-centered design?' AND NOT EXISTS (SELECT 1 FROM question_options o WHERE o.question_id=x.id);
INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'Avoiding all user research', 0 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='What is a key practice in user-centered design?';
INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'Designing without feedback', 0 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='What is a key practice in user-centered design?';

INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'Communicate through actions and responses', 1 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='Interaction design defines how users and systems do what?' AND NOT EXISTS (SELECT 1 FROM question_options o WHERE o.question_id=x.id);
INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'Share electrical power only', 0 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='Interaction design defines how users and systems do what?';
INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'Replace all human decisions', 0 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='Interaction design defines how users and systems do what?';

INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'Human abilities, limitations, and environment', 1 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='Human factors consider which aspect of system use?' AND NOT EXISTS (SELECT 1 FROM question_options o WHERE o.question_id=x.id);
INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'Only the company budget', 0 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='Human factors consider which aspect of system use?';
INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'Only the application file name', 0 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='Human factors consider which aspect of system use?';

INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'It tells users what happened after an action', 1 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='Why is feedback important in an interface?' AND NOT EXISTS (SELECT 1 FROM question_options o WHERE o.question_id=x.id);
INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'It hides system status', 0 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='Why is feedback important in an interface?';
INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'It prevents all interaction', 0 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='Why is feedback important in an interface?';

INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'To observe users completing tasks and find problems', 1 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='What is the purpose of usability testing?' AND NOT EXISTS (SELECT 1 FROM question_options o WHERE o.question_id=x.id);
INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'To measure processor speed only', 0 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='What is the purpose of usability testing?';
INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'To remove all user involvement', 0 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='What is the purpose of usability testing?';

INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'Using inclusive and accessible design practices', 1 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='Which design approach improves accessibility?' AND NOT EXISTS (SELECT 1 FROM question_options o WHERE o.question_id=x.id);
INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'Using very small text for every user', 0 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='Which design approach improves accessibility?';
INSERT INTO question_options (question_id, option_text, is_correct)
SELECT x.id, 'Removing keyboard support', 0 FROM questions x INNER JOIN quizzes q ON q.id=x.quiz_id WHERE q.title='HCI Quiz 1' AND x.question_text='Which design approach improves accessibility?';
