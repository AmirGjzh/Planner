# Phase 2 – Conceptual Domain Design

## 1. Domain Entities

We have four main entities in the system.

### 1.1 User

Represents each user of the system.

Role in the system:
- Owner of their own tasks, categories, and plans
- Responsible for authentication, profile management, and personal settings

### 1.2 Category

Represents task groupings created by a user.

Role in the system:
- Organizes tasks into logical groups (e.g., Work, University, Personal, Fitness)
- Each user has their own categories; categories are not shared between users

### 1.3 Task

Represents an individual piece of work assigned to a specific day on the calendar.

Role in the system:
- Appears in daily, weekly, and monthly views
- Contributes to daily workload calculations
- Used in performance reports, overdue tasks, and upcoming tasks
- Optionally belongs to a plan for project-level grouping

### 1.4 Plan

Represents a higher-level grouping of tasks under a common goal or project.

Role in the system:
- Groups related tasks into meaningful projects (e.g., "Build Portfolio Website", "Learn Laravel")
- Provides progress tracking based on task completion
- Each user has their own plans; plans are not shared between users

## 2. Relationships Between Entities

### User – Task

A User can have multiple Tasks. Each Task belongs to exactly one User.

- User (1) ——— (N) Task

### User – Category

A User can have multiple Categories. Each Category belongs to exactly one User.

- User (1) ——— (N) Category

### Category – Task

A Category can contain multiple Tasks. Each Task belongs to exactly one Category.

- Category (1) ——— (N) Task

### User – Plan

A User can have multiple Plans. Each Plan belongs to exactly one User.

- User (1) ——— (N) Plan

### Plan – Task

A Plan can contain multiple Tasks. Each Task belongs to zero or one Plan (optional association).

- Plan (0..1) ——— (0..N) Task

Note: Category and Plan have no direct relationship. They are independent organizational dimensions. Category classifies tasks by type or subject, while Plan groups tasks under a project or goal.

## 3. Use Cases

A Use Case represents a complete scenario of interaction between a user and the system. Below they are written in a structured, step-by-step manner, ordered by dependency (topological order).

### 3.1 Authentication and Profile

#### UC-01 – Login

Actor: User (guest who is not logged in)

Description: The user logs into the system and reaches the authenticated dashboard.

Main Flow:
1. The user opens the website.
2. If not logged in, the user is redirected to the Login page, or clicks the Login button on the homepage.
3. The user enters their email and password.
4. The system validates the credentials:
   - If successful: the user is logged in and redirected to the Dashboard.
   - If unsuccessful: an error message is shown and the user can try again.

#### UC-02 – Register

Actor: New user (guest)

Description: A new user creates an account to manage personal tasks.

Main Flow:
1. The user clicks Register on the homepage.
2. The registration form is displayed.
3. The user enters required information (username, email, password, password confirmation).
4. The system validates the data:
   - Email format
   - Unique username and email
   - Password length and requirements
5. If validation succeeds:
   - The account is created
   - A success or welcome message is shown
   - The user is redirected to the Login page

#### UC-03 – View and Edit Profile

Actor: Logged-in user

Description: The user views and edits profile information.

Main Flow:
1. The logged-in user clicks Profile from the navigation bar.
2. The system displays current profile information.
3. The user may edit fields such as: username, first name, last name, date of birth, country, gender.
4. The user clicks Save.
5. The system validates and stores the updated information and shows a success message.

#### UC-04 – Delete Account

Actor: Logged-in user

Description: The user deletes their account along with all related data.

Main Flow:
1. The user opens Account Settings from the profile menu.
2. At the bottom of the page, the user sees a Delete Account button.
3. The system requests the current password for confirmation.
4. The user enters the password and confirms.
5. The system verifies the password.
6. If valid:
   - The account deletion process begins
   - Related data is soft-deleted or hard-deleted as configured
7. The user is logged out and sees a message: "Your account has been deleted."

#### UC-05 – Logout

Actor: Logged-in user

Description: The user logs out of the system.

Main Flow:
1. The user clicks Logout in the top navigation bar.
2. The system invalidates the session or token.
3. The user is redirected to the homepage or login page.
4. The navigation bar again shows Login and Register options.

### 3.2 Category Management

#### UC-06 – Manage Categories

