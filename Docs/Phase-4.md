# Phase 4 – Iterative Feature Development

## Objective

Implement each User Story completely, from design to testing and documentation, before moving to the next story.

---

## 4.1 – Select a User Story

For each iteration:

- Choose one User Story from the Phase-1 docs
- Review its requirements
- Review its Acceptance Criteria
- Define its Definition of Done

**Deliverable:** Fully understood story with clear success criteria.

---

## 4.2 – UX/UI Design

Design only what is needed for the current story.

### UX Flow

Define:
- Happy path
- Error paths
- Loading states
- Empty states

### Wireframe

Sketch (on paper or Figjam):
- Page layout
- Components
- Navigation flow

### Design System Usage

- Reuse existing components whenever possible
- Create new reusable components if necessary

**Deliverable:** Implementable UX/UI design.

---

## 4.3 – Technical Design

Before coding:

### Backend Design

Define:
- Domain entities involved
- Services needed
- Actions needed (if any)
- Validation rules
- Authorization rules

### Frontend Design (Livewire)

Define:
- Full-page or nested Livewire component
- Component properties
- Component actions (methods)
- Blade view structure

**Deliverable:** Technical implementation plan.

---

## 4.4 – Backend Implementation

Implement:

- Service classes
- Action classes
- Model scopes or accessors (if needed)
- Validation logic
- Authorization (Gates / Policies)

Apply:
- SRP (Single Responsibility Principle)
- DRY (Don't Repeat Yourself)
- Separation of Concerns
- Phase-3 naming conventions

**Deliverable:** Working backend functionality.

---

## 4.5 – Frontend Implementation

Implement:

- Livewire component class
- Blade view(s)
- Forms with validation
- User interactions (toggle done, filter, sort, etc.)

Verify:
- Responsiveness
- Error handling
- Loading states
- Empty states

**Deliverable:** Working user-facing feature.

---

## 4.6 – Testing

### Unit Tests

Test:
- Business rules (e.g., workload calculation, overdue detection)
- Domain logic in Services / Actions

### Integration Tests

Test:
- Livewire component behavior
- Database interactions
- End-to-end feature flow (where applicable)

### Manual Testing

Verify:
- Happy paths
- Edge cases
- Failure scenarios

**Deliverable:** Tested feature.

---

## 4.7 – Code Review & Refactoring

Review:
- Readability
- Simplicity
- Consistency with Phase-3 conventions
- Maintainability

Perform:
- Small refactors
- Cleanup
- Dead code removal

**Deliverable:** Clean and maintainable code.

---

## 4.8 – Documentation

Update:
- Architecture notes
- Technical decisions
- Phase-3 patterns (if new patterns emerge)

**Deliverable:** Up-to-date documentation.

---

## 4.9 – Story Acceptance

Verify:
- Acceptance Criteria satisfied
- Definition of Done satisfied
- Tests passing
- Documentation updated

**Outcome:**

- ✅ **Story Completed** — move to the next story

or

- 🔄 **Return to previous steps** for corrections

---

## Phase Completion Criteria

The phase is complete when:

- All User Stories are completed
- All Acceptance Criteria pass
- All tests pass
- Documentation is current
- The application is deployable

---

## Completed Use Cases

### UC-01 – Login

**Status:** Completed

**Goal:** Allow a guest user to sign in with email and password, receive validation or credential errors when needed, and enter the authenticated area after successful login.

**Routes:**
- `GET /login` → Livewire page `pages::auth.login`, guest-only route.
- `GET /dashboard` → temporary authenticated destination after login, auth-only route.

**Implementation Files:**
- `Planner/routes/web.php` — defines guest login route and authenticated dashboard route.
- `Planner/resources/views/pages/auth/⚡login/login.php` — Livewire page state, validation, action call, error handling, and redirect.
- `Planner/resources/views/pages/auth/⚡login/login.blade.php` — login form UI, field errors, credential/rate-limit error display, remember-me option.
- `Planner/app/Actions/Auth/LoginUserAction.php` — login business action; handles rate limiting, authentication attempt, session regeneration, and logging.
- `Planner/app/Enums/LoginResult.php` — result enum returned by the login action (`Success`, `Fail`, `RateLimited`).

**Testing Files:**
- `Planner/resources/views/pages/auth/⚡login/login.test.php` — co-located Livewire tests for rendering, validation, failed login, successful login, and rate limiting.
- `Planner/tests/Feature/Auth/LoginAccessTest.php` — route/middleware tests for guest and authenticated access.
- `Planner/tests/Feature/Actions/Auth/LoginUserActionTest.php` — action tests for result states and login logging.
- `Planner/tests/Pest.php` — Pest base setup for Feature and co-located Livewire tests with `LazilyRefreshDatabase`.
- `Planner/phpunit.xml` — adds the `Components` test suite for `resources/views/**/*.test.php`.

**Security & Reliability Notes:**
- Password validation is handled server-side by Livewire.
- Failed credentials use a generic error message.
- Login attempts are rate-limited by normalized email and IP address.
- Successful login regenerates the session.
- Login success, failed attempts, and rate-limited attempts are logged in the action layer.
- Blade output remains escaped; no raw user-controlled HTML is rendered.

**Acceptance Result:** UC-01 is accepted. Dashboard content and the future homepage/root route are intentionally deferred to later use cases.
