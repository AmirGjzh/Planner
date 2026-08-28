# Planner — Full Project Audit Specification

## Purpose

Perform a **complete codebase and product audit** of the Planner project.

This audit is **NOT a production/deployment audit**.

Do NOT focus on or recommend infrastructure or post-development tools such as:

* Docker
* Redis
* Laravel Horizon
* Laravel Telescope
* Sentry
* Filament
* CI/CD
* VPS
* Cloudflare
* Deployment configuration
* Production monitoring
* Production backups
* External infrastructure services

Those topics will be handled **after the application itself is fully completed and audited**.

The goal of this audit is to determine whether the current Planner implementation is:

* Correct
* Secure
* Maintainable
* Well-structured
* Performant
* Consistent
* User-friendly
* Robust against edge cases
* Free of unnecessary complexity
* Ready to move to the next development stage

---

# Audit Rules

For every section:

1. Inspect the actual codebase.
2. Do NOT assume that something exists just because it should exist.
3. Do NOT modify code immediately.
4. First report what currently exists.
5. Identify problems, missing functionality, duplicated logic, unnecessary complexity, and potential bugs.
6. Consider both normal and edge-case behavior.
7. Inspect the interaction between related components, not only individual files.
8. Do not recommend changes just for the sake of changing things.
9. Prefer Laravel-native solutions when they are sufficient.
10. Avoid unnecessary abstractions, packages, or architectural complexity.
11. Distinguish real problems from subjective style preferences.
12. Check whether the current implementation actually matches the intended Planner behavior.

For every finding, classify its priority as:

* 🔴 **Critical** — Security issue, data corruption, serious bug, broken core behavior, or major architectural problem
* 🟠 **Important** — Significant correctness, maintainability, UX, or performance problem
* 🟡 **Nice to Have** — Improvement that is useful but not necessary
* 🟢 **Good** — Already implemented correctly
* ⚪ **Missing** — Required or useful functionality that does not currently exist

---

# 01 — Architecture & Project Structure

Inspect the overall Laravel project architecture.

Check:

* [ ] Is the overall project structure logical?
* [ ] Are responsibilities properly separated?
* [ ] Is business logic placed in appropriate locations?
* [ ] Are Livewire components reasonably sized?
* [ ] Are controllers reasonably sized?
* [ ] Are models reasonably sized?
* [ ] Are there excessively large classes or methods?
* [ ] Is logic unnecessarily duplicated?
* [ ] Are abstractions actually useful?
* [ ] Are there unnecessary abstractions?
* [ ] Are classes located in logical directories?
* [ ] Are naming conventions consistent?
* [ ] Are namespaces consistent?
* [ ] Are there temporary workarounds?
* [ ] Are there TODO/FIXME items that should be resolved?
* [ ] Is there dead code?
* [ ] Is there unreachable code?
* [ ] Are there unused classes?
* [ ] Are there unused methods?
* [ ] Are there unnecessary dependencies?
* [ ] Is the architecture unnecessarily complicated?

### Important

Do not recommend introducing Services, Repositories, DTOs, Actions, or other architectural patterns unless they solve a real problem in the current codebase.

---

# 02 — Database Schema & Migrations

Inspect all migrations and the resulting database design.

Check:

* [ ] Primary keys are appropriate
* [ ] Foreign keys are correctly defined
* [ ] Foreign key actions are correct
* [ ] Nullable columns are actually necessary
* [ ] Column types are appropriate
* [ ] Default values are correct
* [ ] Unique constraints are present where required
* [ ] Database constraints prevent invalid states
* [ ] Required indexes exist
* [ ] Unnecessary indexes do not exist
* [ ] Composite indexes are considered where appropriate
* [ ] Migration naming is consistent
* [ ] Migrations are clean and understandable
* [ ] Migrations can be executed reliably on a fresh database
* [ ] There are no assumptions about manually modified database state
* [ ] Soft deletes are used only where appropriate
* [ ] Timestamps are used correctly
* [ ] Sensitive data is stored safely
* [ ] Database structure matches the application's actual behavior

Pay special attention to frequently queried columns such as:

* `user_id`
* `plan_id`
* `category_id`
* `due_date`
* status fields
* date/time fields

