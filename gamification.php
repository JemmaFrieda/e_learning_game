<?php
declare(strict_types=1);

function notify_user(int $userId, string $title, string $message): void
{
    $statement = db()->prepare('INSERT INTO notifications (user_id, title, message) VALUES (:user_id, :title, :message)');
    $statement->execute(['user_id' => $userId, 'title' => $title, 'message' => $message]);
}

function award_xp(int $userId, int $amount, string $source, ?int $referenceId = null): bool
{
    if ($amount <= 0) return false;
    $existing = db()->prepare('SELECT id FROM xp_transactions WHERE user_id = :user_id AND source = :source AND ((reference_id = :reference_id_value) OR (reference_id IS NULL AND :reference_id_null IS NULL)) LIMIT 1');
    $existing->execute(['user_id' => $userId, 'source' => $source, 'reference_id_value' => $referenceId, 'reference_id_null' => $referenceId]);
    if ($existing->fetch()) return false;

    $insert = db()->prepare('INSERT INTO xp_transactions (user_id, amount, source, reference_id) VALUES (:user_id, :amount, :source, :reference_id)');
    $insert->execute(['user_id' => $userId, 'amount' => $amount, 'source' => $source, 'reference_id' => $referenceId]);
    $profile = db()->prepare('SELECT total_xp, level FROM student_profiles WHERE user_id = :user_id');
    $profile->execute(['user_id' => $userId]);
    $before = $profile->fetch() ?: ['total_xp' => 0, 'level' => 1];
    $totalXp = (int)$before['total_xp'] + $amount;
    $newLevel = (int)floor($totalXp / 100) + 1;
    $update = db()->prepare('UPDATE student_profiles SET total_xp = :total_xp, total_points = total_points + :points, level = :level WHERE user_id = :user_id');
    $update->execute(['total_xp' => $totalXp, 'points' => $amount, 'level' => $newLevel, 'user_id' => $userId]);
    if ($newLevel > (int)$before['level']) notify_user($userId, 'Level up!', 'You reached Level ' . $newLevel . '. Keep learning to reach the next level.');
    return true;
}

function record_daily_activity(int $userId): void
{
    $today = date('Y-m-d');
    $activity = db()->prepare('INSERT IGNORE INTO daily_activity (user_id, activity_date, activity_type) VALUES (:user_id, :activity_date, :activity_type)');
    $activity->execute(['user_id' => $userId, 'activity_date' => $today, 'activity_type' => 'learning']);
    $activityId = $activity->rowCount() > 0 ? (int)db()->lastInsertId() : 0;
    $profileStatement = db()->prepare('SELECT current_streak, longest_streak, last_activity_date FROM student_profiles WHERE user_id = :user_id');
    $profileStatement->execute(['user_id' => $userId]);
    $profile = $profileStatement->fetch();
    if (!$profile || $profile['last_activity_date'] === $today) return;
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $streak = $profile['last_activity_date'] === $yesterday ? (int)$profile['current_streak'] + 1 : 1;
    $longest = max((int)$profile['longest_streak'], $streak);
    $update = db()->prepare('UPDATE student_profiles SET current_streak = :streak, longest_streak = :longest, last_activity_date = :activity_date WHERE user_id = :user_id');
    $update->execute(['streak' => $streak, 'longest' => $longest, 'activity_date' => $today, 'user_id' => $userId]);
    if ($activityId > 0) award_xp($userId, 5, 'daily_activity', $activityId);
}

function sync_achievements_and_badges(int $userId): void
{
    $profileStatement = db()->prepare('SELECT total_xp, current_streak FROM student_profiles WHERE user_id = :user_id');
    $profileStatement->execute(['user_id' => $userId]);
    $profile = $profileStatement->fetch() ?: ['total_xp' => 0, 'current_streak' => 0];
    $counts = [];
    foreach (['lessons' => 'SELECT COUNT(*) FROM lesson_progress WHERE user_id = :user_id AND completed_at IS NOT NULL', 'quizzes' => 'SELECT COUNT(*) FROM quiz_attempts WHERE user_id = :user_id AND completed_at IS NOT NULL', 'passed_quizzes' => 'SELECT COUNT(*) FROM quiz_attempts WHERE user_id = :user_id AND passed = 1', 'courses' => 'SELECT COUNT(*) FROM enrollments WHERE user_id = :user_id AND completed_at IS NOT NULL'] as $key => $query) { $statement = db()->prepare($query); $statement->execute(['user_id' => $userId]); $counts[$key] = (int)$statement->fetchColumn(); }
    $conditions = ['first_lesson' => $counts['lessons'] >= 1, 'first_quiz' => $counts['quizzes'] >= 1, 'quiz_master' => $counts['passed_quizzes'] >= 5, 'course_completed' => $counts['courses'] >= 1, 'streak_7' => (int)$profile['current_streak'] >= 7, 'xp_100' => (int)$profile['total_xp'] >= 100, 'xp_500' => (int)$profile['total_xp'] >= 500];
    $achievementStatement = db()->prepare('SELECT id, name, criteria_type, xp_reward FROM achievements');
    $achievementStatement->execute();
    foreach ($achievementStatement->fetchAll() as $achievement) {
        if (empty($conditions[$achievement['criteria_type']])) continue;
        $insert = db()->prepare('INSERT IGNORE INTO student_achievements (user_id, achievement_id) VALUES (:user_id, :achievement_id)');
        $insert->execute(['user_id' => $userId, 'achievement_id' => $achievement['id']]);
        if ($insert->rowCount() > 0) { if ((int)$achievement['xp_reward'] > 0) award_xp($userId, (int)$achievement['xp_reward'], 'achievement', (int)$achievement['id']); notify_user($userId, 'Achievement unlocked', $achievement['name'] . ' achievement unlocked.'); }
    }
    $badgeStatement = db()->prepare('SELECT id, name, criteria_type, criteria_value FROM badges');
    $badgeStatement->execute();
    foreach ($badgeStatement->fetchAll() as $badge) {
        $value = match ($badge['criteria_type']) { 'lessons' => $counts['lessons'], 'quizzes_passed' => $counts['passed_quizzes'], 'courses' => $counts['courses'], 'streak' => (int)$profile['current_streak'], 'xp' => (int)$profile['total_xp'], default => 0 };
        if ($value < (int)$badge['criteria_value']) continue;
        $insert = db()->prepare('INSERT IGNORE INTO student_badges (user_id, badge_id) VALUES (:user_id, :badge_id)');
        $insert->execute(['user_id' => $userId, 'badge_id' => $badge['id']]);
        if ($insert->rowCount() > 0) notify_user($userId, 'Badge unlocked', $badge['name'] . ' badge unlocked.');
    }
}
