# Phase 1 – Requirements Analysis

## 1.1 – User Stories

The stories below are grouped into the 11 use cases (UC-01 … UC-11), in topological order.

### UC-01 – Login

As a user, I want to log into the system with my email and password so that I can access my tasks and plans.

### UC-02 – Register

As a user, I want to create a new account with a username, email, and password so that I can start managing my tasks.

### UC-03 – View and Edit Profile

As a user, I want to view and edit my profile information (name, date of birth, country, gender) so that I can keep my account details up to date.

### UC-04 – Delete Account

As a user, I want to permanently delete my account with password confirmation so that I can remove all my data from the system.

### UC-05 – Logout

As a user, I want to log out of the system so that my session is securely ended.

### UC-06 – Manage Categories

As a user, I want to create, rename, and delete categories so that I can organize my tasks by topic (e.g., university, work, personal).

Each task belongs to exactly one category.

### UC-07 – Manage Plans

#### Manage Plans

As a user, I want to create, edit, and delete plans so that I can group related tasks under a common goal and keep them up to date. Deleting a plan is blocked if it still has tasks assigned (restrict on delete).

#### View Plan Tasks

As a user, I want to view a specific plan and see all tasks assigned to it so that I can track progress on that project.

#### Plan Progress

As a user, I want to see the progress of a plan based on how many of its tasks are completed so that I can track how close I am to finishing a project.

#### Plan Progress Tracking

As a user, I want to see real-time plan progress updates as I mark tasks done so that I always know the current status.

### UC-08 – Manage Tasks

#### Manage Tasks

As a user, I want to create, edit, and delete tasks so that I can schedule my work and keep it up to date. Creating a task requires a title, category, estimated duration, and priority. Editing allows modifying all fields. Deletion removes the task permanently.

#### Toggle Task Done / Not Done

As a user, I want to mark tasks as completed or revert them to not done so I can track my progress.

#### Filter, Sort, and Date Range

As a user, I want to view tasks for a single day or a custom date range, filter tasks by category, plan, status, or priority, and sort them by date, priority, or estimated time so that I can quickly find specific tasks.

### UC-09 – Daily Workload

As a user, I want the system to calculate the total estimated time of tasks for each day and show workload alerts on the dashboard so I know how much work I have assigned. In this version, thresholds are global and fixed. In future versions, they may become configurable.

### UC-10 – Tasks Needing Attention

As a user, I want to see overdue tasks and tasks whose alarm window (`task_date - day_before_alarm`) has started so that I can prepare for what needs my attention.

### UC-11 – Reports

As a user, I want to select a time range and see my performance — tasks created, completed, completion rate, estimated time, plan progress, and a per-day workload chart — so that I can analyze my performance.

## 1.2 – Non-Functional Requirements

### Usability

- A user should be able to create a new task for a specific day in just a few steps.
- The UI must be simple, clean, and uncluttered.
- Single-day and date-range views must be intuitive and easily accessible.

### Performance

- For a reasonable workload (e.g., several thousand tasks per year), displaying lists (single day, date range, attention) must occur without noticeable delay.

### Maintainability

- The codebase must be layered and modular, separating:
  - Task management
  - Category management
  - Plan management
  - Daily workload logic
  - Reporting logic
  - Tasks needing attention logic
- Important logic components must be covered by unit tests and higher-level tests.

### Reliability

- Changing task status (done/not done), editing, or soft deletion must not result in unintended data loss.
- The system must behave consistently and predictably in calculating overdue, attention, and reporting metrics.

### Simplicity

- The first version must remain as simple as possible in terms of UI and features.
- More advanced features (e.g., advanced notification settings, subscription plans) will be added in later phases.

## 1.3 – Acceptance Criteria

The acceptance criteria are grouped into the 11 use cases (UC-01 … UC-11), in topological order.

### UC-01 – Login

**Given** the user is on the login page,
**When** they enter valid email and password and submit,
**Then** they are authenticated and redirected to the dashboard.
**And** if credentials are invalid, an error message is shown.

### UC-02 – Register

**Given** the user is on the registration page,
**When** they enter a unique username, valid email, and matching password confirmation,
**Then** the account is created and they are redirected to the login page.
**And** if the username or email is taken, an error is shown.

### UC-03 – View and Edit Profile

**Given** the user is on the Profile page,
**When** they view their profile,
**Then** current information is displayed (name, date of birth, country, gender).

**Given** the user modifies profile fields and saves,
**When** the data is valid,
**Then** the profile is updated and a success message is shown.

**Given** the user enters a username that is already taken,
**Then** an error is shown and the profile is not updated.

### UC-04 – Delete Account

**Given** the user is on the Profile page,
**When** they click Delete Account and enter their password,
**Then** the account is soft-deleted, the session is ended, and they are redirected to the login page.

**Given** the user enters the wrong password,
**Then** an error is shown and the account is not deleted.

**Given** the user exceeds 5 failed attempts within a minute,
**Then** further attempts are rate-limited.