Do not add indexes blindly. Consider actual query patterns.

---

# 03 — Eloquent Models

Inspect every Eloquent model.

Check:

* [ ] Relationships are correct
* [ ] Relationship names are consistent
* [ ] Relationship types are correct
* [ ] `belongsTo` is used correctly
* [ ] `hasMany` is used correctly
* [ ] `belongsToMany` is used correctly where needed
* [ ] `$fillable` / `$guarded` is secure
* [ ] Casts are correct
* [ ] Enums are used where they improve correctness
* [ ] Accessors are necessary
* [ ] Mutators are necessary
* [ ] Scopes are useful
* [ ] Global scopes are justified
* [ ] Model methods are not unnecessarily complex
* [ ] Business logic is not excessively concentrated inside models
* [ ] Relationships are not duplicated unnecessarily
* [ ] Unused relationships or methods are removed

---

# 04 — Relationships & Data Integrity

Audit all relationships between:

* Users
* Plans
* Tasks
* Categories
* Reports
* Other Planner entities

Check:

* [ ] A user can only access their own resources
* [ ] Child records cannot incorrectly belong to another user
* [ ] Foreign key relationships are consistent
* [ ] Deletion behavior is correct
* [ ] Orphan records cannot easily be created
* [ ] Duplicate records are prevented where necessary
* [ ] Impossible relationships are prevented
* [ ] Database constraints and application validation agree
* [ ] Relationships behave correctly after deletion
* [ ] Relationships behave correctly after updates

---

# 05 — Business Logic

Audit the actual business rules of Planner.

For every major feature ask:

> What is this feature supposed to do?

Then verify that the implementation actually enforces those rules.

Check:

* [ ] Business rules are clearly defined
* [ ] Business rules are enforced server-side
* [ ] Business rules are not duplicated across multiple components
* [ ] State transitions are valid
* [ ] Invalid state transitions are prevented
* [ ] Users cannot bypass business rules by manually modifying requests
* [ ] Edge cases are handled
* [ ] Date/deadline logic is correct
* [ ] Status logic is consistent
* [ ] Related records remain consistent
* [ ] Multi-step operations are safe
* [ ] Transactions are used where atomicity is actually required
* [ ] Partial failures cannot leave invalid application state

---

# 06 — Authentication

Audit all authentication functionality.

Check:

* [ ] Registration works correctly
* [ ] Login works correctly
* [ ] Logout works correctly
* [ ] Password hashing is secure
* [ ] Password validation is appropriate
* [ ] Password confirmation works
* [ ] Password reset works correctly
* [ ] Email verification works if implemented
* [ ] Remember Me behavior is correct
* [ ] Session handling is correct
* [ ] Authentication state cannot be bypassed
* [ ] Sensitive information is not exposed
* [ ] Authentication errors do not reveal unnecessary information
* [ ] Authentication actions have appropriate rate limiting

---

# 07 — Authorization & Access Control

This is one of the highest-priority sections.

Inspect all protected resources and actions.

Check:

* [ ] Policies are used where appropriate
* [ ] Authorization happens on the server
* [ ] UI visibility is not treated as authorization
* [ ] Users cannot access another user's resources
* [ ] Users cannot modify another user's resources
* [ ] Users cannot delete another user's resources
* [ ] Users cannot manipulate resource IDs to bypass authorization
* [ ] IDOR vulnerabilities are prevented
* [ ] Route model binding does not bypass ownership checks
* [ ] Livewire actions perform authorization checks
* [ ] Forms perform authorization checks
* [ ] Sensitive actions require appropriate permissions
* [ ] Admin-only behavior is properly protected

Test scenarios such as:

```text
User A → Resource A
User B → Resource B

User A attempts:
- View Resource B
- Edit Resource B
- Delete Resource B
- Complete Resource B
- Access Resource B through a manipulated URL
- Trigger Resource B actions through Livewire
```

All must be prevented.

---

# 08 — Routes & Middleware

Inspect all routes.

Check:

* [ ] Route names are consistent
* [ ] Route structure is logical
* [ ] Middleware is appropriate
* [ ] Authentication middleware is correctly applied
* [ ] Authorization middleware is correctly applied
* [ ] Route model binding is used appropriately
* [ ] Duplicate routes do not exist
* [ ] Unused routes do not exist
* [ ] Route parameters are handled safely
* [ ] Sensitive routes are protected
* [ ] HTTP methods are appropriate
* [ ] Routes follow consistent conventions