Actor: Logged-in user

**Create Category:**
1. The user opens the Category Management page.
2. Clicks Add Category.
3. Enters the category name (e.g., Work, University).
4. Saves the category.
5. The system creates the category for that user.

**Edit Category:**
1. The user clicks Edit next to a category.
2. The edit form opens.
3. The user changes the name and saves.
4. Tasks linked to the category remain linked to the same category record.

**Delete Category:**
1. Deletion is prevented if the category still has tasks assigned (restrict on delete).
2. User must reassign or delete all tasks in the category before it can be deleted.

**Filter & Sort (page-level):** The category list supports live text search and sorting by date created (default), name, or task count (folded in from UC-15).

### 3.3 Plan Management

#### UC-07 – Manage Plans

Actor: Logged-in user

Description: The user creates, edits, or deletes plans to group related tasks.

Main Flow:
1. The user navigates to the Plans page.
2. To create a plan, the user fills in the name, optional description, start date, and end date, then submits.
3. To edit a plan, the user modifies the name, description, or date range and saves.
4. To delete a plan, the user clicks Delete. Deletion is blocked if the plan still has tasks assigned (restrict on delete).
5. To complete or reopen a plan, the user uses the per-card actions. Completing is blocked while the plan still has undone tasks.

**Plan Progress & Tracking (folded in from UC-14):**
1. The system shows each plan with its task count and a progress bar (completed tasks / total tasks × 100).
2. The user can view all tasks assigned to a plan (popover / View Tasks deep-link).
3. When tasks are added or marked done, the progress is recalculated and updated.

**Filter & Sort (page-level):** The plan list supports live text search, sorting by state (default) / deadline / load / latest / name, a status filter (All / Active / Completed / Overdue), and a date-range calendar filter (folded in from UC-15).

### 3.4 Task Management

#### UC-08 – Manage Tasks (merged: CRUD + Toggle Done + Overdue + Calendar View + Filter & Sort)

Actor: Logged-in user

Description: The user creates, edits, or deletes tasks for a specific day, toggles task done/not-done, sees overdue tasks, filters tasks by a custom date range, and filters/sorts the task list.

**Create Task:**
1. The user is on the task page for a specific day.
2. The user fills in the title, category, estimated duration, priority, optional description, optional plan, task date, and alarm days, then submits.
3. The system creates the task and recalculates the daily workload.

**Edit Task:**
1. The user modifies any field and saves.
2. The system updates the task and recalculates the daily workload if the date or estimated minutes changed.

**Delete Task:**
1. The user clicks Delete and confirms.
2. The task is permanently removed and the daily workload is recalculated.

**Toggle Done:**
1. Each task has a checkbox or Done button.
2. The user toggles the status.
3. The system updates the task status immediately without a page reload.
4. System background logic: if a task status is not updated by the end of the day, the system considers it Not Done. This affects reports and overdue task detection.

**Overdue:**
1. The user filters by the Overdue status.
2. The system filters tasks where task_date < today and status = Not Done.
3. The list is displayed; the user may mark them as done or delete them.
4. Future versions may allow rescheduling.

**Calendar View (Date Range Filter):**
1. The user selects a date range via a range datepicker.
2. The system filters tasks within the selected range.
3. Full daily, weekly, and monthly calendar views with workload colors are deferred to a later layer.

**Filter & Sort:**
1. The user filters tasks by category, plan, status (Done / Not Done / Overdue), priority, or date range.
2. The user sorts tasks by date, priority, estimated time, or state.
3. Multiple filters and sorts can be combined; clearing filters restores the full list.

### 3.5 Workload (Deferred — dashboard page)

#### UC-09 – Daily Workload

Actor: User (passive — calculated automatically)

Description: The system calculates and displays the total estimated time of tasks for each day.

Main Flow:
1. When tasks are created, edited, or deleted, the system recalculates the total estimated minutes for that day.
2. The total is displayed as hours and minutes.

Status: **Deferred.** The full dashboard workload view is not yet built. (Single-day workload alerts currently live on the task page as part of UC-08.)

### 3.6 Notifications (Deferred — dashboard page)

#### UC-10 – Upcoming Tasks

Actor: User

Description: The user sees tasks that are approaching in the next few days.

