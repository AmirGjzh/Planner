# Livewire v4 – Comprehensive Performance Tricks Summary

This is a curated list of **every performance optimization** mentioned across the Livewire v4 documentation. Each trick includes a brief description and key edge cases to watch out for.

---

## 🚀 Reducing Server Requests & Payload

| Trick | Description | Edge Cases |
|-------|-------------|------------|
| **Use `.renderless` on actions** | Skips the re‑render phase for actions that don’t change the UI (e.g., logging, analytics). | Only use if the action has **no visual side effects**. Combining with `.async` makes it truly fire‑and‑forget. |
| **Use `.async` for side‑effect actions** | Runs actions in parallel without blocking the queue – ideal for logging, notifications, etc. | **Never use with state mutations** – race conditions will corrupt your data. |
| **Use `.debounce` / `.throttle` on `wire:model.live`** | Reduces the number of network requests sent while the user types. Default debounce is 150ms; adjust as needed. | Too short a debounce wastes requests; too long feels laggy. For `.change` or `.blur`, use those instead. |
| **Prefer `.blur` over `.live` for validation** | Validates only when the user leaves the field, cutting down on requests during typing. | Users might not see feedback until they tab out. Combine with a visual “dirty” indicator. |
| **Use `wire:model` without modifiers by default** | Only syncs on action submission – batching many changes into one request. | This is the most performant default; add modifiers only where real‑time feedback is truly needed. |
| **Dispatch events client‑side** | Instead of `$this->dispatch()` (which triggers a server request), use `$wire.$dispatch()` in Alpine to dispatch and handle locally. | If the listener is on another component and needs server data, you’ll still need a request. Only pure client‑side events benefit. |
| **Use `$parent` instead of events for parent‑child communication** | Calling `$parent.method()` avoids the event propagation and extra request overhead. | Tightly couples components – only use when the child knows exactly which parent action to call. |
| **Use `wire:key` in loops** | Helps Livewire track components correctly, preventing unnecessary re‑renders and mis‑ordering. | Without a unique key, Livewire may re‑render the entire list, causing performance and state issues. |
| **Use islands instead of nested components** | Islands isolate updates to a region, skipping re‑renders of the parent and other children. | Islands cannot be placed inside loops or conditionals – move the loop inside. |
| **Alpine‑driven island pattern (page boost — UC-06/07/08)** | For filter/search/sort regions: put `wire:model.live.debounce` (or native wire directives) INSIDE the island — Livewire auto‑scopes any `wire:` directive whose element sits inside an island, so only the island re‑renders. Only for Alpine `@click` calls must you scope manually with `$wire.$island('name').$set('prop', value)`. Add `always: true` so CRUD actions (parent renders) also refresh the island. Modals must live OUTSIDE the island; wrap the list/sort/search in the island, not the modal conditionals. For loading feedback, DON'T swap a skeleton into the grid — keep the real grid mounted and dim it (`wire:loading.delay.short.class="opacity-40"`) while a centered spinner overlays it (`wire:loading.delay.short` on an `absolute inset-0` wrapper); the container keeps its exact height so pagination doesn't jump. | Nightmare pitfalls: `wire:loading.delay.150ms` is NOT a valid v4 delay — Livewire's injected CSS only hides named delays (`delay.short`=150ms); unknown delays stay visible at rest. `wire:target="someProp"` matches payload updates/calls across the whole component, not per‑island — omit it inside islands. `wire:loading` show/hide sets `display` inline, clobbering `display:grid` on the element itself — keep grid classes on an INNER div. Don't hand‑roll Alpine `x-data`/`x-model`/`wire:ignore` on inputs to fake islands — you'll fight Alpine (e.g. `value` resolving to the DOM node) and lose focus semantics; native `wire:model.live` auto‑scopes by containment. |
| **Use `@persist` for elements that survive navigation** | Keeps audio/video players alive across `wire:navigate` page swaps, avoiding re‑initialisation. | Only works with `wire:navigate`. Element must be outside any Livewire component. |

