# Cleaning Flow

The per-topic cleaning cycle: pick one topic — a UC or a non-UC commit (Header, theme adding, Error Pages) — run every subjob, finish the topic, move to the next. Cleaning is a separate flow from the UC-refactor flow tracked in the root `Refactor.md` (gitignored) — its output lands in its own commit(s), never mixed into a refactor step.

Scope of a topic's cleaning pass = the files its refactor/implementation commits touched (code + views + tests + docs), plus the full-repo sweeps below.

## Rules (every subjob, every topic)

1. **Behavior frozen.** Cleaning never changes logic, dates, query counts, rate-limit keys, or markup contracts. A change that would alter behavior stops and goes into the report as a defect — reported, not fixed, unless the user approves it as an explicit fix batch.
2. **Deletions need approval.** Every subjob produces a batch list (item + evidence + why it's dead); the user approves, then we delete. This includes tests — the pest rule "do not delete tests without approval" applies, and `.ai/rules/tests.md` decides *which* twins die (same-layer subsumed twin → delete; cross-layer twins stay).
3. **Kill before polish.** The dead-code sweep runs first so nothing is prettified and then deleted.
4. **Green at every boundary.** Rerun the narrowest set after each edit (`php artisan test --compact <path>`); the full suite runs once, at the gate. If the container is unavailable while editing, batch the narrow reruns into the gate run and note it in the report.
5. **Consistency first** (boost skills): sibling files decide the pattern; never introduce a second way to do the same job.
6. **No new tools or dependencies.** Sweeps use the grep playbook, Pint, and the existing suite — no deadcode analyzers, no new packages.
7. **No git.** Everything stays in the working tree; the user reviews and commits, one commit per topic.
8. **Docs carry no volatile counts.** This doc and anything cleaned under `.ai/` describe shape, not line/test/file numbers (see the guideline).

## Subjobs

### Sweep — dead code & unused assets (full repo)

Runs first, every cycle; also catches cross-topic fallout from earlier passes.

- **Lang:** extract every key from `lang/fa.json` and search `resources/views`, `app`, `tests` for it. Beware dynamic keys: the theme-name keys are consumed through `__(ucfirst($name))` over `config('themes.name')` and live without a literal.
- **Components:** every `resources/views/components/mine/*` directory must have a `x-mine.<name>` (or `x-mine.<dir>.<sub>`) reference outside its own file.
- **CSS:** every `@utility` in `resources/css/mine.css` must appear in views, JS, or tests.
- **PHP:** unreferenced classes, private methods, params, imports, enum cases. Route names must be checked for dynamic consumers first (`route($x['route'])`, `:route="'profile'"`, `routeIs`, the framework `verified` middleware) before being called dead. Convention-registered code is referenced by name, not import — artisan commands, auto-discovered policies, framework-registered providers/middleware, `require`d support files.
- **Files/orphans:** empty dirs, stray `.bak`/`.orig`, TODO/FIXME, published `resources/views/vendor/*` overrides nothing renders, images/fonts nothing references.

### Code (non-tests)

- Ritual first: read `.ai/rules/index.md`, then every rule file whose glob matches the files in scope, then `grep -rin '<keyword>' .ai/rules`.
- Delete: commented-out code, debug leftovers, unreachable branches, unused private methods/params/imports, stale PHPDoc.
- Trim: smallest coherent change — shrink by removing redundancy only, never restructure; merge duplicate branches only when provably identical.
- Style: PHPDoc over inline comments; comments say *why*, drop *what*; explicit return types and parameter hints; constructor promotion; curly braces always; descriptive names.
- Architecture untouched: Actions stay `final` with one entry point and `LoggerInterface` as the only injected dependency; authorization stays in Actions; no new layers (no repositories, DTOs, events).
- Close: `vendor/bin/pint --format agent <touched paths>`.

### Views & CSS (prettiness/minimalism)

- Blade: remove redundant wrapper divs and depth, prefer the `x-mine.*` kit over hand-rolled markup, consistent class/attribute order, drop dead `wire:` targets. Markup pins, ARIA wiring, and the three-weight font rule stay.
- CSS: drop unused `@utility`s (from the sweep), no deprecated v3 utilities, `gap` over sibling margins, honest `@source` list, `dark:` parity.
- Rules in charge: `.ai/rules/island.md`, `animate.md`, `css.md`, `components.md`, `mine.md`.

### Commentary (why-comments)

- Scope: non-test production code (`app/`, `routes/`, `resources/views/`, `config/`). Tests keep their trailing `//` why-note convention under the Tests subjob.
- A comment earns its place by documenting what the code cannot show: a decision and its rejected alternative, a security/timing constraint, a cross-file contract (e.g. `.ai/rules/auth.md`), a workaround. Test: would a future edit here plausibly go wrong without this note? If no → no comment.
- PHPDoc blocks over inline comments; inline `//` only for local rationale inside a method body. Blade uses `{{-- --}}` — HTML comments ship to the browser.
- Never: restating names or parameters, step narration, commented-out code, getter-style PHPDoc, volatile counts.
- Close: `vendor/bin/pint --format agent <touched paths>`; the diff must contain comment lines only; the report lists the comment sites added.

### Tests

- Inventory: `tests/Feature/Actions/<Area>/`, `tests/Feature/<Area>/`, co-located `resources/views/pages/**/*.test.php`, `tests/Browser/`.
- Per test, classify (`.ai/rules/tests.md` decides ties):
  - **trim** — several decisions in one `it()` → one defect or one contract per test, arrange-act-assert;
  - **consolidate** — same-layer twins where one subsumes the other → delete the subsumed one; cross-layer twins (page vs action) stay;
  - **delete (approval)** — obsolete tests covering behavior a topic removed, tests whose name promises what `Livewire::test` cannot observe, zero-assertion filler;
  - **sharpen** — shallow tests that would pass if the feature vanished; missing failure modes (validation, 403, limiter outcomes, dispatch failure);
  - **add** — an uncovered decision from the topic's changed code or a high-value failure mode.
- Keep tests small: shared factory states/helpers over copy-paste, no framework-behavior tests, browser tier only for JS-only behavior (with `assertNoJavaScriptErrors()`).
- Rerun the narrowest set after every edit; the full suite waits for the gate.

### i18n

- No hardcoded user-facing strings in touched views — `__('Natural language key')` only.
- `lang/fa.json`: no duplicate or orphan keys; `:count` placeholders stay Western-digit; framework files in `lang/en/` untouched.
- Locale-sensitive behavior (Sunday/Saturday week start, RTL, per-account locale) is a contract — sweep only, never "fix".

### Docs & repo sync

- The topic's docs — phase files, touched `.ai/rules/*.md`, README — must match the code they describe: no stale step narration, examples that match real names/shapes, `index.md` globs cover any path the topic added.
- Ignore-file parity for every file the topic added: `.gitignore` keeps repo junk untracked, root `.dockerignore` keeps dev-only tooling out of the build context (`.ai/`, `AGENTS.md`, `phpunit.xml`, …). Check both, every time.
- No volatile counts while touching docs.

### Gate (definition of done)

1. `vendor/bin/pint --format agent` on every touched PHP/Blade path.
2. Full `php artisan test --compact` (browser tier too if a JS/UI contract changed).
3. Re-read the whole diff against every mapped rule (the boost "How to Apply" final step).
4. Emit the **cleaning report**: per subjob — deletions with evidence (the approval list), files shrunk, tests trimmed/added, comment sites added, defects found-but-not-fixed; suite runtime before/after.
5. User reviews the tree and commits (one commit per topic).

## Cycle order

**Sweep → Code → Views & CSS → Commentary → Tests → i18n → Docs & repo sync → Gate** — sweep first so dead weight is never polished, gate last so nothing leaves unverified. Finish the topic fully before starting the next one.