---

# 09 — Controllers

Inspect all controllers.

Check:

* [ ] Controllers are reasonably small
* [ ] Controllers do not contain excessive business logic
* [ ] Validation is handled appropriately
* [ ] Authorization is performed
* [ ] Responses are consistent
* [ ] Duplicate logic is avoided
* [ ] Unnecessary controllers/actions are removed
* [ ] Methods have clear responsibilities
* [ ] Queries are not unnecessarily complex

---

# 10 — Livewire Components

Audit every Livewire component.

Check:

* [ ] Components have clear responsibilities
* [ ] Components are not excessively large
* [ ] Public properties are necessary
* [ ] Sensitive state is not unnecessarily public
* [ ] State is validated
* [ ] Actions are authorized
* [ ] Database queries are efficient
* [ ] Unnecessary re-renders are avoided
* [ ] Unnecessary requests are avoided
* [ ] `wire:model` is used appropriately
* [ ] `wire:key` is used correctly where needed
* [ ] Loading states exist where appropriate
* [ ] Error states exist where appropriate
* [ ] Success feedback exists where appropriate
* [ ] Empty states exist where appropriate
* [ ] Component state does not become inconsistent
* [ ] Components do not duplicate business logic
* [ ] Components do not directly perform unnecessarily complex operations

---

# 11 — Blade Templates & Components

Inspect Blade templates and reusable UI components.

Check:

* [ ] Components are reusable where appropriate
* [ ] Duplicate markup is minimized
* [ ] Blade files are not excessively large
* [ ] Business logic is not placed in Blade
* [ ] Conditional rendering is understandable
* [ ] Loops are efficient
* [ ] Escaping is correct
* [ ] Dynamic content is safe
* [ ] Component naming is consistent
* [ ] Props are clearly defined
* [ ] UI primitives are reused consistently

---

# 12 — Database Query Audit

Perform an actual query audit of the application.

For every major page and action determine:

* [ ] How many database queries are executed?
* [ ] Which queries are necessary?
* [ ] Are there unnecessary queries?
* [ ] Are there duplicate queries?
* [ ] Are there N+1 queries?
* [ ] Is lazy loading causing repeated queries?
* [ ] Should `with()` be used?
* [ ] Should `withCount()` be used?
* [ ] Should `exists()` be used instead of loading full records?
* [ ] Should `select()` limit selected columns?
* [ ] Should pagination be used?
* [ ] Is `get()` being used where pagination is needed?
* [ ] Are queries executed inside loops?
* [ ] Are relationship queries repeated?
* [ ] Are dashboard queries efficient?
* [ ] Are report queries efficient?
* [ ] Are expensive queries repeated unnecessarily?

### Important

Do not optimize based on assumptions.

Inspect actual query behavior.

---

# 13 — Pagination, Search & Filtering

For every potentially large collection:

* [ ] Pagination exists where necessary
* [ ] Pagination is implemented efficiently
* [ ] Search is server-side where appropriate
* [ ] Search queries are efficient
* [ ] Filters are efficient
* [ ] Sorting is efficient
* [ ] Search/filter combinations are handled correctly
* [ ] Empty search results have a proper state
* [ ] Filters can be reset
* [ ] Query parameters are handled consistently
* [ ] Large datasets do not cause excessive memory usage

---

# 14 — Cache Audit

Audit the project for unnecessary repeated computation and database access.

For every frequently accessed piece of data ask:

1. Is it expensive to calculate?
2. Is it requested frequently?
3. Does it change infrequently?
4. Would caching actually improve performance?
5. Can the cache be invalidated reliably?

Check potential areas such as:

* Categories
* Dashboard statistics
* Reports
* User preferences
* Configuration
* Expensive calculations

For every existing cache:

* [ ] Cache key is correct
* [ ] Cache scope is correct
* [ ] TTL is appropriate
* [ ] Cache invalidation is correct
* [ ] Stale data is acceptable
* [ ] Cache does not create consistency bugs
* [ ] Cache is actually providing value

