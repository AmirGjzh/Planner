---
paths:
  - '.ai/skills/main-project/**.md'
---

# Main Project

## Keep .ai docs synced with code; mirror to .agents
`.ai/` is the committed authoritative docs; `.agents/skills/main-project/` is the gitignored runtime skill mirror and must be re-copied from `.ai/skills/main-project/` after any edit. Docs must match code by fresh review (branch changes mean git diffs are unreliable). User's UI language preference is `users.locale` applied by `HasUser::boot()` — the Sahebi/Dokhmali choose-account login was removed and must not be re-added.