Main Flow:
1. The user opens the dashboard.
2. The system finds tasks within the configured window (today to today + X days).
3. The tasks appear in an Upcoming Tasks list.

Future versions may send these notifications via email.

Status: **Deferred.** The dashboard page is not yet built.

### 3.7 Reports (Deferred — report page)

#### UC-11 – Performance Reports

Actor: User

Description: The user views a performance summary over a chosen time range.

Main Flow:
1. The user opens the Reports page.
2. Selects a time range (date A to date B).
3. The system calculates:
   - Total tasks created
   - Completed tasks
   - Incomplete or overdue tasks
   - Completion rate
4. The results are shown using numbers, simple charts, or tables.

Status: **Deferred.** The report page is not yet built.

## 4. Conceptual Domain Model

At this level we define only the core attributes, not database types.

### 4.1 User

Conceptual fields:
- User ID
- Username
- Email
- Password (hashed)
- First Name
- Last Name
- Date of Birth
- Country
- Gender
- Account Creation Date
- Account Status (Active / Deleted / Suspended)

Some fields may be optional in implementation.

### 4.2 Category

Conceptual fields:
- Category ID
- Category Name
- Owner User (reference to User)
- (Optional in future) category color or icon

### 4.3 Task

Conceptual fields:
- Task ID
- Title
- Description
- Owner User (reference to User)
- Category (reference to Category)
- Plan (optional, reference to Plan)
- Task date (scheduled day)
- Estimated duration
- Priority (High / Medium / Low)
- Status (Done / Not Done)
- Notification days before deadline
- Creation date
- Last update date

### 4.4 Plan

Conceptual fields:
- Plan ID
- Plan Name
- Description (optional)
- Start Date (required)
- End Date (required)
- Done (boolean, default false)
- Owner User (reference to User)
- Creation date
- Last update date

## 5. Module Boundaries

The system is decomposed into the following modules. Each module has a clear responsibility and communicates with others through well-defined service classes.

### 5.1 Authentication Module

**Responsibility:** User registration, login, logout, password management, session handling.
**Depends on:** User model, Laravel's built-in Auth system.
**Used by:** All other modules (user must be authenticated).

### 5.2 Category Management Module

**Responsibility:** Create, read, update, delete categories. Enforce restrict-on-delete when category has tasks.
**Depends on:** User, Task models.
**Interacts with:** Task Management (tasks reference categories).

### 5.3 Plan Management Module

**Responsibility:** Create, read, update, delete plans. Calculate plan progress (% completed). Prevent deletion when plan has tasks.
**Depends on:** User, Task models.
**Interacts with:** Task Management (tasks reference plans).

### 5.4 Task Management Module

**Responsibility:** Create, read, update, delete tasks. Toggle task status (Done / Not Done). Filtering and sorting tasks. Assign or remove task from plan.
**Depends on:** User, Category, Plan models.
**Interacts with:** Category Management, Plan Management, Daily Workload Module.

### 5.5 Daily Workload Module

**Responsibility:** Calculate total estimated minutes per day for a user. Map total time to workload level and message. Recalculate when tasks are created, updated, or deleted.
**Depends on:** Task model.
**Interacts with:** Task Management (triggered by task changes), View Layer (provides workload data).

### 5.6 Overdue Tasks Module

**Responsibility:** Detect tasks where task_date < today and done = false. Provide overdue list scoped to the authenticated user.
**Depends on:** Task model.
**Interacts with:** Task Management (status changes remove tasks from overdue).

### 5.7 Upcoming Tasks Module

**Responsibility:** Detect tasks within the notification window (today to today + day_before_alarm). Provide upcoming list.
**Depends on:** Task model.
**Interacts with:** Task Management (date/task changes affect upcoming).

### 5.8 Reporting Module

**Responsibility:** Generate performance reports for a given date range. Calculate: total tasks, completed tasks, completion rate, overdue count.
**Depends on:** Task model.
**Interacts with:** View Layer (display results in tables or charts).

### 5.9 View / Presentation Layer

**Responsibility:** Render UI using Laravel Blade and Livewire components. Handle user interactions via Livewire method calls. Compose data from service classes for display.
**Depends on:** All modules (orchestrates data for views).
**Technology:** Laravel Blade layouts + Livewire full-page / nested components.
