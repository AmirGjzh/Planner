# Conceptual Domain Design

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
Textual representation of the relationships:

### User – Task
A User can have multiple Tasks.

Each Task belongs to exactly one User.

Relationship type:
- User (1) ——— (N) Task

### User – Category
A User can have multiple Categories.

Each Category belongs to exactly one User.

Relationship type:
- User (1) ——— (N) Category

### Category – Task
A Category can contain multiple Tasks.

Each Task belongs to exactly one Category.

Relationship type:
- Category (1) ——— (N) Task

### User – Plan
A User can have multiple Plans.

Each Plan belongs to exactly one User.

Relationship type:
- User (1) ——— (N) Plan

### Plan – Task
A Plan can contain multiple Tasks.

Each Task belongs to zero or one Plan (optional association).

Relationship type:
- Plan (0..1) ——— (0..N) Task

Note: Category and Plan have no direct relationship. They are independent organizational dimensions. Category classifies tasks by type/subject, while Plan groups tasks under a project or goal.

## 3. Main Use Cases
A Use Case represents a complete scenario of interaction between a user and the system.

Below they are written in a structured, step‑by‑step manner.

### 3.1 Authentication and Profile

#### UC‑01 – Login
Actor: User (guest who is not logged in)

Description:

The user logs into the system and views today's tasks.

Main Flow

- The user opens the website.
- If not logged in:
    - The user is redirected to the Login page, or
    - Clicks the Login button on the homepage.
- The user enters email/username and password.
- The system validates the credentials:
    - If successful → the user is logged in and redirected to the Today's Tasks page.
    - If unsuccessful → an error message is shown and the user can try again.

#### UC‑02 – Register
Actor: New user (guest)

Description:

A new user creates an account to manage personal tasks.

Main Flow

- The user clicks Register on the homepage.
- The registration form is displayed.
- The user enters required information (e.g., email/username, password, password confirmation, possibly name).
- The system validates the data:
    - Email format
    - Unique username/email
    - Password length/requirements
- If validation succeeds:
    - The account is created
    - A success/welcome message is shown
    - The user is redirected to the Login page

#### UC‑03 – View and Edit Profile
Actor: Logged-in user

Description:

The user views and edits profile information.

Main Flow

- The logged-in user clicks Profile from the navigation bar.
- The system displays current profile information.
- The user may edit fields such as:
    - First name
    - Last name
    - Date of birth
    - Country
    - Gender
- The user clicks Save.
- The system validates and stores the updated information and shows a success message.

#### UC‑04 – Logout
Actor: Logged-in user

Description:

The user logs out of the system.

Main Flow

- The user clicks Logout in the top navigation bar.
- The system invalidates the session/token.
- The user is redirected to the homepage or login page.
- The navigation bar again shows Login / Register options.

#### UC‑05 – Delete Account
Actor: Logged-in user

Description:

The user deletes their account along with all related tasks and categories.

(The exact behavior — soft delete or hard delete — can be finalized later.)

Main Flow

- The user opens Account Settings from the profile menu.
- At the bottom of the page, the user sees a Delete Account button.
- The system requests the current password for confirmation.
- The user enters the password and confirms.
- The system verifies the password.
- If valid:
    - The account deletion process begins
    - Related data may be soft-deleted or hard-deleted (to be finalized later)
- The user is logged out and sees a message such as:"Your account has been deleted."

### 3.2 Task Management

#### UC‑06 – Create Task
Actor: Logged-in user

Description:

The user creates a new task for a specific day.

Main Flow

- The user is on the Today's Tasks page or another day.
- The user clicks Add Task.
- A task creation form appears containing fields such as:
    - Title
    - Description
    - Category
    - Plan (optional)
    - Date (default = currently viewed day)
    - Estimated duration
    - Priority (High / Medium / Low)
    - Notification days before deadline
    - Recurrence settings
- The user submits the form.
- The system stores the task and associates it with the corresponding User, Category, and optionally a Plan.
- The task appears in that day's task list.
- The system recalculates the daily workload and updates the day's color and message.

#### UC‑07 – Edit Task
Actor: Logged-in user

Description:

The user modifies an existing task.

Important note:

In this version, the task date cannot be changed for simplicity.

Main Flow

- The user opens a day view or task list.
- The user clicks Edit next to a task.
- The edit form opens with current values pre-filled.
- The user can modify:
    - Title
    - Description
    - Category
    - Plan (optional)
    - Estimated time
    - Priority
    - Notification days before
    - Recurrence settings
- The date field is locked.
- The user clicks Save.
- The system saves changes and recalculates the day's workload if necessary.

#### UC‑08 – Delete Task
Actor: Logged-in user

Description:

The user deletes a task.

Main Flow

- The user clicks Delete next to a task.
- The system optionally displays a confirmation dialog.
- The user confirms deletion.
- The system permanently deletes the task.
- The system recalculates the daily workload and updates the day's color/message.

#### UC‑09 – Change Task Status (Done / Not Done)
Actor: Logged-in user

Description:

The user marks a task as completed or not completed.

Main Flow

- The user views tasks for a specific day.
- Each task has a checkbox or Done button.
- The user toggles the status.
- The system updates the task status.

System background logic:

If a task status is not updated by the end of the day, the system considers it Not Done.

This affects:

- reports
- overdue task detection

### 3.3 Category Management

#### UC‑10 – Manage Categories
Actor: Logged-in user

Create Category:

- The user opens the Category Management page.
- Clicks Add Category.
- Enters the category name (e.g., Work, University).
- Saves the category.
- The system creates the category for that user.

