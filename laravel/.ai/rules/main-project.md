---
paths:
  - '.ai/skills/main-project/**/*.md'
---

# Main Project

Rules for maintaining the project docs themselves (`.ai/`).

## .ai is the authoritative docs; .agents mirrors it by symlink

- `.ai/` is the committed source of truth. `.agents/skills/main-project` is a **symlink** to `.ai/skills/main-project` (gitignored), so edits to `.ai/` take effect automatically — never copy or edit `.agents/skills/main-project` directly.
- Keep `.ai/` docs in sync with code by fresh review; branch changes make git diffs unreliable for this.

## Known product decisions to respect

- The older Sahebi/Dokhmali choose-account login flow was removed — do not re-add it.