Do NOT add caching simply because caching exists.

---

# 15 — Validation

Audit every form and user input.

Check:

* [ ] Backend validation exists
* [ ] Required fields are validated
* [ ] Data types are validated
* [ ] String lengths are validated
* [ ] Numeric ranges are validated
* [ ] Date values are validated
* [ ] Relationships are validated
* [ ] Ownership is validated
* [ ] Unique constraints are respected
* [ ] File uploads are validated
* [ ] Validation messages are clear
* [ ] Validation messages are shown near the correct field
* [ ] Validation cannot be bypassed through direct requests
* [ ] Validation rules match database constraints

---

# 16 — File Uploads

If file uploads exist, inspect them thoroughly.

Check:

* [ ] File type validation
* [ ] MIME validation
* [ ] File size limits
* [ ] Safe filenames
* [ ] Storage location
* [ ] Public/private access
* [ ] Executable file prevention
* [ ] Image validation
* [ ] Image dimension limits
* [ ] Unused file cleanup
* [ ] Orphan file cleanup
* [ ] User ownership of uploaded files

---

# 17 — Error Handling

Audit all error scenarios.

Check:

* [ ] Validation errors
* [ ] Authorization errors
* [ ] 404 errors
* [ ] 403 errors
* [ ] 419 errors
* [ ] 429 errors
* [ ] 500 errors
* [ ] Database failures
* [ ] Network-related failures
* [ ] Livewire failures
* [ ] Unexpected exceptions

Check that:

* [ ] Users receive understandable feedback
* [ ] Sensitive technical information is not exposed
* [ ] Errors do not leave inconsistent state
* [ ] UI remains usable after an error

---

# 18 — Security Audit

Perform a general security review.

Check for:

* [ ] SQL injection
* [ ] XSS
* [ ] CSRF issues
* [ ] IDOR
* [ ] Mass assignment
* [ ] Privilege escalation
* [ ] Broken access control
* [ ] Unsafe file uploads
* [ ] Open redirects
* [ ] Sensitive information leakage
* [ ] Unsafe URL parameters
* [ ] Unsafe user-generated HTML
* [ ] Insecure direct object references
* [ ] Missing authorization
* [ ] Excessive data exposure

Also check:

* [ ] Secrets are not hardcoded
* [ ] Passwords are never logged
* [ ] Tokens are not exposed
* [ ] Sensitive database fields are protected

---

# 19 — Rate Limiting Audit

Identify every action that could be abused.

Check whether rate limiting is appropriate for:

* [ ] Login
* [ ] Registration
* [ ] Password reset
* [ ] Email verification
* [ ] Search
* [ ] File uploads
* [ ] Expensive operations
* [ ] Export operations
* [ ] Sensitive Livewire actions
* [ ] Public endpoints
* [ ] Future API endpoints

Do not add rate limiting everywhere without reason.

---

# 20 — Transactions & Atomicity

Identify multi-step database operations.

For each one ask:

> What happens if step 3 fails after steps 1 and 2 succeed?

Check:

* [ ] Transactions are used where necessary
* [ ] Partial updates cannot corrupt state
* [ ] Related records remain consistent
* [ ] Delete operations are safe
* [ ] Multi-record updates are atomic where necessary
* [ ] Exceptions correctly rollback operations

---

# 21 — Concurrency & Race Conditions

Try to break important actions using concurrent requests.

Check:

* [ ] Double-clicking an action
* [ ] Duplicate form submission
* [ ] Two simultaneous updates
* [ ] Two simultaneous deletes
* [ ] Two simultaneous completion actions
* [ ] Concurrent creation of unique records
* [ ] Concurrent updates to the same resource

Verify that:

* [ ] Duplicate operations are prevented where necessary
* [ ] Database constraints protect critical invariants
* [ ] Transactions are used where appropriate
* [ ] Atomic operations are used where appropriate

---

# 22 — Date & Time Handling

Planner is heavily date-oriented, so audit this carefully.

Check:

* [ ] Timezone strategy is consistent
* [ ] Dates are stored correctly
* [ ] Times are stored correctly
* [ ] User-facing formatting is consistent
* [ ] Deadline calculations are correct
* [ ] Today's tasks are calculated correctly
* [ ] Overdue logic is correct
* [ ] Future dates are handled correctly
* [ ] Midnight edge cases work
* [ ] Month/year boundaries work
* [ ] Date ranges are validated
* [ ] Report date ranges are correct

