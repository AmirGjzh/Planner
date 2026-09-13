# Planner

> Introduction to the project — to be written later.

---

## 2. How to use this project

This guide shows you how to set the app up, run it, update it, and make sure it's always
working well. Take it step by step and you'll be fine.

### What you need

- **Docker with Docker Compose** — any recent version.
- **Git** — used to download and update the project.
- An **internet connection** — only the very first time.

### Project layout

```
planner/                          # this project (you are here)
├── planner                       # production commands: ./planner up, ./planner ps, ...
├── planner-dev                   # development commands: ./planner-dev shell, ...
├── laravel/                      # the app itself
│   ├── compose.production.yml    # production stack (used by ./planner)
│   ├── compose.development.yml   # development stack (used by ./planner-dev)
│   ├── .env                      # your settings file (created below)
│   └── ...                       # app code, kept out of the images
└── docker/                       # Docker image definitions, shared by both stacks
```

Two small command tools wrap Docker Compose for you, so you never type a long command.

### Set it up from scratch

Do these steps once, in order:

1. **Download the project.**

   ```bash
   git clone <your-repo-url> planner
   cd planner
   ```

2. **Create your settings file** (a ready-made production template is included):

   ```bash
   cp laravel/.env.production laravel/.env
   ```

3. **Open `laravel/.env` and fill in these 4 things.** Leave everything else as it is.
   All 4 are required — if you skip one, the start command stops immediately with a clear
   message naming the missing one.

   - `APP_KEY` — the security key. Generate one, then paste the result into the file. The
     value **must** start with `base64:` followed by a 32-byte key (44 characters):

     ```bash
     printf 'base64:%s\n' "$(openssl rand -base64 32)"
     ```

   - `DB_PASSWORD`, `MYSQL_ROOT_PASSWORD`, `REDIS_PASSWORD` — just fill them in with any
     passwords you like.

4. **Start the app.** The first time is slower, because everything gets downloaded,
   built, and put together:

   ```bash
   ./planner up
   ```

5. **Check that it's running perfectly** (see below).

That's it. You're done.

### Make sure it's running perfectly

After starting (or updating), do this small check:

```bash
./planner ps
```

You should see **4 items** — `php-fpm`, `nginx`, `mysql`, `redis` — all of them `Up` /
`running` / `healthy`.

Then:

