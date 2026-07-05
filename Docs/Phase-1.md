# Phase 1 – Requirements Analysis

## 1.1 – User Stories

### 1.1.1 – Categories & Organization

#### Create Category
    As a user, I want to create categories for my tasks so that I can organize them by topic (e.g., university, work, personal).

#### Assign Task to Category
    As a user, I want each task to belong to a specific category so that I can filter and review tasks based on their category.

Each task belongs to exactly one category.

### 1.1.2 – Task Management

#### Create Task for a Specific Day
    As a user, I want to create a task for a specific day so that I know what needs to be done on that day.

#### Set Estimated Time for Each Task
    As a user, I want to enter an estimated duration (e.g., in minutes/hours) for each task so the system can determine my daily workload.

#### Edit Task
    As a user, I want to edit my existing tasks (title, description, category, date, estimated time, recurrence settings, etc.) so that I can keep my schedule up to date if circumstances change.

#### Delete Task
    As a user, I want to delete a task so it no longer appears in my list. If the task belongs to a plan that gets deleted, the task is also deleted (cascade delete).

### 1.1.3 – Priority

#### Set Task Priority
    As a user, I want to assign a priority level (e.g., High, Normal, Low) to each task so that I can focus on more important tasks on busy days and include priority in filters and reports.

### 1.1.4 – Task Status

#### Mark Task as Done / Not Done
    As a user, I want to mark tasks as completed (Done) or revert them to Not Done so I can track my progress.

#### Automatic Not Done Status
    As a user, if I do not specify a task’s status by the end of the day, I want the system to automatically consider it “Not Done” so that performance reports remain accurate.

#### View Completed and Incomplete Tasks from Previous Days
    As a user, I want to view completed and incomplete tasks from past days to evaluate how well I followed my plan.

### 1.1.5 – Recurring / Multiple-Day Tasks

#### Define Recurring Tasks
    As a user, I want to assign a task to multiple selected days (e.g., specific weekdays or multiple dates) so that I don’t have to create repetitive tasks separately for each day.

### 1.1.6 – Views

#### Daily View
    As a user, I want to see all tasks for a specific day along with the total workload for that day.

#### Weekly View
    As a user, I want a weekly view to see tasks for the current week or any selected week.

#### Monthly Calendar View
    As a user, I want to see tasks displayed on a monthly calendar to have an overall view of my monthly schedule.

#### Create/View Tasks via Calendar
    As a user, I want to click on a specific day in the calendar to view its tasks or create a new task for that day.

### 1.1.7 – Filtering & Sorting

#### Filter Tasks
    As a user, I want to filter tasks by:
    - Category
    - Plan
    - Date or date range
    - Status (Done / Not Done)
    - Priority
    so that I can quickly find specific tasks.

#### Sort Tasks
    As a user, I want to sort tasks by:
    - Date
    - Estimated time
    - Status
    - Priority
    so that I can maintain a more organized view.

### 1.1.8 – Daily Workload & Color Alerts

#### Calculate Total Daily Task Time
    As a user, I want the system to calculate the total estimated time of tasks for each day so I know how much work I have assigned to myself.

#### Color-Code Days Based on Total Time
    As a user, I want each day in the calendar or daily view to have a status color based on total workload:
    - Total time = 0 hours → White
    - Total time < 3 hours → Green
    - Total time < 5 hours → Yellow
    - Total time < 8 hours → Red
    - Total time ≥ 8 hours → Black
    So that I can immediately recognize which days are light, moderate, or extremely busy.

#### Contextual Message Based on Workload
    As a user, I want to see a contextual message for each workload level (Green, Yellow, Red, Black), such as:
    - “Light day”
    - “Normal day”
    - “Busy day”
    - “Overloaded day”
    so that I can better evaluate my planning.

In this version, thresholds are global and fixed according to the color table. In future versions, they may become configurable.

### 1.1.9 – Overdue Tasks

#### Detect Overdue Tasks
    As a user, I want the system to identify tasks whose due date has passed and are still not marked as Done as “Overdue” so that I can plan to catch up.

#### Display Overdue List
    As a user, I want a dedicated section/list for Overdue tasks so I can quickly see what I have fallen behind on.

### 1.1.10 – Simple Notifications / Upcoming Tasks

#### Define Notification Window
    As a user, I want to define a time window (e.g., X days before the task date) so the system can mark tasks within that window as “Upcoming.”

#### Display Upcoming Tasks on Dashboard
    As a user, I want a table/list on the main dashboard showing tasks approaching within the defined window.

In future versions, notifications may be extended to email or other channels.

### 1.1.11 – Performance Reports

#### Performance Report for a Time Range
    As a user, I want to select a time range (e.g., one week or one month) and see:
    - Total tasks created
    - Total tasks completed
    - Completion rate (percentage)
    - Number of overdue tasks
    so that I can analyze my performance.

### 1.1.12 – Soft Delete Behavior

#### Soft Delete & Admin Management
    Soft deletion is only used for User accounts. Categories, tasks, and plans are hard-deleted. If a plan is deleted, all tasks within that plan are also cascade-deleted. If a category is deleted, tasks referencing it are prevented from deletion unless they are reassigned first.

### 1.1.13 – Plan Management

#### Create Plan
    As a user, I want to create a plan with a name and optional description so that I can group related tasks under a common goal or project.

#### Edit Plan
    As a user, I want to edit my plan's name, description, or date range so that I can keep my plans up to date.

#### Delete Plan
    As a user, I want to delete a plan so it no longer appears in my list. Deleting a plan also deletes all its tasks (cascade delete).