### UC-05 – Logout

**Given** the user is logged in,
**When** they click Logout,
**Then** the session is invalidated and they are redirected to the login page.
**And** the navigation bar shows Login and Register options again.

### UC-06 – Manage Categories

**Given** the user is on the Category Management page,
**When** they enter a unique category name and submit,
**Then** the category is created and appears in the category list.
**And** if the name is empty or duplicate, an error is shown.

**Given** the user clicks Edit on a category,
**When** they change the name and save,
**Then** the category is updated.
**And** if the new name is a duplicate, an error is shown.

**Given** the user clicks Delete on a category with no tasks,
**When** they confirm,
**Then** the category is permanently removed.

**Given** the user clicks Delete on a category that has tasks,
**Then** deletion is blocked and an error message is shown.

### UC-07 – Manage Plans

#### Manage Plans

**Given** the user is on the Plans page,
**When** they enter a name, optional description, and valid date range,
**Then** the plan is created and appears in the plan list.
**And** if the name is duplicate or dates are invalid, an error is shown.

**Given** the user is viewing a plan,
**When** they modify the name, description, or date range and save,
**Then** the plan is updated with the new values.
**And** duplicate name detection applies.

**Given** the user is viewing a plan that has tasks,
**When** they attempt to delete the plan,
**Then** the deletion is blocked and an error message is shown.

**Given** the user is viewing a plan with no tasks,
**When** they delete and confirm,
**Then** the plan is permanently removed.

#### View Plan Tasks

**Given** the user is viewing a plan,
**When** they open the plan details,
**Then** all tasks assigned to that plan are displayed.
**And** the plan progress percentage is shown.

#### Plan Progress

**Given** a plan has tasks with some marked as done,
**When** the user views the plan list or plan details,
**Then** the progress is displayed as a percentage (completed tasks / total tasks × 100).

#### Plan Progress Tracking

**Given** the user marks a task in a plan as done or not done,
**When** the status changes,
**Then** the plan progress is recalculated and updated in the UI immediately.

### UC-08 – Manage Tasks

#### Manage Tasks

**Given** the user is on the task page for a specific day,
**When** they fill in the title, select a category, set an estimated duration, and submit,
**Then** the task appears in that day's task list.
**And** if required fields are missing, submission is rejected.

**Given** the user is viewing a task,
**When** they modify any field and save,
**Then** the task is updated.

**Given** the user is viewing a task,
**When** they click delete and confirm,
**Then** the task is permanently removed.

#### Toggle Task Done / Not Done

**Given** the user is viewing tasks for a day,
**When** they toggle the status of a task,
**Then** the task's done field updates immediately.
**And** the change is reflected in the UI without a page reload.

#### Filter, Sort, and Date Range

**Given** the user is on a task list view,
**When** they select a date range or filter (category, plan, status, or priority),
**Then** only matching tasks are displayed.
**And** multiple filters can be combined.
**And** clearing filters restores the full list.

**Given** the user selects a sort option (date, priority, or estimated time),
**Then** the task list is reordered accordingly.

### UC-09 – Daily Workload

**Given** the user is on the dashboard viewing the current week,
**When** tasks exist for a day,
**Then** the total estimated minutes is summed and displayed as hours and minutes.
**And** a workload alert is shown based on thresholds (No tasks / Light ≤120 min / Moderate ≤240 min / Heavy ≤360 min / Very Heavy >360 min).

### UC-10 – Tasks Needing Attention

**Given** the user has tasks that are overdue or within their notification window (`task_date - day_before_alarm <= today`),
**When** they open the dashboard,
**Then** those tasks are displayed in a list with due/overdue labels.
**And** completed tasks are excluded.

### UC-11 – Reports

**Given** the user selects a date range (preset or custom),
**When** they open the Reports page,
**Then** the system displays: total tasks created, completed tasks, completion rate (%), estimated time, total/completed plans, and a per-day workload chart.
**And** the data is scoped to the authenticated user only.

## 1.4 – Requirements Prioritization (MoSCoW)

### Must Have (MVP Critical)

- User authentication (register, login, logout) — UC-01, 02, 05
- Category CRUD — UC-06
- Assign task to exactly one category — UC-06
- Plan CRUD — UC-07
- Assign / remove task from plan — UC-07
- Plan progress tracking — UC-07
- Task CRUD (create, read, edit, delete) — UC-08
- Set estimated duration per task — UC-08
- Mark task as done / not done — UC-08
- View tasks for a single day or a custom date range — UC-08
- Daily workload calculation with alerts — UC-09
- Tasks needing attention list (overdue + day_before_alarm window) — UC-10

### Should Have (High Priority — Next)

- Filter tasks by category, plan, status, priority, date range — UC-08
- Sort tasks by date, priority, estimated time — UC-08
- Performance reports over a date range — UC-11

### Could Have (Nice to Have)

- Profile editing (name, DOB, country, gender) — UC-03
- Account deletion — UC-04
- Admin panel for user management