---

## ⚡ Optimising Rendering & Computations

| Trick | Description | Edge Cases |
|-------|-------------|------------|
| **Use `#[Computed]` for expensive data** | Memoises the result within a single request – avoids re‑executing heavy queries when accessed multiple times. | Memoisation lasts **only for the current request**. For cross‑request caching, use `persist: true` or `cache: true`. |
| **Use `persist: true` on computed properties** | Caches the computed value across multiple requests for the **same component instance** (default 1 hour). | Cleared with `unset($this->prop)` – useful when the underlying data changes. |
| **Use `cache: true` on computed properties** | Shares a single cached value across **all instances** of the component – ideal for global data. | Use a custom `key` to avoid collisions. The cache is global, so clearing it affects all users. |
| **Use `#[Lazy]` for below‑the‑fold components** | Defers loading until the component scrolls into view, improving initial page load time. | Placeholders must match the root element type. Components are isolated by default; use `bundle: true` to combine many into one request. |
| **Use `#[Defer]` for above‑the‑fold heavy components** | Loads immediately after the page renders, not blocking initial paint. | Similar to `#[Lazy]`, but loads regardless of visibility. Bundle multiple if needed. |
| **Use `@island` for performance isolation** | Wraps a block to re‑render independently – great for expensive computed properties. | Use `lazy: true` or `defer: true` for on‑demand loading. `always: true` forces re‑render with parent – use sparingly. |
| **Use `skipRender()` / `.renderless` to skip full re‑renders** | Prevents the component from sending back HTML when the action doesn’t change the view. | Combine with `#[Async]` for background tasks. Only skip render if the UI truly doesn’t change. |
| **Use `wire:replace` for third‑party widgets** | Forces a complete replacement of the element’s children (or itself) to reset internal state without diffing. | Replace is more expensive than diffing; only use when diffing causes issues (e.g., with external libraries). |

---

## 📦 Caching & Persistence

| Trick | Description | Edge Cases |
|-------|-------------|------------|
| **Use `#[Session]` for user‑specific, non‑shareable state** | Persists property values in the session across page refreshes – no URL clutter. | Storing large objects or collections will bloat the session and slow down requests. Keep to simple values. |
| **Use `#[Url]` for shareable state** | Syncs with the query string – bookmarkable and SEO‑friendly. | Query strings have length limits. Use `except` to keep URLs clean. Too many URL parameters can hurt performance (more data sent). **Hydration reads by property name, not `as` alias:** `#[Url] public array $category_filter` hydrates from `?category_filter[]=…`, NOT `?categories[]=…`. Deep links (e.g. `route('tasks', ['category_filter' => [$id]])`) must use the SAME key Livewire writes — pick property names equal to the desired URL key, or pass `#[Url(as: 'categories')]` and link with that alias. |
| **Use `#[Cache]` with computed properties** | See `cache: true` – global caching across all component instances. | Requires a cache driver that supports tags if using `tags`. Cleared manually or by TTL. |
| **Use `bundle: true` with lazy/defer components** | Merges many lazy/deferred components into a single network request, reducing HTTP overhead. | Slower components will block faster ones in a bundle – use only when load times are similar. |
| **Use `wire:key` to force re‑initialisation** | Changing the `wire:key` on a child component will destroy and recreate it, resetting its internal state. | Overuse can cause unnecessary re‑renders. Only change keys when the component’s identity fundamentally changes. |

---

## 📱 Client‑Side Optimisations (No Server Round‑Trip)

