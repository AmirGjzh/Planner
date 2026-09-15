# Project Setup

This project uses two GitHub repositories:

* **Public Repository** → `origin`
* **Private Repository** → `private`

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

```bash
git fetch private
git switch -c <PRIVATE_BRANCH> --track private/<PRIVATE_BRANCH>
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

Work on `private-branch`:

```bash
git switch <PRIVATE_BRANCH>
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

git switch <PRIVATE_BRANCH>
git merge main
git push private
```

### Important

* `origin` is the **Public Repository**.
* `private` is the **Private Repository**.
* Public changes should be made on `main`.
* Private-only changes should be made on `private-branch`.
* Be careful not to run `git push origin` while working on private-only changes.
