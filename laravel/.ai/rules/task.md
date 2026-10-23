---
paths:
  - 'app/Actions/Task/**'
  - 'app/Enums/TaskPriority.php'
---

# Task

Audit decision for task CRUD (UC-08), partially mirroring the category rules (UC-06).

## Titles are squished, never case-folded

- Titles normalize with `Str::squish()` on both layers: the page canonicalizes `add_title`/`edit_title` before validation (whitespace-only must hit the `required` message) and `CreateTaskAction`/`EditTaskAction` squish again at the domain boundary (the category/plan pattern). Edge trim covers ZWNJ (`Str::trim`'s charlist — a half-space-only title would otherwise render invisible and slip past `required`), internal whitespace runs collapse to one space, and the casing the user typed is stored — tasks have no per-user uniqueness requirement, so there is nothing case-folding could buy. Descriptions keep the `nullableString` edge-strip only (multi-line text — no collapsing).
- Search: `whereRaw('LOWER(title) LIKE ?', [Str::lower('%'.$term.'%')])` in the Livewire page; the term is `trim()`med once in the computed (categories/plans parity — `"hello "` must still match).

## Every sort carries a deterministic tiebreaker

`date`→id, `priority`→id, `estimated`/`load`→id, `state`→task_date,id, `latest`→created_at,id desc; an unknown `?sort=` value falls through to `orderBy('id')` instead of no ORDER BY at all (the plans UC-07 precedent — ties would otherwise shuffle between paginated requests).

## The un-done cascade is surfaced as a toast

`ToggleTaskDoneAction` returns `Reactivated` when un-doning reopened a completed plan (R1); `reopenTask()` keeps the task toast and dispatches the existing `Your plan re activated` key as a second toast. Completing can never cascade, so `completeTask()`'s match maps `Reactivated` to `null` for totality only.

## Zero categories: warning alert, not a dead end

With no categories, the add/edit modals render a `warning` `mine/alert` above the form — title `No categories yet!`, body the sentence `First create a category from :link.` whose `:link` is the `mine-text-link` anchor → `/categories` (`wire:navigate`), localized as the `here`/`اینجا` word — instead of stranding the user on the "No categories yet" option (the F4 dead-end). The body embeds the link via the `{!! __('… :link.', ['link' => '<a…>…</a>']) !!}` idiom (forgot-password's `:email` precedent); a bare under-select link read as a blank dead-end and was reworked into the alert at sign-off. Alert copy follows the house pattern: titles end in `!`/`?`, bodies end in `.`.
