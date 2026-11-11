# Emma Learn System Flowchart

## Main System Flow

```mermaid
flowchart TD
    A[Start] --> B[Open Emma Learn]
    B --> C{User logged in?}

    C -- No --> D[Login or Register]
    D --> E{Account type}
    E -- Student --> F[Student Login]
    E -- Admin --> G[Admin Login]

    F --> H{Valid credentials?}
    G --> I{Valid credentials?}
    H -- No --> J[Show Please Log In Popup]
    I -- No --> J
    J --> D

    H -- Yes --> K[Start Student Session]
    I -- Yes --> L[Start Admin Session]

    K --> M[Student Dashboard]
    M --> N[View XP, Level, Streak, Badges and Notifications]
    M --> O[Browse Courses]
    O --> P[Search Courses]
    P --> Q[View Course Details]
    Q --> R{Enrolled?}
    R -- No --> S[Enroll in Course]
    R -- Yes --> T[View Lessons and Quizzes]
    S --> T

    T --> U[Open Lesson]
    U --> V[Watch Lesson Video]
    V --> W[Mark Lesson Complete]
    W --> X[Add Lesson XP]
    X --> Y[Update Course Progress]
    Y --> Z{Course Complete?}
    Z -- Yes --> AA[Add Course Completion Bonus]
    AA --> AB[Unlock Achievement and Badge]
    Z -- No --> T

    T --> AC[Start Quiz]
    AC --> AD[Answer Multiple-Choice Questions]
    AD --> AE[Submit Quiz]
    AE --> AF[Calculate Score and Percentage]
    AF --> AG[Save Quiz Attempt]
    AG --> AH{Quiz Passed?}
    AH -- Yes --> AI[Show Congratulations Popup and Fireworks]
    AI --> AJ[Add Quiz XP and Bonus XP]
    AJ --> AB
    AH -- No --> AK[Show Score and Correct Answers]
    AK --> T

    AB --> AL[Create Notification]
    AL --> M

    L --> AM[Admin Dashboard]
    AM --> AN[View Statistics and Recent Activity]
    AM --> AO[Manage Courses]
    AO --> AP[Add, Edit or Delete Course]
    AP --> AQ[Manage Lessons]
    AQ --> AR[Add, Edit or Delete Lesson]
    AR --> AS[Add Optional YouTube Video]

    AM --> AT[Manage Quizzes]
    AT --> AU[Add, Edit or Delete Quiz]
    AU --> AV[Manage Questions]
    AV --> AW[Add Options and Correct Answers]

    AM --> AX[Manage Students]
    AX --> AY[View Student Profile]
    AY --> AZ[View Progress, XP, Achievements and Badges]

    M --> BA[Student Profile]
    M --> BB[Leaderboard]
    M --> BC[Notifications]
    BC --> BD[Mark Notifications as Read]

    M --> BE[Logout]
    AM --> BE
    BE --> BF[Destroy Session]
    BF --> B
```

## Database Relationship Flow

```mermaid
erDiagram
    USERS ||--|| STUDENT_PROFILES : has
    USERS ||--o{ ENROLLMENTS : creates
    COURSES ||--o{ ENROLLMENTS : receives
    COURSES ||--o{ LESSONS : contains
    LESSONS ||--o{ QUIZZES : contains
    QUIZZES ||--o{ QUESTIONS : contains
    QUESTIONS ||--o{ QUESTION_OPTIONS : has
    USERS ||--o{ LESSON_PROGRESS : tracks
    LESSONS ||--o{ LESSON_PROGRESS : receives
    USERS ||--o{ QUIZ_ATTEMPTS : makes
    QUIZZES ||--o{ QUIZ_ATTEMPTS : receives
    QUIZ_ATTEMPTS ||--o{ QUIZ_ANSWERS : contains
    USERS ||--o{ XP_TRANSACTIONS : earns
    USERS ||--o{ NOTIFICATIONS : receives
    USERS ||--o{ STUDENT_BADGES : earns
    BADGES ||--o{ STUDENT_BADGES : assigned
    USERS ||--o{ STUDENT_ACHIEVEMENTS : earns
    ACHIEVEMENTS ||--o{ STUDENT_ACHIEVEMENTS : assigned
```

## Important Project Paths

```text
auth/                     Authentication pages
student/dashboard.php     Student dashboard
student/courses.php       Course listing and search
student/course.php        Course details and progress
student/lesson.php        Lesson viewing and completion
student/quizzes.php       Student quiz listing
student/quiz.php          Quiz attempt and results
student/profile.php       Student profile
student/notifications.php Student notifications
student/leaderboard.php   Student leaderboard
admin/dashboard.php       Admin dashboard
admin/courses.php         Course management
admin/lessons.php         Lesson management
admin/quizzes.php         Quiz management
admin/questions.php       Question and option management
admin/students.php        Student management
```