---

# 23 — Reports & Calculations

Audit all Planner statistics and reports.

Check:

* [ ] Task counts are correct
* [ ] Completed counts are correct
* [ ] Incomplete counts are correct
* [ ] Overdue counts are correct
* [ ] Time calculations are correct
* [ ] Date range filtering is correct
* [ ] User ownership is respected
* [ ] Queries are efficient
* [ ] Aggregations are correct
* [ ] Empty data is handled
* [ ] Large date ranges are handled
* [ ] Results are consistent with the underlying tasks/plans

---

# 24 — Notifications

If notifications are implemented:

* [ ] Correct events trigger notifications
* [ ] Notifications are not duplicated
* [ ] Read/unread state works
* [ ] Mark as read works
* [ ] Mark all as read works
* [ ] Notifications belong to the correct user
* [ ] Old notifications are handled appropriately
* [ ] Notification content is accurate
* [ ] Links inside notifications are correct

---

# 25 — UI / UX Audit

Audit every page as a real user.

For every page check:

### States

* [ ] Initial state
* [ ] Loading state
* [ ] Empty state
* [ ] Success state
* [ ] Error state
* [ ] Validation state
* [ ] Disabled state
* [ ] Hover state
* [ ] Focus state
* [ ] Active state

### Visual consistency

* [ ] Typography is consistent
* [ ] Spacing is consistent
* [ ] Border radius is consistent
* [ ] Colors come from the design system
* [ ] Buttons are consistent
* [ ] Inputs are consistent
* [ ] Cards are consistent
* [ ] Alerts are consistent
* [ ] Icons are consistent
* [ ] Visual hierarchy is clear
* [ ] Pages are not unnecessarily cluttered

---

# 26 — Responsive Design

Test:

* [ ] Small mobile
* [ ] Large mobile
* [ ] Tablet
* [ ] Laptop
* [ ] Desktop
* [ ] Large desktop

Pay special attention to:

* [ ] Header
* [ ] Sidebar
* [ ] Navigation
* [ ] Forms
* [ ] Modals
* [ ] Dropdowns
* [ ] Tables
* [ ] Cards
* [ ] Pagination
* [ ] Reports
* [ ] Long text
* [ ] Empty states

Ensure that mobile is not simply a compressed desktop layout.

---

# 27 — Accessibility

Check:

* [ ] Keyboard navigation
* [ ] Visible focus states
* [ ] Input labels
* [ ] Accessible buttons
* [ ] Appropriate contrast
* [ ] Semantic HTML
* [ ] Image alt text
* [ ] Modal accessibility
* [ ] Dropdown accessibility
* [ ] Checkbox accessibility
* [ ] Error message accessibility
* [ ] Focus management

---

# 28 — Reusable UI Components

Audit common UI components.

Check whether the project properly reuses components such as:

* [ ] Button
* [ ] Input
* [ ] Select
* [ ] Checkbox
* [ ] Modal
* [ ] Dropdown
* [ ] Card
* [ ] Badge
* [ ] Alert
* [ ] Pagination
* [ ] Empty State
* [ ] Loading State
* [ ] Separator

For each component ask:

> Is this reusable enough?

and:

> Are other pages unnecessarily recreating this component?

---

# 29 — State Management

Audit application state.

Check:

* [ ] There is a clear source of truth
* [ ] State is not unnecessarily duplicated
* [ ] Livewire state is minimal
* [ ] Session state is used appropriately
* [ ] URL state is used where useful
* [ ] UI state does not conflict with database state
* [ ] Stale state is handled
* [ ] Refreshing the page does not unexpectedly break state
* [ ] Browser back/forward behavior is reasonable

---

# 30 — UX Flow Audit

Walk through complete user journeys.

At minimum:

```text
Register
    ↓
Login
    ↓
Dashboard
    ↓
Create Category
    ↓
Create Plan
    ↓
Create Task
    ↓
Edit Task
    ↓
Complete Task
    ↓
View Reports
    ↓
Edit Profile
    ↓
Logout
```

