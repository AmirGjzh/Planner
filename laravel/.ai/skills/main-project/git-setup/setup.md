# Project Setup

This project uses two GitHub repositories:

* **Public Repository** → `origin` → `github.com/AmirGjzh/Planner`
* **Private Repository** → `private` → `github.com/AmirGjzh/Planner-Pink-World` (branch `main` → `pink-world`)

Both repositories are used in the same local project.

## Setup on a New System

### 1. Clone the Public Repository

```bash
git clone <PUBLIC_REPOSITORY_URL>
cd <PROJECT_FOLDER>
```

### 2. Add the Private Repository

```bash
git remote add private <PRIVATE_REPOSITORY_URL>
```

Check the remotes:

```bash
git remote -v
```

You should see:

```text
origin   → Public Repository
private  → Private Repository
```

### 3. Get the Private Branch

`private/main` tracks exactly one branch: `pink-world`.

```bash
git fetch private
git switch -c pink-world --track private/pink-world
```

The project is now ready.

## Daily Workflow

### Public Changes

Work on `main`:

```bash
git switch main
```

After making changes:

```bash
git add .
git commit -m "Your commit message"
git push origin
```

### Private Changes

Work on `pink-world`:

```bash
git switch pink-world
```

After making changes:

```bash
git add .
git commit -m "Your commit message"
git push private
```

## Sync Private with Public

When `main` has new changes and the private branch needs them:

```bash
git switch main
git pull origin

git switch pink-world
git merge main
git push private
```

### Important

* `origin` is the **Public Repository**.
* `private` is the **Private Repository**.
* Public changes should be made on `main`.
* Private-only changes should be made on `pink-world`.
* Be careful not to run `git push origin` while working on private-only changes.
