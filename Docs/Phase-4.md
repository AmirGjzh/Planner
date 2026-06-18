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
