# Phase 1 – Requirements Analysis

## 1.1 – User Stories

### Authentication and Profile

#### Login
As a user, I want to log into the system with my email and password so that I can access my tasks and plans.

#### Register
As a user, I want to create a new account with a username, email, and password so that I can start managing my tasks.

#### View and Edit Profile
As a user, I want to view and edit my profile information (name, date of birth, country, gender) so that I can keep my account details up to date.

#### Logout
As a user, I want to log out of the system so that my session is securely ended.

#### Delete Account
As a user, I want to permanently delete my account with password confirmation so that I can remove all my data from the system.

### Category Management

#### Manage Categories
As a user, I want to create, rename, and delete categories so that I can organize my tasks by topic (e.g., university, work, personal).

Each task belongs to exactly one category.

### Plan Management

#### Create Plan
As a user, I want to create a plan with a name, optional description, and a date range so that I can group related tasks under a common goal.

#### Edit Plan
As a user, I want to edit my plan's name, description, or date range so that I can keep my plans up to date.

#### Delete Plan
As a user, I want to delete a plan so it no longer appears in my list. Deleting a plan is blocked if it still has tasks assigned (restrict on delete).

### Task Management

#### Create Task
As a user, I want to create a task for a specific day with a title, category, estimated duration, and priority so that I know what needs to be done on that day.

#### Edit Task
As a user, I want to edit my existing tasks (title, description, category, estimated time, priority, alarm days) so that I can keep my schedule up to date if circumstances change.

#### Delete Task
As a user, I want to delete a task so it no longer appears in my list.

### Task Status

#### Mark Task as Done / Not Done
As a user, I want to mark tasks as completed or revert them to not done so I can track my progress.

#### Automatic Not Done Status
As a user, if I do not specify a task's status by the end of the day, I want the system to automatically consider it not done so that performance reports remain accurate.

### Workload and Indicators

#### Daily Workload Calculation
As a user, I want the system to calculate the total estimated time of tasks for each day so I know how much work I have assigned to myself.

#### Color-Coded Day Indicators
As a user, I want each day in the calendar or daily view to have a status color based on total workload:
- Total time = 0 hours → White
- Total time < 3 hours → Green
- Total time < 5 hours → Yellow
- Total time < 8 hours → Red
- Total time ≥ 8 hours → Black

In this version, thresholds are global and fixed. In future versions, they may become configurable.

### Views

#### Calendar View (Daily, Weekly, Monthly)
As a user, I want to see tasks displayed in daily, weekly, and monthly calendar views so that I can have both detailed and overview perspectives of my schedule.

#### View Day Details
As a user, I want to click on a specific day in the calendar to view its tasks and total workload so that I can drill down into any day.

### Filtering and Sorting

#### Filter and Sort Tasks
As a user, I want to filter tasks by category, plan, date range, status, or priority, and sort them by date, priority, or estimated time so that I can quickly find specific tasks.

### Overdue and Notifications

#### Overdue Tasks
As a user, I want the system to identify tasks whose due date has passed and are still not marked as done so that I can plan to catch up.

#### Upcoming Tasks
As a user, I want to see tasks that are approaching within a defined window (today + X days) so that I can prepare for what's coming.

### Reports

#### Performance Reports
As a user, I want to select a time range and see total tasks created, completed, completion rate, and overdue count so that I can analyze my performance.

### Plan Tracking

#### View Plan Tasks
As a user, I want to view a specific plan and see all tasks assigned to it so that I can track progress on that project.

#### Plan Progress
As a user, I want to see the progress of a plan based on how many of its tasks are completed so that I can track how close I am to finishing a project.

#### Plan Progress Tracking
As a user, I want to see real-time plan progress updates as I mark tasks done so that I always know the current status.

### Recurring Tasks

#### Recurring / Multi-Day Tasks
As a user, I want to assign a task to multiple selected days (e.g., specific weekdays or multiple dates) so that I don't have to create repetitive tasks separately for each day.

### Soft Delete Behavior

