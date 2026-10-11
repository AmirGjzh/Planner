---
paths:
  - 'app/Console/**'
---

# Commands

Conventions for artisan command classes under `app/Console/**`.

## Commands declare their signature with attributes

- `final class Xxx extends Command` carrying `#[Signature('themes:favicons')]` and `#[Description(...)]` — the attribute style, not the `$signature` property stub.
- `handle()` returns `self::SUCCESS` / `self::FAILURE` and reports every failure with `$this->error(...)` before returning; no restating PHPDoc on `handle()` (the `#[Description]` already says what it does) and no thrown exceptions for usage problems.
- Validate preconditions **before** writing anything (template present, config complete), so a failed run never leaves a half-generated directory behind.
- Commands are re-runnable by overwriting in place: the per-theme favicons are regenerated from the template on every run, never merged with previous output.
