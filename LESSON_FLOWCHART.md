# Emma Learn Lesson Flowchart

## Student Lesson Flow

```mermaid
flowchart TD
    A[Student opens a lesson] --> B{Logged in?}
    B -- No --> C[Redirect to Login]
    C --> D[Show Login Popup]
    D --> E[Student Logs In]
    E --> F{Login Successful?}
    F -- No --> D
    F -- Yes --> G[Check Course Enrollment]
    B -- Yes --> G

    G --> H{Enrolled in Course?}
    H -- No --> I[Show Access Denied Message]
    I --> J[Redirect to Course List]
    H -- Yes --> K[Load Published Lesson]

    K --> L{Lesson Found?}
    L -- No --> M[Show Lesson Not Found]
    M --> J
    L -- Yes --> N[Display Lesson Page]

    N --> O[Display Lesson Title]
    N --> P[Display Lesson Content]
    N --> Q{Video Available?}
    Q -- Yes --> R[Display YouTube Video]
    Q -- No --> S[Continue Without Video]
    R --> T[Student Studies Lesson]
    S --> T

    T --> U{Lesson Already Completed?}
    U -- Yes --> V[Show Lesson Completed]
    U -- No --> W[Student Clicks Mark Complete]

    W --> X{CSRF Token Valid?}
    X -- No --> Y[Show Security Error]
    X -- Yes --> Z[Save Lesson Progress]

    Z --> AA[Add Lesson XP]
    AA --> AB[Record Daily Activity]
    AB --> AC[Update Streak]
    AC --> AD[Update Course Progress]

    AD --> AE{All Course Lessons Completed?}
    AE -- No --> AF[Show Updated Progress]
    AE -- Yes --> AG[Mark Course Completed]
    AG --> AH[Add Course Completion XP]
    AH --> AI[Unlock Achievement or Badge]
    AI --> AJ[Create Course Completion Notification]
    AJ --> AF

    AF --> AK{Next Lesson Available?}
    AK -- Yes --> AL[Show Next Lesson Button]
    AK -- No --> AM[Show Course Completion Status]

    V --> AN{Next Lesson Available?}
    AN -- Yes --> AL
    AN -- No --> AM
    AL --> A
```

## Lesson Database Flow

```mermaid
flowchart LR
    U[Student] --> E[enrollments]
    E --> C[courses]
    C --> L[lessons]
    L --> V[video_url]
    U --> P[lesson_progress]
    L --> P
    P --> X[xp_transactions]
    X --> SP[student_profiles]
    SP --> A[achievements]
    SP --> B[badges]
    A --> N[notifications]
    B --> N
```

## Lesson Security Rules

- The student must be authenticated.
- The student must be enrolled in the lesson course.
- Only published lessons can be opened.
- Lesson progress is stored for the current student only.
- Completion requests require a valid CSRF token.
- XP is awarded only once for each lesson completion.
- Video URLs are restricted to valid YouTube URLs.

## Related Files

```text
student/lesson.php       Student lesson page
student/course.php       Course lessons and progress
admin/lessons.php        Admin lesson management
includes/gamification.php XP, streaks, badges and achievements
database/schema.sql      Lesson and progress tables
```