#### Soft Delete and Admin Management
Soft deletion is only used for User accounts. Categories, tasks, and plans are hard-deleted. Deleting a plan is blocked if it still has tasks (restrict on delete). If a category is deleted, tasks referencing it are prevented from deletion unless they are reassigned first.

## 1.2 – Non-Functional Requirements

### Usability

- A user should be able to create a new task for a specific day in just a few steps.
- The UI must be simple, clean, and uncluttered.
- Daily, weekly, and monthly views must be intuitive and easily accessible.

### Performance

- For a reasonable workload (e.g., several thousand tasks per year), displaying lists (daily, weekly, monthly, overdue, upcoming) must occur without noticeable delay.

### Maintainability

- The codebase must be layered and modular, separating:
  - Task management
  - Category management
  - Plan management
  - Daily workload and color logic
  - Reporting logic
  - Upcoming tasks logic
- Important logic components must be covered by unit tests and higher-level tests.

### Reliability

- Changing task status (done/not done), editing, or soft deletion must not result in unintended data loss.
- The system must behave consistently and predictably in calculating overdue, upcoming, and reporting metrics.

### Simplicity

- The first version must remain as simple as possible in terms of UI and features.
- More advanced features (e.g., advanced notification settings, subscription plans) will be added in later phases.

## 1.3 – Acceptance Criteria

### Login

**Given** the user is on the login page,
**When** they enter valid email and password and submit,
**Then** they are authenticated and redirected to the dashboard.
**And** if credentials are invalid, an error message is shown.

### Register

**Given** the user is on the registration page,
**When** they enter a unique username, valid email, and matching password confirmation,
**Then** the account is created and they are redirected to the login page.
**And** if the username or email is taken, an error is shown.

### Manage Categories

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

### Create Plan

**Given** the user is on the Plans page,
**When** they enter a name, optional description, and valid date range,
**Then** the plan is created and appears in the plan list.
**And** if the name is duplicate or dates are invalid, an error is shown.

### Edit Plan

**Given** the user is viewing a plan,
**When** they modify the name, description, or date range and save,
**Then** the plan is updated with the new values.
**And** duplicate name detection applies.

### Delete Plan

**Given** the user is viewing a plan that has tasks,
**When** they attempt to delete the plan,
**Then** the deletion is blocked and an error message is shown.

**Given** the user is viewing a plan with no tasks,
**When** they delete and confirm,
**Then** the plan is permanently removed.

### View and Edit Profile

**Given** the user is on the Profile page,
**When** they view their profile,
**Then** current information is displayed (name, date of birth, country, gender).

**Given** the user modifies profile fields and saves,
**When** the data is valid,
**Then** the profile is updated and a success message is shown.

**Given** the user enters a username that is already taken,
**Then** an error is shown and the profile is not updated.

### Logout

**Given** the user is logged in,
**When** they click Logout,
**Then** the session is invalidated and they are redirected to the login page.
**And** the navigation bar shows Login and Register options again.

### Delete Account

**Given** the user is on the Profile page,
**When** they click Delete Account and enter their password,
**Then** the account is soft-deleted, the session is ended, and they are redirected to the login page.

**Given** the user enters the wrong password,
**Then** an error is shown and the account is not deleted.

**Given** the user exceeds 5 failed attempts within a minute,
**Then** further attempts are rate-limited.

### Create Task

**Given** the user is on the task page for a specific day,
**When** they fill in the title, select a category, set an estimated duration, and submit,
**Then** the task appears in that day's task list.
**And** the daily workload is recalculated.
**And** if required fields are missing, submission is rejected.

### Edit Task

**Given** the user is viewing a task,
**When** they modify any field and save,
**Then** the task is updated.
**And** the daily workload is recalculated if the date or estimated minutes changed.

### Delete Task

**Given** the user is viewing a task,
**When** they click delete and confirm,
**Then** the task is permanently removed.
**And** the daily workload is recalculated.

### Mark Task as Done / Not Done

**Given** the user is viewing tasks for a day,
**When** they toggle the status of a task,
**Then** the task's done field updates immediately.
**And** the change is reflected in the UI without a page reload.

