CREATE DATABASE IF NOT EXISTS emma_elearning CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE emma_elearning;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(80) NOT NULL,
    last_name VARCHAR(80) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('student', 'admin') NOT NULL DEFAULT 'student',
    status ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
    last_login_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE student_profiles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL UNIQUE,
    avatar_path VARCHAR(255) NULL,
    bio TEXT NULL,
    total_xp INT UNSIGNED NOT NULL DEFAULT 0,
    total_points INT UNSIGNED NOT NULL DEFAULT 0,
    level INT UNSIGNED NOT NULL DEFAULT 1,
    current_streak INT UNSIGNED NOT NULL DEFAULT 0,
    longest_streak INT UNSIGNED NOT NULL DEFAULT 0,
    last_activity_date DATE NULL,
    CONSTRAINT fk_profile_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE courses (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    image_path VARCHAR(255) NULL,
    status ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'draft',
    created_by INT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_course_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE lessons (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_id INT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    content LONGTEXT NOT NULL,
    video_url VARCHAR(500) NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 1,
    duration_minutes SMALLINT UNSIGNED NULL,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_lesson_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE enrollments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    course_id INT UNSIGNED NOT NULL,
    enrolled_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME NULL,
    UNIQUE KEY uq_enrollment (user_id, course_id),
    CONSTRAINT fk_enrollment_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_enrollment_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE lesson_progress (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    lesson_id INT UNSIGNED NOT NULL,
    completed_at DATETIME NULL,
    UNIQUE KEY uq_lesson_progress (user_id, lesson_id),
    CONSTRAINT fk_progress_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_progress_lesson FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE quizzes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lesson_id INT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    description TEXT NULL,
    pass_percentage DECIMAL(5,2) NOT NULL DEFAULT 60.00,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    CONSTRAINT fk_quiz_lesson FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE questions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT UNSIGNED NOT NULL,
    question_text TEXT NOT NULL,
    points INT UNSIGNED NOT NULL DEFAULT 10,
    sort_order INT UNSIGNED NOT NULL DEFAULT 1,
    CONSTRAINT fk_question_quiz FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE question_options (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_id INT UNSIGNED NOT NULL,
    option_text VARCHAR(500) NOT NULL,
    is_correct BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT fk_option_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE quiz_attempts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    quiz_id INT UNSIGNED NOT NULL,
    score DECIMAL(8,2) NOT NULL DEFAULT 0,
    percentage DECIMAL(5,2) NOT NULL DEFAULT 0,
    passed BOOLEAN NOT NULL DEFAULT FALSE,
    started_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME NULL,
    CONSTRAINT fk_attempt_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_attempt_quiz FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE quiz_answers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT UNSIGNED NOT NULL,
    question_id INT UNSIGNED NOT NULL,
    option_id INT UNSIGNED NULL,
    is_correct BOOLEAN NOT NULL DEFAULT FALSE,
    points_awarded INT UNSIGNED NOT NULL DEFAULT 0,
    CONSTRAINT fk_answer_attempt FOREIGN KEY (attempt_id) REFERENCES quiz_attempts(id) ON DELETE CASCADE,
    CONSTRAINT fk_answer_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    CONSTRAINT fk_answer_option FOREIGN KEY (option_id) REFERENCES question_options(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE badges (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(500) NOT NULL,
    icon_path VARCHAR(255) NULL,
    criteria_type VARCHAR(80) NOT NULL,
    criteria_value INT UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE student_badges (
    user_id INT UNSIGNED NOT NULL,
    badge_id INT UNSIGNED NOT NULL,
    awarded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, badge_id),
    CONSTRAINT fk_student_badge_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_student_badge_badge FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE achievements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(500) NOT NULL,
    criteria_type VARCHAR(80) NOT NULL,
    xp_reward INT UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE student_achievements (
    user_id INT UNSIGNED NOT NULL,
    achievement_id INT UNSIGNED NOT NULL,
    completed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, achievement_id),
    CONSTRAINT fk_student_achievement_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_student_achievement_achievement FOREIGN KEY (achievement_id) REFERENCES achievements(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE xp_transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    amount INT NOT NULL,
    source VARCHAR(100) NOT NULL,
    reference_id INT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_xp_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE daily_activity (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    activity_date DATE NOT NULL,
    activity_type VARCHAR(80) NOT NULL DEFAULT 'login',
    UNIQUE KEY uq_daily_activity (user_id, activity_date, activity_type),
    CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    message TEXT NOT NULL,
    read_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notification_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
