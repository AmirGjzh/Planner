# Phase 0 – Vision and Scope

## 0.1 – Product Vision

A task management system designed to make planning easier. It enables users to categorize tasks, organize them into plans or projects, assign them to specific days, control daily workload, view completed and incomplete tasks across different days, and generate performance reports over selected time periods.

## 0.2 – User Roles

### Regular User

- Create and manage their own tasks
- View tasks for a single day or a custom date range
- Receive workload alerts on the dashboard
- Access personal performance reports

### Admin

- Perform high-level system management (e.g., manage user accounts, data cleanup)
- In future versions: manage plans, manage users, and more

In the MVP, the primary focus is on the Regular User role.
The Admin role is mainly limited to soft deletion and data management.

## 0.3 – MVP Scope (11 Use Cases)

The MVP is organized into exactly 11 use cases (UC-01 … UC-11). Each part below maps 1:1 to a UC spec in Phase-2.

### UC-01 – Login

- Sign in with email and password
- Invalid credentials produce a clear error message

### UC-02 – Register

- Create an account with a username, email, and password
- Duplicate usernames and emails are rejected

### UC-03 – View and Edit Profile

- View profile information (name, date of birth, country, gender)
- Edit and save profile fields

### UC-04 – Delete Account

- Delete the account with password confirmation
- User accounts are soft-deleted with obfuscated credentials

### UC-05 – Logout

- End the session securely from the navigation bar

### UC-06 – Manage Categories

- Create categories and assign each task to a category
- Rename or delete categories
- Deleting a category with tasks is blocked (restrict on delete)

### UC-07 – Manage Plans

- Create plans to group related tasks under a common goal or project
- Assign a name and optional description to each plan
- Assign tasks to a plan (optional — tasks can exist without a plan)
- View all tasks within a plan
- Track plan progress based on completed vs. total tasks
- Edit or delete plans (deleting a plan is blocked if it still has tasks — restrict on delete)

### UC-08 – Manage Tasks

- Add tasks for a specific day (date only, no starting time)
- Specify estimated duration for each task
- Mark tasks as done / not done directly from the task list
- If not marked as completed, tasks are automatically considered incomplete at the end of the day
- View completed and incomplete tasks from previous days
- View tasks for a single day or a custom date range
- Filter tasks by category, plan, date range, status, or priority
- Sort tasks by date, priority, or estimated time

### UC-09 – Daily Workload

- Calculate the total estimated time of tasks per day
- Display the day's status using color indicators based on workload
- Display appropriate warnings when the daily workload exceeds defined thresholds

### UC-10 – Tasks Needing Attention

- Display tasks that need attention (overdue or whose alarm window has started) on the dashboard
- Future enhancement: email notifications and other notification methods

### UC-11 – Reports

- Generate performance reports for a selected time range (week/month presets or a custom date range)
- Show a tasks summary (total, completed, completion rate, estimated time), a plans summary (total, completed), and a per-day workload chart

> **Cross-cutting:** Categories and plans are hard-deleted; soft deletion applies only to user accounts (UC-04). Plans and categories with tasks cannot be deleted (restrict on delete).
