# Coding Standards

Rules and patterns for normal coding in this project — the cross-cutting quality bar every change meets as it is written, not a special pass run over finished work. Path-specific conventions live in [`.ai/rules/`](../../../rules/index.md) (read via the session ritual first); this doc covers what applies **everywhere**, whatever the file.

**See also:** [project rules](../../../rules/index.md) · [agentic setup](../agentic/setup.md) · [docs map](../SKILL.md)

## Contents

- [Rules that apply to every change](#rules-that-apply-to-every-change)
- [Consistency before invention](#consistency-before-invention)
- [Dead code and orphans](#dead-code-and-orphans)
- [Code](#code)
- [Comments](#comments)
- [Views and CSS](#views-and-css)
- [Tests](#tests)
- [i18n](#i18n)
- [Docs and repo sync](#docs-and-repo-sync)
- [Gate](#gate)

## Rules that apply to every change

1. **Stay in scope.** A change ships one concern. A defect noticed on the way is reported to the user, not silently fixed — an unreviewable mix of intended and opportunistic changes is how regressions hide. Fixes the user approves become their own change.
2. **Deletions need approval.** Every deletion (code, assets, tests) is listed first — item, evidence, why it's dead — and the user approves before it goes. The pest rule "do not delete tests without approval" applies; [`.ai/rules/tests.md`](../../../rules/tests.md) decides *which* twins die (same-layer subsumed twin → delete; cross-layer twins stay).
3. **Remove before you polish.** Dead code, unused assets, and stale docs go first, so nothing is prettified and then deleted.
4. **Green at every boundary.** Rerun the narrowest set after each edit (`php artisan test --compact <path>`); the full suite runs once, at the gate. If the container is unavailable while editing, batch the narrow reruns into the gate run and note it.
5. **Consistency first** (see next section): sibling files decide the pattern.
6. **No new tools or dependencies** without approval — sweeps use grep, Pint, and the existing suite; no analyzers, no packages.
7. **No git.** Everything stays in the working tree; the user reviews and commits.
8. **Docs carry no volatile counts.** Never write line/test/file/model counts into any doc — describe the shape or point at the source; a hard-coded count forces a pointless update whenever the thing changes (explicit identifier ranges like `UC-01 … UC-11` are content, not counts).

## Consistency before invention

- **Sibling files decide the pattern.** Before adding a method, component, class, or test, find the nearest existing example and copy its shape — never introduce a second way to do the same job.
- When two existing patterns disagree, prefer the one used by the majority of siblings and raise the outlier instead of blending them.
- Reuse the shared kit before hand-rolling: `x-mine.*` components over raw markup, shared support classes (`Minutes::format()`, `PersianNumber`, `Jalali`) over inline reimplementations, existing Actions over new layers.

## Dead code and orphans

Prevent it while writing; sweep it when an area is touched. A key exists only if something consumes it — dynamic and convention-registered consumers must be checked before anything is called dead:

- **Lang:** search every key in `lang/fa.json` against `resources/views`, `app`, `tests`. Beware dynamic keys (`__(ucfirst($name))` over `config('themes.name')`) and escaped keys (a call site's `don't` → `don\'t` never matches its literal — re-grep escaped before flagging).
- **Components:** every `resources/views/components/mine/*` directory needs an `x-mine.<name>` (or `x-mine.<dir>.<sub>`) reference outside its own file.
- **CSS:** every `@utility` in `resources/css/mine.css` must appear in views, JS, or tests.
- **PHP:** unreferenced classes, private methods, params, imports, enum cases. Check dynamic route-name consumers first (`route($x['route'])`, `:route="'profile'"`, `routeIs`, the `verified` middleware). Convention-registered code is referenced by name, not import — artisan commands, auto-discovered policies, providers/middleware, `require`d support files.
- **Files:** empty dirs, stray `.bak`/`.orig`, TODO/FIXME, published `resources/views/vendor/*` nothing renders, images/fonts nothing references.
- **Pairs:** a Blade `x-on:<event>.window` listener and the server `$this->dispatch('<event>')` feeding it are one contract — a listener without its dispatch (or vice versa) is dead. Sweep both halves.

## Code

- Ritual first: read [`.ai/rules/index.md`](../../../rules/index.md), then every rule file whose glob matches the files in scope, then `grep -rin '<keyword>' .ai/rules`.
- Delete: commented-out code, debug leftovers, unreachable branches, unused private methods/params/imports, stale PHPDoc.
- Trim: smallest coherent change — remove redundancy, never restructure; merge duplicate branches only when provably identical.
- Style: explicit return types and parameter hints; PHP 8 constructor property promotion; curly braces always; descriptive names (`isRegisteredForDiscounts`, not `discount()`).
- Architecture stays put: Actions remain `final` with one entry point and `LoggerInterface` as the only injected dependency; authorization stays in Actions; no new layers (no repositories, DTOs, events) — see [`.ai/rules/actions.md`](../../../rules/actions.md).
- Close: `vendor/bin/pint --format agent <touched paths>`.

## Comments

- Scope: non-test production code (`app/`, `routes/`, `resources/views/`, `config/`). Tests keep their trailing `//` why-note convention ([`.ai/rules/tests.md`](../../../rules/tests.md)).
- A comment earns its place by documenting what the code cannot show: a decision and its rejected alternative, a security/timing constraint, a cross-file contract (e.g. [`.ai/rules/auth.md`](../../../rules/auth.md)), a workaround. Test: would a future edit here plausibly go wrong without this note? If no → no comment.
- PHPDoc blocks over inline comments; inline `//` only for local rationale inside a method body. Blade uses `{{-- --}}` — HTML comments ship to the browser.
- Never: restating names or parameters, step narration, commented-out code, getter-style PHPDoc, volatile counts.

## Views and CSS

- Blade: remove redundant wrapper divs and depth, prefer the `x-mine.*` kit over hand-rolled markup, consistent class/attribute order, drop dead `wire:` targets. Markup pins, ARIA wiring, and the three-weight font rule stay (`.ai/rules/mine.md`, `components.md`, `css.md`).
- CSS: no unused `@utility`s, no deprecated v3 utilities, `gap` over sibling margins, honest `@source` list, `dark:` parity.
- Keep user-facing copy out of markup: `__('Natural language key')` only (see [i18n](#i18n)).

## Tests

- One defect or one contract per `it()` — arrange-act-assert; several decisions in one test get split.
- Prefer shared factory states/helpers over copy-paste; build owned records via relationship `->create()` (never real fixture/seed calls) — see [`.ai/rules/tests.md`](../../../rules/tests.md).
- Classify every test you touch: **trim** (split), **consolidate** (same-layer twin subsumed → delete with approval; cross-layer twins stay), **sharpen** (would pass if the feature vanished? add the missing failure mode — validation, 403, limiter outcomes), **add** (an uncovered decision or high-value failure mode).
- No framework-behavior tests; browser tier only for JS-only behavior, with `assertNoJavaScriptErrors()`.
- Rerun the narrowest set after every edit; the full suite waits for the gate.

## i18n

- No hardcoded user-facing strings — `__('Natural language key')` only.
- Exception: a locale-conditional native word rendered only by its own locale's branch (the fa `'کار'` count word on category cards, `'فارسی'` in the locale switcher) is a deliberate copy decision — copy stays user-owned; everything else goes through `__()`.
- `lang/fa.json`: no duplicate or orphan keys; `:count` placeholders stay plain (digits are converted at the call site — Western in en, Persian in fa, [`.ai/rules/lang.md`](../../../rules/lang.md)); framework files in `lang/en/` untouched.
- Locale-sensitive behavior (Sunday/Saturday week start, RTL, per-account locale) is a contract — never "fix" it.

## Docs and repo sync

- Docs match the code they describe: no stale step narration, examples use real names/shapes, `index.md` globs cover any path added.
- Ignore-file parity for every new file: `.gitignore` keeps repo junk untracked, root `.dockerignore` keeps dev-only tooling out of the build context (`.ai/`, `AGENTS.md`, `phpunit.xml`, `pint.json`, …). Check both, every time.
- Link related docs with relative markdown links instead of bare paths, so readers can click through.

## Gate

Definition of done for any change:

1. `vendor/bin/pint --format agent` on every touched PHP/Blade path.
2. Full `php artisan test --compact --parallel` (browser tier too — `composer test:browser` — if a JS/UI contract changed).
3. Re-read the whole diff against every mapped rule (the boost "How to Apply" final step).
4. Report to the user: what changed, deletions approved, tests added/trimmed, defects found-but-not-fixed, suite result.
5. The user reviews the tree and commits.
