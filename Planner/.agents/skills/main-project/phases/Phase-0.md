# Phase 0 – Vision and Scope

## 0.1 – Product Vision

A daily and weekly task management system designed to replace tracking tasks in Telegram. It enables users to categorize tasks, organize them into plans or projects, assign them to specific calendar days, control daily workload, view completed and incomplete tasks across different days, and generate performance reports over selected time periods.

## 0.2 – User Roles

### Regular User

- Create and manage their own tasks
- View daily, weekly, and monthly plans
- Receive workload alerts
- Access personal performance reports

### Admin

- Perform high-level system management (e.g., manage user accounts, data cleanup)
- In future versions: manage plans, manage users, and more

In the MVP, the primary focus is on the Regular User role.
The Admin role is mainly limited to soft deletion and data management.

## 0.3 – MVP Scope

In the first version, the system must include at least the following features:

### Task and Category Management

- Create categories and assign each task to a category
- Add tasks for a specific day (date only, no starting time)
- Specify estimated duration for each task

### Plan Management

- Create plans to group related tasks under a common goal or project
- Assign a name and optional description to each plan
- Assign tasks to a plan (optional — tasks can exist without a plan)
- View all tasks within a plan
- Track plan progress based on completed vs. total tasks
- Edit or delete plans (deleting a plan is blocked if it still has tasks — restrict on delete)

### Daily Workload Calculation

- Calculate the total estimated time of tasks per day
- Display the day's status using color indicators based on workload:
  - 0 hours
  - Less than 3 hours
  - Less than 5 hours
  - Less than 8 hours
  - 8 hours or more
- Display appropriate warnings when the daily workload exceeds defined thresholds

### Task Views

- Daily view
- Weekly view
- Monthly calendar view

### Filtering and Sorting

- Filter tasks by category, plan, date range, status, or priority
- Sort tasks by date, priority, or estimated time

### Recurring and Multi-Day Tasks

- Ability to define tasks for multiple days (recurring or multiple dates)

### Task Status Management

- Mark tasks as completed
- If not marked as completed, tasks are automatically considered incomplete at the end of the day
- View completed and incomplete tasks from previous days

### Performance Reporting

- Generate performance reports for a selected time range (e.g., weekly or monthly)

### Overdue Tasks

- Display overdue tasks (past their scheduled date and not marked as done)

### Notifications

- Display upcoming tasks on the main dashboard (or in a separate list)
- Future enhancement: email notifications and other notification methods

### Task Operations

- Add
- Edit
- Delete

### Soft Delete

- User accounts are soft-deleted with obfuscated credentials
- Plans and categories are hard-deleted
- Plans with tasks cannot be deleted; categories with tasks cannot be deleted