Edit Category:

- The user clicks Edit next to a category.
- The edit form opens.
- The user changes the name and saves.

Tasks linked to the category remain linked to the same category record.

Delete Category:

- Deletion is prevented if the category still has tasks assigned (restrict on delete).
- User must reassign or delete all tasks in the category before it can be deleted.

### 3.4 Views and Reporting

#### UC‑11 – View Tasks for a Specific Day
Actor: User

Flow

- The user selects a date or clicks a day in the calendar.
- The system shows tasks for that day (filtered by user).
- The total workload and status color/message are displayed.
- The user may create, edit, delete, or mark tasks as Done/Not Done.

#### UC‑12 – Weekly View
Actor: User

Flow

- The user switches the UI to Week View.
- The user selects a week (or navigates previous/next).
- The system displays tasks for the week in a table or grid.
- Each day shows its workload color indicator.

#### UC‑13 – Monthly Calendar View
Actor: User

Flow

- The user opens Calendar View.
- The system displays the current or selected month.
- Each day shows its workload color.
- The user can click a day to view or add tasks.

#### UC‑14 – Filter and Sort Tasks
Actor: User

Flow

The user can filter tasks by:

- Category
- Plan
- Date / date range
- Status (Done / Not Done)
- Priority
- Estimated duration

The user can sort tasks by:

- Date
- Priority
- Estimated time

The system updates the list accordingly.

#### UC‑15 – Daily Load Status
Actor: User

Description:

The user sees the workload level of a day using colors and messages.

Color rules:

- 0 hours → White
- < 3 hours → Green
- < 5 hours → Yellow
- < 8 hours → Red
- ≥ 8 hours → Black

The system calculates total estimated time and displays the corresponding message.

The same colors appear in weekly and monthly views.

#### UC‑16 – Upcoming Tasks
Actor: User

Description:

The user sees tasks that are approaching in the next few days.

Flow

- The user opens the dashboard.
- The system finds tasks within the configured window(today → today + X days).
- The tasks appear in an Upcoming Tasks list.

Future versions may send these notifications via email.

#### UC‑17 – Overdue Tasks
Actor: User

Description:

The user sees tasks that are past their scheduled date and still not completed.

Flow

- The user opens the Overdue Tasks section.
    - The system filters tasks where:
    - task_date < today
- status = Not Done
- The list is displayed.

The user may mark them as done or delete them.

Future versions may allow rescheduling.

#### UC‑18 – Performance Reports
Actor: User

Description:

The user views a performance summary over a chosen time range.

Flow

- The user opens the Reports page.
- Selects a time range (date A → date B).
- The system calculates:
    - total tasks created
    - completed tasks
    - incomplete/overdue tasks
    - completion rate
- The results are shown using numbers, simple charts, or tables.

### 3.5 Plan Management

#### UC‑19 – Create Plan
Actor: Logged-in user

Description:

The user creates a new plan to group related tasks under a project or goal.

Main Flow

- The user navigates to the Plans page.
- Clicks Create Plan.
- A form appears with fields such as:
    - Name
    - Description
    - Start Date (required)
    - End Date (required)
- The user fills in the fields and submits.
- The system stores the plan and associates it with the user.
- The plan appears in the user's plan list.

#### UC‑20 – Edit Plan
Actor: Logged-in user

Description:

The user edits an existing plan.

Main Flow

- The user opens the Plans page.
- Clicks Edit next to a plan.
- The edit form opens with current values pre-filled.
- The user modifies fields (name, description, date range) and saves.
- The system updates the plan.

#### UC‑21 – Delete Plan
Actor: Logged-in user

Description:

The user deletes a plan. All tasks assigned to the plan are also deleted (cascade delete).

Main Flow

- The user clicks Delete next to a plan.
- The system shows a confirmation dialog warning that all tasks in the plan will be deleted.
- The user confirms.
- The system deletes the plan and all its associated tasks.
- The plan and its tasks disappear from the user's lists.

#### UC‑22 – View Plan and Its Tasks
Actor: Logged-in user

Description:

The user views a specific plan and all tasks assigned to it.

Main Flow

- The user navigates to the Plans page.
- Clicks on a plan or selects View.
- The system shows plan details (name, description, date range, progress).
- Below the details, all tasks assigned to the plan are displayed.
- The user can create, edit, delete, or mark tasks as Done/Not Done from this view.
- The system calculates and displays plan progress as a percentage (completed tasks / total tasks).

#### UC‑23 – Assign / Remove Task from Plan
Actor: Logged-in user

Description:

The user assigns a task to a plan or removes it from a plan.

Main Flow

- The user opens a task creation or edit form.
- The form includes a Plan dropdown (optional, defaults to "No Plan").
- The user selects a plan (or changes to "No Plan") and saves.
- The system associates the task with the selected plan, or clears the association.

## 4. Conceptual Domain Model
At this level we define only the core attributes, not database types.

### 4.1 User
Suggested conceptual fields:

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

### Recurring Tasks Note
Since the system supports selecting multiple days for a task, there are two possible designs.

#### Option 1 – Simple MVP Model (Recommended)
When the user selects multiple seeds, the system creates separate independent tasks for each date.

Advantages:

- Simpler design
- No additional recurrence entities required
- Suitable for MVP

#### Option 2 – Advanced Model (Future)
Introduce a separate entity such as TaskPattern or recurrence configuration.

For Phase 2 and MVP, we choose Option 1.

Therefore, no recurrence pattern field is included in the domain model for now; recurrence will be handled in the service logic/UI layer.