### Daily Workload Calculation

**Given** the user is viewing a day,
**When** tasks exist for that day,
**Then** the total estimated minutes is summed and displayed.
**And** the day shows the correct color indicator based on thresholds.
**And** the contextual message matches the color level.

### Color-Coded Day Indicators

**Given** the user is viewing the calendar,
**When** a day has tasks with different total estimated times,
**Then** the day displays the correct color: White (0h), Green (<3h), Yellow (<5h), Red (<8h), Black (≥8h).

### Overdue Tasks

**Given** tasks exist with task_date < today and done = false,
**When** the user opens the Overdue section,
**Then** all such tasks are listed.
**And** tasks marked as done are excluded from the list.

### Automatic Not Done Overnight

**Given** a task has task_date = today and done = false,
**When** the day ends,
**Then** the system ensures the task status remains Not Done for reporting purposes.

### Performance Reports

**Given** the user selects a date range,
**When** they request a report,
**Then** the system displays: total tasks created, total completed, completion rate (%), and overdue count.
**And** the data is scoped to the authenticated user only.

### Calendar View

**Given** the user opens the Calendar View,
**When** the current month is displayed,
**Then** each day shows its workload color indicator.
**And** the user can navigate between months.

**Given** the user switches to Weekly View,
**Then** tasks for the selected week are displayed with daily workload colors.

**Given** the user switches to Daily View,
**Then** all tasks for the selected day are displayed with the total workload.

### View Day Details

**Given** the user clicks a day in the monthly calendar,
**When** the day has tasks,
**Then** the system navigates to the daily view showing all tasks and the total workload.

### View Plan Tasks

**Given** the user is viewing a plan,
**When** they open the plan details,
**Then** all tasks assigned to that plan are displayed.
**And** the plan progress percentage is shown.

### Plan Progress

**Given** a plan has tasks with some marked as done,
**When** the user views the plan list or plan details,
**Then** the progress is displayed as a percentage (completed tasks / total tasks × 100).

### Plan Progress Tracking

**Given** the user marks a task in a plan as done or not done,
**When** the status changes,
**Then** the plan progress is recalculated and updated in the UI immediately.

### Filter and Sort Tasks

**Given** the user is on a task list view,
**When** they select a filter (category, plan, status, priority, or date range),
**Then** only matching tasks are displayed.
**And** multiple filters can be combined.
**And** clearing filters restores the full list.

**Given** the user selects a sort option (date, priority, or estimated time),
**Then** the task list is reordered accordingly.

### Upcoming Tasks

**Given** the user has tasks within the notification window (today + X days),
**When** they open the dashboard,
**Then** upcoming tasks are displayed in a list.

### Recurring / Multi-Day Tasks

**Given** the user creates a task for multiple days,
**When** they select multiple dates,
**Then** separate independent tasks are created for each selected date.

### Soft Delete Behavior

**Given** a user account is deleted,
**Then** the user record is soft-deleted with obfuscated email and username.

**Given** a plan with tasks is deleted,
**Then** deletion is blocked by the database constraint.

**Given** a category with tasks is deleted,
**Then** deletion is blocked by the database constraint.

## 1.4 – Requirements Prioritization (MoSCoW)

### Must Have (MVP Critical)

- User authentication (register, login, logout)
- Category CRUD
- Task CRUD (create, read, edit, delete)
- Assign task to exactly one category
- Set estimated duration per task
- Mark task as done / not done
- Daily view with task list
- Weekly view
- Monthly calendar view
- Daily workload calculation with color indicators (White, Green, Yellow, Red, Black)
- Overdue tasks list
- Upcoming tasks list (based on day_before_alarm)
- Plan CRUD
- Assign / remove task from plan
- Plan progress tracking

### Should Have (High Priority — Next)

- Filter tasks by category, plan, status, priority, date range
- Sort tasks by date, priority, estimated time
- Performance reports over a date range
- Create task for multiple days (recurrence via duplicate entries)

### Could Have (Nice to Have)

- Profile editing (name, DOB, country, gender)
- Account deletion
- Admin panel for user management