1. Open your browser and go to **<http://localhost>** — the app should load.
2. Create your account (there's a **Register / Sign up** link) and log in.
3. Bonus check: visit <http://localhost/up> — it should just show the number `200`.

If all of that works, the app is running perfectly.

### Use it every day

- Open your browser and go to **<http://localhost>**, log in, and use it.
- You can also use it from other devices on your network — instead of `localhost`, type
  the computer's IP, e.g. `http://192.168.1.20`.
- Your data is saved and stays there. Updating, restarting, or rebuilding the app
  **never** deletes it.

### Stop, start, and clean up

All commands run from the project folder. They **all** keep your data safe — stopping or
cleaning up never touches it.

| Command | What it does | When to use it |
|---|---|---|
| `./planner start` | Turns the app back on, right where it was | After you paused it with `stop` |
| `./planner stop` | Pauses the app and frees up your resources | When you want the app off for a while (it starts back up fast with `start`) |
| `./planner up` | Starts the app using the current (already built) version | A plain start — e.g. after `down` |
| `./planner build && ./planner up` | Builds the newest code, then starts it | After every update (see "Update the app") |
| `./planner down` | Stops the app completely and removes all temporary stuff | Rarely — when you want a totally clean slate. Bring it back with `up` |

How to think about them:

- **Start / Stop** are the everyday pair — pause the app to free resources, resume it when
  you need it again.
- **`up`** is the "official start" — it makes sure the app is running from its current
  version. **On its own it does not bring in new code**; that's what `build` is for, so
  after an update always run `build` first.
- **`down`** and **`up`** are the rare pair — a full shutdown, then a full fresh start.
  `down` does not delete your data.
- Never run `./planner down -v` — that extra `-v` **deletes your saved data**.

Nice to know: after your computer restarts, the app **starts by itself** — you don't have
to do anything.

Type `./planner` with no command to see the full list at any time.

### Update the app (when a new version arrives)

1. Get the newest code:

   ```bash
   git pull
   ```

2. Build and start the new version (your data stays untouched):

   ```bash
   ./planner build && ./planner up
   ```

3. Wait for it to finish, then repeat the check from
   ["Make sure it's running perfectly"](#make-sure-its-running-perfectly).

---

## 3. How to develop this app

Like everything else here, development runs in Docker — no PHP or Node installed on your
computer. You get one container with everything in it: PHP, Composer, and Node. All your
code lives in the normal project folder and every edit shows up right away.

### What you need

Same three things as production: **Docker with Docker Compose**, **Git**, and an
**internet connection** for the first run.

### Set it up from scratch

Do these steps once, in order:

1. **Download the project.**

   ```bash
   git clone <your-repo-url> planner
   cd planner
   ```

2. **Create your settings file** (the dev template is included):

   ```bash
   cp laravel/.env.development laravel/.env
   ```

3. **Open `laravel/.env` and fill in these 4 things.** Leave everything else as it is.

   - `APP_KEY` — the security key. It must start with `base64:` followed by a 32-byte key
     (44 characters):

     ```bash
     printf 'base64:%s\n' "$(openssl rand -base64 32)"
     ```

     `APP_KEY` must be in place **before you start for the first time** — the start
     command refuses to run without it.
   - `DB_PASSWORD`, `MYSQL_ROOT_PASSWORD`, `REDIS_PASSWORD` — fill them in with any
     passwords you like, *before* starting for the first time.

4. **Start the dev setup** (same on every system; first time is slow because it downloads
   and builds everything):

   ```bash
   ./planner-dev up
   ```

5. **Open a terminal inside the development container:**

   ```bash
   ./planner-dev shell
   ```

6. **Install your project's libraries once** (they live on `vendor/` / `node_modules/`
   in your project folder and persist between restarts):

   ```sh
   composer install
   npm install
   ```

Nothing runs automatically — the container stays up and waits for you, and you start the
servers yourself next.

### Start developing

> **One-command shortcut:** `composer run dev` starts the app server, the queue worker,
> and Vite all together. The steps below are the same thing, one by one (`--host=0.0.0.0`
> is required because Docker forwards ports to the container's network interface — a
> server bound to `127.0.0.1` inside the container is invisible from your browser; Vite
> already binds `0.0.0.0` via its config, which is why `npm run dev` needs no flag):

1. In the container terminal, start the app server:

   ```sh
   php artisan serve --host=0.0.0.0 --port=8000
   ```

2. In a second container terminal, start the front-end "live reload" tool (optional, but
   nice — styles and pages refresh by themselves):

   ```sh
   npm run dev
   ```

3. Open your browser and go to <http://localhost:8000> — the project home page loads
   immediately. Create your account (Register link) to use the real features.
4. Bonus check: visit <http://localhost:8000/up> — it should just show the number `200`.

### Work on it every day

- Every morning, just start and re-open your terminal:

  ```bash
  ./planner-dev up
  ./planner-dev shell
  ```

  Then start the servers from above (`php artisan serve ...` and `npm run dev`).
- **Edit code, refresh the browser** — changes appear instantly, no rebuilds, because the
  app folder is shared between you and the container.
- **After you change the database**, run `php artisan migrate` in the container.
- **Run the tests** any time: `composer test` (they're fast; they don't even need the
  database).

### Stop, start, and clean up

All commands run from the project folder. They all keep your data safe — stopping or
cleaning up never touches it.

| Command | What it does | When to use it |
|---|---|---|
| `./planner-dev start` | Turns dev back on, right where it was | After you paused it with `stop` |
| `./planner-dev stop` | Pauses dev and frees up your resources | When you take a long break |
| `./planner-dev up` | Starts dev using the current (already built) setup | A plain start — e.g. after `down` |
| `./planner-dev build && ./planner-dev up` | Rebuilds the tool container, then starts dev | Only after a setup change (rare) — never needed for normal code edits |
| `./planner-dev down` | Stops dev completely and removes temporary stuff | Rarely — when you want a totally clean slate. Bring it back with `up` |
| `./planner-dev shell` | Opens a terminal in the workspace container | Your daily entry point |

Same rule as production: **never** run `./planner-dev down -v` — the `-v` deletes your
database.

Type `./planner-dev` with no command to see the full list at any time.

### Stop, start, and clean up (production vs development)

Your computer keeps only **one settings file** on the `laravel/` folder at a time — one
copy holds the values for the environment you're currently running. If you switch between
them:

- Dev: `cp laravel/.env.development laravel/.env`, fill `APP_KEY`, `DB_PASSWORD`,
  `MYSQL_ROOT_PASSWORD`, `REDIS_PASSWORD`.
- Prod: `cp laravel/.env.production laravel/.env`, fill `APP_KEY`, `DB_PASSWORD`,
  `MYSQL_ROOT_PASSWORD`, `REDIS_PASSWORD` (all required — the start command stops until
  they're set).

Each setup keeps its own database — switching never mixes data.