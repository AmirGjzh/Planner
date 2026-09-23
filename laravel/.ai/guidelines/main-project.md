# Planner — Project Guideline

Planner is a personal task-planner web app: tasks, categories, and plans with a workload-aware weekly dashboard, a "tasks needing attention" list, date-range views with search/filter/sort, and performance reports. Fully implemented (UC-01..UC-11) with two UI languages.

## Session Ritual (mandatory, in order)

1. **Activate the `main-project` skill** — it holds the UC numbering, implementation status, phase docs, docker docs, and key decisions.
2. **Read `.ai/rules/index.md`**, then read every rule file whose `paths` glob matches the files you will touch.
3. **`grep -rin '<keyword>' .ai/rules`** — catches rules a path match alone misses.
4. Only then plan or edit code, and follow every matching rule.

## Stack (verify before relying on an API)

| Concern | Version |
| --- | --- |
| PHP | 8.4 (`composer show --direct` to confirm any package) |
| Laravel | 13 (`laravel/framework`) |
| Livewire | 4 — full pages are multi-file SFCs in `resources/views/pages/⚡*/`; routed via `Route::livewire('/path', 'pages::<name>')` |
| Tailwind | 4 — CSS-first (`@import "tailwindcss"`, `@theme`), no `tailwind.config.js` |
| Testing | Pest 4 — `it()` + `expect()`, in-memory SQLite (`:memory:`), LazilyRefreshDatabase wired in `tests/Pest.php` |
| Data | MySQL + Redis (both in Docker; sessions/cache on Redis) |

## Architecture (what to preserve)

- **Actions** are the domain layer: `final class`, one public entry method (`execute()`, or domain-named like `ReportsAction::summary()`), primitives/`Request` params, `LoggerInterface` as the only injected dependency, `XxxResult` enums as return types. No repositories, no DTOs, no events/queued jobs.
- **Authorization lives in Actions**: `abort_unless($user->can(...), 403)` — never `$this->authorize()` or `@can`. Policies auto-discover from `app/Policies`.
- **Rate limiting is per-action** with `Str::transliterate('kebab-prefix:identifier')` keys; cleared on success; `available_in` logged for outcomes. Follow the exact key shapes in the rules.
- **UI language is per-user**: the shared `HasUser` trait's `boot()` runs `app()->setLocale($this->user->locale)` on every Livewire request (initial + rehydration). Guest pages use the app locale; only auth boundaries set the locale explicitly.
- **i18n**: JSON string keys (`__('My tasks')`) in `lang/fa.json`; `lang/en/` holds only framework files. Week start is locale-aware: Sunday for en, Saturday (Jalali) for fa.

## Environment (all commands run inside the dev container)

```bash
./planner-dev up      # start workspace + mysql + redis
./planner-dev shell   # open a shell in the container (cwd = /var/www = laravel/)
composer run dev      # app server (port 8000) + queue worker + Vite together
php artisan serve --host=0.0.0.0 --port=8000   # manual app server
php artisan migrate   # after any schema change
php artisan test      # fast, in-memory SQLite, never touches dev data
```

Vite dev binds `0.0.0.0` (no flag). If the UI doesn't reflect a frontend change, the user must run `npm run dev` / `composer run dev` / `npm run build`.

## Coding rules (non-negotiable)

- Follow every matching rule in `.ai/rules` before writing code.
- **Never touch git.** Do not run `git add`, `git commit`, `git push`, `git restore --staged`, or any other index/staging/branch command — all git work is done by the user. Leave every change in the working tree for them to review and stage themselves.
- **New file? Decide both ignore files.** `.gitignore` keeps the repo clean — secrets, local env, and generated files stay untracked; shared config (e.g. `pint.json`) stays committed. The root `.dockerignore` keeps images clean — dev-only tooling (`AGENTS.md`, `.ai/`, `phpunit.xml`, `pint.json`, …) never enters the build context; anything the image needs must not be listed. Check both whenever you add or bring in a file.
- Use the boost agent skills for Laravel/Livewire/Tailwind/testing patterns and the `search-docs` MCP tool for exact API syntax.
- Pest: `php artisan make:test --pest`; feature tests first; co-located `.test.php` page tests live beside pages in `resources/views/pages/⚡<name>/`.
- Run `vendor/bin/pint --dirty --format agent` after modifying PHP or Blade files (Blade formatting is enabled via `pint.json`).
- **Docs: no volatile counts.** Never hard-code counts that drift with every change (line counts, file/test/model counts, theme/UC counts) — describe the shape or point at the source; a redundant count forces a pointless doc update whenever the thing changes. Explicit identifier ranges (e.g. `UC-01 … UC-11`) are content, not counts.
- Keep going in `.ai/`: this guideline, the rules, and the skill docs are the contract for the code.

## Docs map

The full docs map (phases, docker, git workflow, agentic environment) lives in the `main-project` skill. `.ai/` is the committed, authoritative docs; `.agents/skills/main-project` is a symlink that mirrors it automatically — edit `.ai/` only.

## Reply style

Be concise; focus on what matters rather than restating obvious details.