For every flow check:

* [ ] Navigation is intuitive
* [ ] Loading feedback exists
* [ ] Success feedback exists
* [ ] Error feedback exists
* [ ] Validation is clear
* [ ] Back navigation works
* [ ] Refreshing does not break state
* [ ] Unauthorized actions are prevented
* [ ] The user always understands what happened

---

# 31 — Edge Case Audit

Actively attempt to break the application.

Test:

* [ ] Empty database
* [ ] No plans
* [ ] No tasks
* [ ] No categories
* [ ] One plan
* [ ] One task
* [ ] Hundreds/thousands of records
* [ ] Very long title
* [ ] Very long description
* [ ] Empty description
* [ ] Duplicate names
* [ ] Invalid dates
* [ ] Past dates
* [ ] Future dates
* [ ] Expired plans
* [ ] Deleted related records
* [ ] User with no activity
* [ ] User with heavy activity
* [ ] Invalid URL
* [ ] Manipulated ID
* [ ] Double submission
* [ ] Refresh during an operation
* [ ] Browser back after deletion
* [ ] Concurrent actions

---

# 32 — Performance Audit

Inspect application performance without prematurely optimizing everything.

Check:

* [ ] Slow database queries
* [ ] N+1 queries
* [ ] Excessive Livewire requests
* [ ] Excessive component rendering
* [ ] Large collections loaded into memory
* [ ] Unnecessary calculations
* [ ] Repeated calculations
* [ ] Large Blade rendering
* [ ] Large frontend bundles
* [ ] Unoptimized images
* [ ] Missing pagination
* [ ] Missing eager loading
* [ ] Unnecessary database queries

For every optimization recommendation explain:

1. What is slow?
2. Why is it slow?
3. How significant is the problem?
4. What is the simplest correct solution?

---

# 33 — Code Quality

Perform a general code-quality review.

Look for:

* [ ] Duplicate code
* [ ] Long methods
* [ ] Large classes
* [ ] Deep nesting
* [ ] Poor naming
* [ ] Magic numbers
* [ ] Magic strings
* [ ] Unnecessary comments
* [ ] Missing useful comments
* [ ] Dead code
* [ ] Unused imports
* [ ] Unused variables
* [ ] Unnecessary conditionals
* [ ] Over-engineering
* [ ] Under-engineering
* [ ] Inconsistent conventions
* [ ] Fragile code
* [ ] Hard-to-test code

Do not refactor code merely to make it look different.

The goal is:

> **Simple, readable, predictable code.**

---

# 34 — Type Safety & Static Analysis

Inspect type safety.

Check:

* [ ] Method return types
* [ ] Parameter types
* [ ] Nullable types
* [ ] Property types
* [ ] Collection types where useful
* [ ] Enum usage
* [ ] PHPDoc where actually useful
* [ ] Potential null errors
* [ ] Potential type errors
* [ ] Impossible states

If configured, review:

* Laravel Pint
* PHPStan / Larastan

Do not add annotations that provide no real value.

---

# 35 — Dependencies

Inspect Composer and NPM dependencies.

For every dependency ask:

* [ ] Is it actually used?
* [ ] Is it necessary?
* [ ] Does Laravel already provide the functionality?
* [ ] Is the dependency reliable?
* [ ] Is it actively maintained?
* [ ] Is it introducing unnecessary complexity?
* [ ] Is it used consistently?
* [ ] Can unused dependencies be removed?

Do not recommend adding packages unless there is a clear benefit.

---

# 36 — Configuration

Inspect application configuration.

Check:

* [ ] Configuration values are centralized
* [ ] Environment-specific values are not hardcoded
* [ ] Secrets are not hardcoded
* [ ] Configuration names are consistent
* [ ] Configuration is actually used
* [ ] Unused configuration is removed
* [ ] Environment variables have sensible defaults where appropriate

---

# 37 — Logging & Debugging Code

Audit development/debugging leftovers.

Check:

* [ ] No `dd()`
* [ ] No `dump()`
* [ ] No temporary debug output
* [ ] No test-only bypasses
* [ ] No temporary authentication bypasses
* [ ] No fake data accidentally used in production code
* [ ] No commented-out abandoned code
* [ ] No debug-only UI accidentally left in the application

