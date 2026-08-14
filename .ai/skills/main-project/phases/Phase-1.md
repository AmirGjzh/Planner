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

#### Manage Plans

As a user, I want to create, edit, and delete plans so that I can group related tasks under a common goal and keep them up to date. Deleting a plan is blocked if it still has tasks assigned (restrict on delete).

### Task Management

#### Manage Tasks (UC-08)

As a user, I want to create, edit, and delete tasks so that I can schedule my work and keep it up to date. Creating a task requires a title, category, estimated duration, and priority. Editing allows modifying all fields. Deletion removes the task permanently.

### Task Status

#### Mark Task as Done / Not Done

As a user, I want to mark tasks as completed or revert them to not done so I can track my progress. **Part of UC-08.**

### Workload and Indicators

#### Daily Workload

As a user, I want the system to calculate the total estimated time of tasks for each day and show workload alerts so I know how much work I have assigned. In this version, thresholds are global and fixed. In future versions, they may become configurable.

**[Deferred — dashboard page, not yet built.]** Single-day workload alerts already exist on the Tasks page (UC-08); the full daily/weekly/monthly dashboard workload views are deferred to UC-09.

### Views

#### Calendar View (Date Range Filter)

As a user, I want to see tasks filtered by a date range so that I can view my schedule for specific periods. Full daily, weekly, and monthly views are deferred to a later layer.

### Filtering and Sorting

#### Filter and Sort Tasks

As a user, I want to filter tasks by category, plan, date range, status, or priority, and sort them by date, priority, or estimated time so that I can quickly find specific items. **Per-page search/sort/filter is implemented on the Categories (UC-06), Plans (UC-07), and Tasks (UC-08) pages.**

### Overdue and Notifications

#### Overdue Tasks

As a user, I want the system to identify tasks whose due date has passed and are still not marked as done so that I can plan to catch up. **Implemented as an Overdue status filter on the Tasks page (UC-08).**

#### Upcoming Tasks

As a user, I want to see tasks that are approaching within a defined window (today + X days) so that I can prepare for what's coming. **[Deferred — dashboard page, not yet built (UC-10).]**

### Reports

#### Performance Reports

As a user, I want to select a time range and see total tasks created, completed, completion rate, and overdue count so that I can analyze my performance. **[Deferred — report page, not yet built (UC-11).]**

### Plan Tracking

#### View Plan Tasks

As a user, I want to view a specific plan and see all tasks assigned to it so that I can track progress on that project. **Implemented as part of Plan Management (UC-07).**

#### Plan Progress

As a user, I want to see the progress of a plan based on how many of its tasks are completed so that I can track how close I am to finishing a project. **Implemented as part of Plan Management (UC-07).**

#### Plan Progress Tracking

As a user, I want to see real-time plan progress updates as I mark tasks done so that I always know the current status. **Layer 1 implemented (recomputed per visit) in UC-07; live cross-page reactivity deferred.**



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
  - Daily workload logic
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

### Manage Plans

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

### Manage Tasks

**Given** the user is on the task page for a specific day,
**When** they fill in the title, select a category, set an estimated duration, and submit,
**Then** the task appears in that day's task list.
**And** the daily workload is recalculated.
**And** if required fields are missing, submission is rejected.

**Given** the user is viewing a task,
**When** they modify any field and save,
**Then** the task is updated.
**And** the daily workload is recalculated if the date or estimated minutes changed.

**Given** the user is viewing a task,
**When** they click delete and confirm,
**Then** the task is permanently removed.
**And** the daily workload is recalculated.

### Mark Task as Done / Not Done

**Given** the user is viewing tasks for a day,
**When** they toggle the status of a task,
**Then** the task's done field updates immediately.
**And** the change is reflected in the UI without a page reload.

### Daily Workload

**[Deferred — dashboard page not yet built (UC-09).]** Single-day workload alerts already satisfy this on the Tasks page (UC-08).

**Given** the user is viewing a day,
**When** tasks exist for that day,
**Then** the total estimated minutes is summed and displayed.
**And** a workload alert is shown based on thresholds (Rest/<3h/<6h/6h+).

### Overdue Tasks

**Given** tasks exist with task_date < today and done = false,
**When** the user opens the Overdue section,
**Then** all such tasks are listed.
**And** tasks marked as done are excluded from the list.


### Performance Reports

**[Deferred — report page not yet built (UC-11).]**

**Given** the user selects a date range,
**When** they request a report,
**Then** the system displays: total tasks created, total completed, completion rate (%), and overdue count.
**And** the data is scoped to the authenticated user only.

### Calendar View

**Given** the user selects a date range,
**When** they apply the filter,
**Then** tasks within that range are displayed.
**And** the user can navigate between periods.

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

**[Deferred — dashboard page not yet built (UC-10).]**

**Given** the user has tasks within the notification window (today + X days),
**When** they open the dashboard,
**Then** upcoming tasks are displayed in a list.


## 1.4 – Requirements Prioritization (MoSCoW)

### Must Have (MVP Critical)

- User authentication (register, login, logout)
- Category CRUD
- Task CRUD (create, read, edit, delete)
- Assign task to exactly one category
- Set estimated duration per task
- Mark task as done / not done
- Daily view with task list
- Weekly view __(deferred — current range filter satisfies a reduced Layer 1)__
- Monthly calendar view __(deferred — date-range filter only)__
- Daily workload calculation with alerts __(implemented per-day on Tasks page; dashboard-level aggregation deferred)__
- Overdue tasks list __(implemented as an Overdue status filter on the Tasks page)__
- Upcoming tasks list (based on day_before_alarm) __(deferred — dashboard not built)__
- Plan CRUD
- Assign / remove task from plan
- Plan progress tracking __(Layer 1 done; live cross-page reactivity deferred)__

### Should Have (High Priority — Next)

- Filter tasks by category, plan, status, priority, date range __(done per page)__
- Sort tasks by date, priority, estimated time __(done per page)__
- Performance reports over a date range __(deferred — report page not built)__

### Could Have (Nice to Have)

- Profile editing (name, DOB, country, gender)
- Account deletion
- Admin panel for user management
