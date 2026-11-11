USE emma_elearning;

ALTER TABLE achievements ADD COLUMN IF NOT EXISTS criteria_type VARCHAR(80) NOT NULL DEFAULT '' AFTER description;

INSERT IGNORE INTO achievements (name, description, criteria_type, xp_reward) VALUES
('First Lesson', 'Complete your first lesson.', 'first_lesson', 10),
('First Quiz', 'Complete your first quiz.', 'first_quiz', 15),
('Quiz Master', 'Pass five quizzes.', 'quiz_master', 50),
('Course Completed', 'Complete your first course.', 'course_completed', 50),
('7 Day Streak', 'Learn for seven consecutive days.', 'streak_7', 35),
('100 XP', 'Earn 100 XP.', 'xp_100', 25),
('500 XP', 'Earn 500 XP.', 'xp_500', 75);

INSERT IGNORE INTO badges (name, description, criteria_type, criteria_value) VALUES
('Lesson Starter', 'Complete one lesson.', 'lessons', 1),
('Quiz Champion', 'Pass five quizzes.', 'quizzes_passed', 5),
('Course Finisher', 'Complete one course.', 'courses', 1),
('Streak Keeper', 'Reach a seven-day streak.', 'streak', 7),
('XP Collector', 'Earn 500 XP.', 'xp', 500);