#### View Plan and Its Tasks
    As a user, I want to view a plan and see all the tasks assigned to it so that I can track progress on that project or goal.

#### Assign / Remove Task from Plan
    As a user, I want to assign a task to a plan or remove it from a plan so that I can organize tasks under the appropriate project.

#### Plan Progress
    As a user, I want to see the progress of a plan based on how many of its tasks are completed so that I can track how close I am to finishing a project.

## 1.2 – Functional Requirements

The system must:
- Allow creating, editing, and deleting categories.
- Allow creating, editing, and deleting tasks.
- Each task:
    - Must belong to exactly one category.
    - Must have exactly one scheduled date (day).
    - Must have an estimated duration (minutes/hours).
    - Must have a status (Done / Not Done).
    - May have a priority level.
    - May include recurrence/multi-day configuration.
- The system must:
    - Calculate total task time for each day.
    - Determine and display the appropriate color and contextual message for each day.
- Regarding plans:
    - Allow creating, editing, and deleting plans.
    - Each plan must have a name, start date, and finish date, and belong to exactly one user.
    - A task may optionally belong to a plan (zero or one plan per task).
    - A plan can contain multiple tasks.
    - Deleting a plan also deletes all its tasks (cascade delete).
    - Plans and categories have no direct relationship.
    - The system must display plan progress based on completed vs. total tasks.
    - Plans have a "done" boolean to mark completion.
- Display tasks in:
    - Daily view
    - Weekly view
    - Monthly calendar view
    - Support filtering and sorting by:
    - Category
    - Date / date range
    - Status
    - Priority
    - Estimated time
- Detect overdue tasks (past date and not Done).
- Display a list of overdue tasks.
- Identify upcoming tasks based on today’s date and the defined notification window.
- Display upcoming tasks on the main page or in a dedicated section.
- Never soft-delete tasks or plans; permanent deletion is immediate for both. Only User accounts use soft deletion for account management.
- Automatically consider tasks as “Not Done” if the user does not update their status by the end of the day (for reporting and overdue logic).

## 1.3 – Non-Functional Requirements

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
    - Daily workload & color logic
    - Reporting logic
    - Simple notification (Upcoming) logic
- Important logic components must be covered by unit tests and higher-level tests.

### Reliability
- Changing task status (Done/Not Done), editing, or soft deletion must not result in unintended data loss.
- The system must behave consistently and predictably in calculating Overdue, Upcoming, and reporting metrics.

### Simplicity
- The first version must remain as simple as possible in terms of UI and features.
- More advanced features (e.g., advanced notification settings, subscription plans) will be added in later phases.

## 1.4 – Acceptance Criteria

### AC‑01 – Create Category

**Given** the user is logged in and on the Category Management page,
**When** they enter a unique category name and submit,
**Then** the category is created and appears in the category list.
**And** if the name is empty or duplicate, an error is shown and the category is not created.

### AC‑02 – Create Task

**Given** the user is logged in and viewing a specific day,
**When** they fill in the title, select a category, set an estimated duration, and submit,
**Then** the task appears in that day's task list.
**And** the daily workload is recalculated.
**And** if required fields (title, category, duration) are missing, submission is rejected.

### AC‑03 – Mark Task as Done / Not Done

**Given** the user is viewing tasks for a day,
**When** they toggle the status of a task,
**Then** the task's `done` field updates immediately.
**And** the change is reflected in the UI without a page reload (Livewire).

### AC‑04 – Daily Workload Display

**Given** the user is viewing a day,
**When** tasks exist for that day,
**Then** the total estimated minutes is summed and displayed.
**And** the day shows the correct color indicator based on thresholds (White / Green / Yellow / Red / Black).
**And** the contextual message matches the color level.

### AC‑05 – Filter Tasks

**Given** the user is on a task list view,
**When** they select a filter (category, plan, status, priority, or date range),
**Then** only matching tasks are displayed.
**And** multiple filters can be combined.
**And** clearing filters restores the full list.

### AC‑06 – Delete Plan with Cascade

**Given** the user is viewing a plan that has tasks,
**When** they delete the plan and confirm the warning dialog,
**Then** the plan is permanently deleted.
**And** all tasks assigned to that plan are also deleted.
**And** the daily workload is recalculated for affected days.

### AC‑07 – Overdue Tasks Detection

**Given** tasks exist with `task_date < today` and `done = false`,
**When** the user opens the Overdue section,
**Then** all such tasks are listed.
**And** tasks marked as done are excluded from the list.

### AC‑08 – Performance Report

**Given** the user selects a date range,
**When** they request a report,
**Then** the system displays: total tasks created, total completed, completion rate (%), and overdue count.
**And** the data is scoped to the authenticated user only.

## 1.5 – Requirements Prioritization (MoSCoW)

### Must Have (MVP Critical)
- User authentication (register, login, logout)
- Category CRUD
- Task CRUD (create, read, edit, delete)
- Assign task to exactly one category
- Set estimated duration per task
- Mark task as Done / Not Done
- Daily view with task list
- Weekly view
- Monthly calendar view
- Daily workload calculation with color indicators (White / Green / Yellow / Red / Black)
- Overdue tasks list
- Upcoming tasks list (based on `day_before_alarm`)
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

### Won't Have (Deliberately Excluded for MVP)
- Email notifications
- Push notifications
- Social login (OAuth)
- Task sharing between users
- API endpoints (Livewire handles all interaction)
- Configurable workload thresholds (fixed for MVP)