| Trick | Description | Edge Cases |
|-------|-------------|------------|
| **Use `wire:show` for client‑side toggles** | Toggles `display: none` instantly without a server request. | The element remains in the DOM; good for frequently toggled content. For heavy content, consider `@if` to remove from DOM entirely. |
| **Use `wire:text` for optimistic UI updates** | Updates text content immediately when a property changes client‑side (via Alpine). | The server still needs to sync eventually – use with `wire:click` or `wire:model` to persist. |
| **Use `wire:bind` for reactive HTML attributes** | Binds `class`, `style`, `href`, etc. reactively on the client without extra requests. | Expressions are evaluated in Alpine – keep them lightweight. |
| **Use `wire:dirty` for unsaved indicators** | Detects client‑side changes without contacting the server – purely CSS toggling. | Works with `wire:target` to focus on specific fields. No performance cost. |
| **Use `wire:loading` with `data-loading` attribute (preferred)** | Styling via the `data-loading` attribute is more performant and doesn’t require `wire:target`. | Tailwind v4+ needed for advanced variants. Avoid heavy animations during loading. |
| **Use `wire:intersect` for lazy data loading** | Triggers actions when an element enters the viewport – ideal for infinite scroll. | Use `.once` for one‑time loads. `margin` can preload before the element is visible. |
| **Use `wire:poll` with `.visible` and longer intervals** | Polls only when the component is visible, reducing background requests. | Default background throttling reduces requests by 95% when tab is inactive. |
| **Use `wire:sort` for drag‑and‑drop** | Re‑orders DOM client‑side and only sends the final order to the server. | The server handler must update the database – the client does the heavy lifting. |
| **Use `wire:transition` for animations** | Uses the browser’s native View Transitions API – hardware‑accelerated and smooth. | Supports `prefers‑reduced‑motion` automatically. Use `#[Transition]` for directional types. |

---

## 🗂️ File Uploads & Large Data

| Trick | Description | Edge Cases |
|-------|-------------|------------|
| **Use S3 for temporary uploads** | Set `LIVEWIRE_TEMPORARY_FILE_UPLOAD_DISK=s3` to bypass your server and upload directly to S3. | Requires proper S3 lifecycle rules to clean up temporary files. Validation rules that access the file (e.g., `mimes`) will fail if S3 is not public. |
| **Use `->temporaryUrl()` for previews** | Generates a signed URL to display an image preview without storing it publicly. | Only works for images. If using S3, the URL is signed directly. |
| **Use progress indicators** | Listen to `livewire-upload-progress` events to show a progress bar – improves user experience. | Use Alpine to bind the progress value. Adds client‑side overhead but is negligible. |
| **Set global validation rules** | In `config/livewire.php`, set `rules` to reject unwanted files early. | Throttle uploads with `middleware` to prevent abuse. |

---

## 🧪 Testing & Debugging

| Trick | Description | Edge Cases |
|-------|-------------|------------|
| **Use `withoutLazyLoading()` in tests** | Disables lazy/defer behaviour so you can assert the full rendered content. | Only for testing – prevents placeholders from interfering with assertions. |
| **Test with `assertFileDownloaded`** | Fast and doesn’t actually download the file. | Works with any download response from Livewire. |
| **Use `assertDispatched` for events** | Verifies that events are fired correctly without needing to test the side effects. | Can check parameters with closures. |
| **Use `assertSet` to verify property values** | Ensures properties are correctly updated after actions. | Combined with `set()` to simulate user input. |

---

## 🧠 General Principles

- **Batch updates** – Livewire automatically batches multiple `wire:model` changes into one request when triggered by an action. Use this to your advantage.
- **Avoid storing large collections in public properties** – they are serialised and sent to the client. Use computed properties instead.
- **Use `#[Locked]` for IDs and authorisation‑sensitive data** – prevents client‑side tampering, reducing the need for extra validation.
- **Use `wire:ignore` for third‑party library elements** – prevents Livewire from interfering with their internal state.
- **Use `wire:key` for dynamic lists** – helps Livewire track items and avoid unnecessary re‑renders.
- **Use `data-loading` over `wire:loading`** – simpler, more flexible, and works with events automatically.

---

This list covers **all the performance-related tips** from the Livewire v4 documentation. Apply them judiciously – not every trick fits every scenario. Always measure the impact and choose the right tool for the job.
