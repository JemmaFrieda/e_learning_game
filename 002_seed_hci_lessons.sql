USE emma_elearning;

INSERT INTO lessons (course_id, title, content, sort_order, duration_minutes, status)
SELECT c.id, 'Introduction to Human Computer Interaction',
'Human Computer Interaction (HCI) studies how people interact with digital products and computer systems. HCI combines computing, psychology, design, and research to create systems that are useful, understandable, and accessible. In this course, we will examine users, interfaces, interaction patterns, usability, and user experience.',
1, 30, 'published'
FROM courses c
WHERE c.slug = 'human-computer-interaction'
  AND NOT EXISTS (SELECT 1 FROM lessons l WHERE l.course_id = c.id AND l.title = 'Introduction to Human Computer Interaction');

INSERT INTO lessons (course_id, title, content, sort_order, duration_minutes, status)
SELECT c.id, 'Human Computer Interaction Fundamentals',
'The fundamentals of HCI include understanding users, tasks, context, feedback, consistency, affordances, and system limitations. A good interaction lets users predict what will happen, recover from mistakes, and complete tasks with reasonable effort. These principles guide every stage of interface design.',
2, 35, 'published'
FROM courses c
WHERE c.slug = 'human-computer-interaction'
  AND NOT EXISTS (SELECT 1 FROM lessons l WHERE l.course_id = c.id AND l.title = 'Human Computer Interaction Fundamentals');

INSERT INTO lessons (course_id, title, content, sort_order, duration_minutes, status)
SELECT c.id, 'User Interface Design',
'User Interface design focuses on the visual and interactive parts of a product. Layout, typography, color, spacing, navigation, controls, and responsive behavior should work together to communicate hierarchy and support user tasks. Effective interfaces are clear, consistent, and accessible across screen sizes.',
3, 40, 'published'
FROM courses c
WHERE c.slug = 'human-computer-interaction'
  AND NOT EXISTS (SELECT 1 FROM lessons l WHERE l.course_id = c.id AND l.title = 'User Interface Design');

INSERT INTO lessons (course_id, title, content, sort_order, duration_minutes, status)
SELECT c.id, 'User Experience (UX) Design',
'User Experience design considers the complete experience before, during, and after using a product. UX work includes research, journey mapping, information architecture, prototyping, testing, and iteration. The goal is to make the product useful, usable, valuable, and satisfying for its intended users.',
4, 40, 'published'
FROM courses c
WHERE c.slug = 'human-computer-interaction'
  AND NOT EXISTS (SELECT 1 FROM lessons l WHERE l.course_id = c.id AND l.title = 'User Experience (UX) Design');

INSERT INTO lessons (course_id, title, content, sort_order, duration_minutes, status)
SELECT c.id, 'Usability and User-Centered Design',
'Usability measures how effectively, efficiently, and satisfactorily users can complete tasks. User-centered design places users and their goals at the center of the process through research, early testing, inclusive design, and continuous improvement. Evidence from real users is more reliable than assumptions alone.',
5, 35, 'published'
FROM courses c
WHERE c.slug = 'human-computer-interaction'
  AND NOT EXISTS (SELECT 1 FROM lessons l WHERE l.course_id = c.id AND l.title = 'Usability and User-Centered Design');

INSERT INTO lessons (course_id, title, content, sort_order, duration_minutes, status)
SELECT c.id, 'Interaction Design',
'Interaction design defines how users and systems communicate through actions and responses. Designers plan flows, states, controls, feedback, error handling, and transitions. Strong interaction design makes system behavior predictable and helps users accomplish goals without unnecessary complexity.',
6, 35, 'published'
FROM courses c
WHERE c.slug = 'human-computer-interaction'
  AND NOT EXISTS (SELECT 1 FROM lessons l WHERE l.course_id = c.id AND l.title = 'Interaction Design');

INSERT INTO lessons (course_id, title, content, sort_order, duration_minutes, status)
SELECT c.id, 'Human Factors and Ergonomics',
'Human factors and ergonomics examine how human abilities, limitations, environments, and physical conditions affect interaction. Designers consider vision, motor skills, attention, memory, workload, posture, device size, and accessibility. Applying these considerations produces safer, more comfortable, and more inclusive systems.',
7, 35, 'published'
FROM courses c
WHERE c.slug = 'human-computer-interaction'
  AND NOT EXISTS (SELECT 1 FROM lessons l WHERE l.course_id = c.id AND l.title = 'Human Factors and Ergonomics');
