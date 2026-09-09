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

### Set it up from scratch

Do these steps once, in order:

1. **Download the project.**

   ```bash
   git clone <your-repo-url> planner
   cd planner
   ```

2. **Create your personal settings file** (a ready-made template is included):

   - **Mac / Linux:**

     ```bash
     cp .env.production .env
     ```

   - **Windows** (PowerShell):

     ```powershell
     Copy-Item .env.production .env
     ```

3. **Open `.env` and fill in these 4 things.** Leave everything else as it is.

   - `APP_KEY` — the security key. Generate it, then paste the result into the file:

     - **Mac / Linux:**

       ```bash
       openssl rand -base64 32
       ```

     - **Windows** (PowerShell):

       ```powershell
       [System.Convert]::ToBase64String([System.Security.Cryptography.RandomNumberGenerator]::GetBytes(32))
       ```

   - `DB_PASSWORD`, `MYSQL_ROOT_PASSWORD`, `REDIS_PASSWORD` — just fill them in with any
     passwords you like.

4. **Build and start the app** (this works the same on every system). The first time is
   slower, because everything gets downloaded and put together:

   ```bash
   docker compose -f compose.prod.yaml up -d --build
   ```

5. **Check that it's running perfectly** (see below).

That's it. You're done.

### Make sure it's running perfectly

After starting (or updating), do this small check:

```bash
docker compose -f compose.prod.yaml ps
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

All commands run from the project folder (`planner`). They **all** keep your data safe —
stopping or cleaning up never touches it.

There are 4 commands you might ever need:

| Command | What it does | When to use it |
|---|---|---|
| `docker compose -f compose.prod.yaml start` | Turns the app back on, right where it was | After you paused it with `stop` |
| `docker compose -f compose.prod.yaml stop` | Pauses the app and frees up your computer's resources | When you want the app off for a while (it starts back up fast with `start`) |
| `docker compose -f compose.prod.yaml up -d` | Starts the app using the current (already built) version | A plain start — e.g. after `down`, or after a restart where the app didn't come back on its own |
| `docker compose -f compose.prod.yaml up -d --build` | Builds the newest code, then starts the app | After every update (the `--build` part is what makes the new code appear) |
| `docker compose -f compose.prod.yaml down` | Stops the app completely and removes all temporary stuff | Rarely — when you want a totally clean slate. Bring it back with `up -d` |

How to think about them:

- **Start / Stop** are the everyday pair — pause the app to free resources, resume it when
  you need it again.
- **`up -d`** is the "official start" — it makes sure the app is running from its current
  version. **On its own it does not bring in new code**; that's what `--build` is for, so
  after an update always use `up -d --build`.
- **`down`** and **`up -d`** are the rare pair — a full shutdown, then a full fresh start.
  `down` does not delete your data.
- Never run `docker compose down -v` — that extra `-v` **deletes your saved data**.

Nice to know: after your computer restarts, the app **starts by itself** — you don't have
to do anything.

### Update the app (when a new version arrives)

1. Get the newest code:

   ```bash
   git pull
   ```

2. Build and start the new version (your data stays untouched):

   ```bash
   docker compose -f compose.prod.yaml up -d --build
   ```

3. Wait for it to finish, then repeat the check from
   ["Make sure it's running perfectly"](#make-sure-its-running-perfectly).

---

## 3. How to develop this app

> Development setup is not ready yet — it will be documented here later.