---

# 38 — Localization

Planner uses Persian UI, so inspect localization carefully.

Check:

* [ ] RTL layout is consistent
* [ ] Persian text is natural
* [ ] UI copy is consistent
* [ ] User-facing language is friendly and consistent
* [ ] Validation messages are understandable
* [ ] Date formatting is consistent
* [ ] Number formatting is consistent
* [ ] Translation keys are consistent if localization files are used
* [ ] No unnecessary hardcoded duplicate strings exist

---

# 39 — Final Feature-by-Feature Audit

For every major Planner feature, perform the following review:

```text
Feature
├── UI
├── UX
├── Routes
├── Authorization
├── Validation
├── Business Logic
├── Database
├── Queries
├── Performance
├── Error Handling
├── Edge Cases
├── Responsive Behavior
└── Code Quality
```

Do not consider a feature complete just because the happy path works.

---

# 40 — Final "Does This Design Actually Make Sense?" Review

This is the final architectural/product review.

For every major feature ask:

> Is the current behavior actually what Planner should do?

Do not only ask:

> Does the current code work?

Also ask:

* Is this the simplest correct implementation?
* Is the user experience logical?
* Is the data model logical?
* Is the business rule logical?
* Is the feature consistent with the rest of Planner?
* Could this create future bugs?
* Could this create unnecessary technical debt?
* Is this feature over-engineered?
* Is this feature under-engineered?
* Is there a simpler Laravel-native solution?
* Is there anything here that will become difficult to maintain later?

---

# Required Audit Report Format

After completing each section, provide a report in this structure:

## Section: [Section Name]

### Current Implementation

Describe what currently exists in the codebase.

### 🟢 Good

List things that are already implemented correctly.

### 🟠 Important Problems

List significant issues.

For each issue include:

* File
* Class/component/method
* Problem
* Why it matters
* Recommended solution

### 🟡 Nice to Have

List useful but non-critical improvements.

### ⚪ Missing

List functionality or safeguards that are currently absent.

### 🔴 Critical

List security, correctness, data integrity, or architectural problems that should be fixed before continuing.

### Overall Status

Choose one:

```text
🟢 Good
🟡 Needs Improvement
🟠 Significant Issues
🔴 Critical Issues
```

### Recommended Next Actions

Give a short ordered list of what should be fixed first.

---

# Important Audit Principles

## Do Not Over-Engineer

Do not introduce complexity simply because it is considered "professional".

A simple Laravel implementation that is correct and maintainable is preferable to unnecessary architecture.

## Do Not Optimize Without Evidence

Do not recommend caching, indexing, abstraction, or refactoring without explaining the actual problem it solves.

## Security Over Convenience

If there is a conflict between convenience and data/security correctness, prioritize correctness and security.

## Backend Is the Source of Truth

Never consider a UI restriction to be sufficient authorization.

## Test Real Behavior

Inspect actual database queries, actual Livewire behavior, actual routes, actual validation, and actual authorization.

## Consider Edge Cases

The happy path is not enough.

## Preserve Existing Good Work

Do not rewrite working code simply because another implementation is theoretically cleaner.

---

# Final Audit Summary

At the end of the complete audit, provide:

## Overall Project Health

```text
Architecture:       [score]
Database:           [score]
Business Logic:     [score]
Security:           [score]
Authorization:      [score]
Performance:        [score]
UI/UX:              [score]
Maintainability:    [score]
Testing:            [score]
Code Quality:       [score]
```

Use a 10-point scale.

Then provide:

### 🔴 Must Fix

Issues that should be fixed before considering the current development phase complete.

### 🟠 Should Fix

Important improvements.

### 🟡 Could Improve

Nice-to-have improvements.

### 🟢 Already Strong

Parts of the project that are already well implemented.

### 🧭 Recommended Order

Provide the recommended order in which the identified issues should be fixed.

Do not begin discussing Docker, Redis, Horizon, Telescope, Filament, Sentry, CI/CD, deployment, or other production infrastructure unless explicitly requested in a later stage.

The purpose of this audit is to make the **current Planner application itself as correct, clean, robust, maintainable, performant, secure, and polished as reasonably possible before moving to production infrastructure.**
