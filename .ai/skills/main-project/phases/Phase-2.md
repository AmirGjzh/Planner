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
- Appears in single-day and date-range views
- Contributes to daily workload calculations
- Used in performance reports and attention lists
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

## 3. Use Cases & Modules

A Use Case represents a complete scenario of interaction between a user and the system. Below they are written in a structured, step-by-step manner, ordered by dependency (topological order).

Each use case is implemented by a dedicated module (listed under "Module"). Modules communicate through service classes; a View / Presentation layer renders them via Laravel Blade + Livewire. Overdue detection is provided by the Task Management module (status + filter) and counted by the Reporting module.

### 3.1 UC-01 – Login

**Module:** Authentication Module — user registration, login, logout, password management, and session handling. Depends on the User model and Laravel's built-in Auth system. Used by all other modules (the user must be authenticated).

Actor: User (guest who is not logged in)

Description: The user logs into the system and reaches the authenticated dashboard.

Main Flow:
1. The user opens the website.
2. If not logged in, the user is redirected to the Login page, or clicks the Login button on the homepage.
3. The user enters their email and password.
4. The system validates the credentials:
   - If successful: the user is logged in and redirected to the Dashboard.
   - If unsuccessful: an error message is shown and the user can try again.

### 3.2 UC-02 – Register

**Module:** Authentication Module.

Actor: New user (guest)

Description: A new user creates an account to manage personal tasks.

Main Flow:
1. The user clicks Register on the homepage.
2. The registration form is displayed.
3. The user enters required information (email or username, password, password confirmation).
4. The system validates the data:
   - Email format
   - Unique username and email
   - Password length and requirements
5. If validation succeeds:
   - The account is created
   - A success or welcome message is shown
   - The user is redirected to the Login page

### 3.3 UC-03 – View and Edit Profile

**Module:** Profile Module — read and update profile information. Depends on the User model.

Actor: Logged-in user

Description: The user views and edits profile information.

Main Flow:
1. The logged-in user clicks Profile from the navigation bar.
2. The system displays current profile information.
3. The user may edit fields such as: first name, last name, date of birth, country, gender.
4. The user clicks Save.
5. The system validates and stores the updated information and shows a success message.

### 3.4 UC-04 – Delete Account

**Module:** Account Module — verifies the password, obfuscates credentials, and soft-deletes the account. Depends on the User model.

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
7. The user is logged out, sees a success toast ("Your account deleted successfully"), and is redirected to the homepage.

### 3.5 UC-05 – Logout

**Module:** Authentication Module.

Actor: Logged-in user

Description: The user logs out of the system.

Main Flow:
1. The user clicks Logout in the top navigation bar.
2. The system invalidates the session or token.
3. The user is redirected to the homepage or login page.
4. The navigation bar again shows Login and Register options.

### 3.6 UC-06 – Manage Categories

**Module:** Category Management Module — create, read, update, delete categories, and enforce restrict-on-delete when a category has tasks. Depends on the User and Task models. Interacts with Task Management (tasks reference categories).

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

### 3.7 UC-07 – Manage Plans

**Module:** Plan Management Module — create, read, update, delete plans, calculate plan progress (% completed), and prevent deletion when a plan has tasks. Depends on the User and Task models. Interacts with Task Management (tasks reference plans).

Actor: Logged-in user

Description: The user creates, edits, or deletes plans to group related tasks, and tracks each plan's progress.

Main Flow:
1. The user navigates to the Plans page.
2. To create a plan, the user fills in the name, optional description, start date, and end date, then submits.
3. To edit a plan, the user modifies the name, description, or date range and saves.
4. To delete a plan, the user clicks Delete. Deletion is blocked if the plan still has tasks assigned (restrict on delete).
5. Each plan shows its task count and progress percentage (completed tasks / total tasks × 100).
6. The user can view all tasks assigned to a plan.
7. When a task in a plan is marked as done or not done, the plan progress is recalculated and updated.

### 3.8 UC-08 – Manage Tasks

**Module:** Task Management Module — create, read, update, delete tasks, toggle task status (Done / Not Done), filter and sort tasks, and assign or remove a task from a plan. Depends on the User, Category, and Plan models. Interacts with Category Management, Plan Management, and the Workload Module. Also provides overdue detection (task_date < today and not done).

Actor: Logged-in user

Description: The user creates, edits, or deletes tasks for a specific day, marks tasks done/not done, and views tasks by single day or a custom date range with filters and sorting.

Main Flow:
1. The user is on the task page.
2. To create a task, the user fills in the title, category, estimated duration, priority, optional plan, selects a day, and submits.
3. To edit a task, the user modifies any field and saves.
4. To delete a task, the user clicks Delete and confirms. The task is permanently removed.
5. To toggle status, the user clicks Complete / Reopen on a task; the done field updates immediately.
6. The user can view a single day or a custom date range, and filter by category, plan, status, or priority.
7. The user can sort the list by date, priority, or estimated time.

### 3.9 UC-09 – Daily Workload

**Module:** Workload Module — calculate total estimated minutes per day for a user, map the total to a workload level and message, and recalculate when tasks are created, updated, or deleted. Depends on the Task model. Interacts with Task Management (triggered by task changes) and the View layer (provides workload data).

Actor: User (passive — calculated automatically)

Description: The system calculates and displays the total estimated time of all tasks (completed or not) for each day on the dashboard.

Main Flow:
1. The user opens the dashboard.
2. The system sums the estimated minutes of all tasks for each day of the current week (Sunday–Saturday), including completed tasks.
3. Each day is displayed as hours and minutes with a workload alert based on thresholds (No tasks / Light ≤120 min / Moderate ≤240 min / Heavy ≤360 min / Very Heavy >360 min).

### 3.10 UC-10 – Tasks Needing Attention

**Module:** Attention Module — detect tasks that are overdue or within their notification window (`task_date - day_before_alarm <= today`) and not done, then provide the attention list. Depends on the Task model. Interacts with Task Management (date/task changes affect attention).

Actor: User

Description: The user sees tasks that need attention: overdue tasks and tasks approaching within their alarm window.

Main Flow:
1. The user opens the dashboard.
2. The system finds tasks that are not done and where `task_date - day_before_alarm <= today` (this includes overdue tasks).
3. The tasks appear in a "Tasks needing attention" list ordered by date, each with a due/overdue label ("Due today", "Due tomorrow", "Due in X days", "X days ago").

Future versions may send these notifications via email.

### 3.11 UC-11 – Reports

**Module:** Reporting Module — generate performance reports for a given date range, aggregating tasks (total, completed, completion rate, estimated time), plans (total, completed), and a per-day workload chart. Depends on the Task and Plan models. Interacts with the View layer (display results in stat cards and a bar chart).

Actor: User

Description: The user views a performance summary over a chosen time range.

Main Flow:
1. The user opens the Reports page.
2. Selects a time range via a preset (this/last week, this/last month) or a custom date range.
3. The system calculates:
   - Total tasks created and completed tasks
   - Completion rate and total estimated time
   - Total and completed plans (those overlapping the range)
   - Per-day completed vs. remaining estimated minutes
4. The results are shown as stat cards and a per-day workload chart.

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
