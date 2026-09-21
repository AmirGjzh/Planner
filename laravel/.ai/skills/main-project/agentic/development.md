# Agentic Development Environment

How this project's AI-agent environment is set up: Laravel Boost, the OpenCode MCP bridge, the skills ecosystem, and project rules. `AGENTS.md` and `boost.json` reference the pieces described here.

## Where everything runs

- **opencode (the agent) runs on the host**, launched from the `laravel/` directory so it discovers the project config, skills, and MCP server.
- **PHP, Composer, and the app run inside the dev `workspace` container.** The host has no `php`, and MySQL/Redis live on the Docker network, so every Artisan command is executed inside the container:

```bash
./planner-dev up      # start workspace + mysql + redis (required for the MCP bridge)
./planner-dev shell   # open a shell in the container (cwd = /var/www = laravel/)
```

## Laravel Boost

[Boost](https://github.com/laravel/boost) is Laravel's agent toolkit: AI guidelines, agent skills, and an MCP server with app absumption tools plus a >17,000-document Laravel search API.

### Install (one-time)

```bash
composer require laravel/boost --dev
php artisan boost:install
```

In the interactive installer select:

- Agent: **OpenCode**
- Features: **guidelines**, **skills**, and **MCP**

`boost:install` (and later `boost:update`) generate the OpenCode integration according to Boost's OpenCode agent contract:

| Piece | File | Where |
| --- | --- | --- |
| Configuration | `boost.json` | `laravel/` (gitignored) |
| AI guidelines | `AGENTS.md` | `laravel/` (gitignored) |
| Boost skills | `.agents/skills/<skill>/` | `laravel/` (gitignored) |
| Custom skill mirror | `.agents/skills/main-project` → `.ai/skills/main-project` | symlink |
| MCP server config | `opencode.json` (see below) | `laravel/` (gitignored) |

Everything generated is per-developer and gitignored. The committed, authoritative copy is `.ai/`: custom guidelines in `.ai/guidelines/`, project rules in `.ai/rules/`, and the custom skill in `.ai/skills/`.

### MCP server (`opencode.json`)

Boost registers the `laravel-boost` MCP server in `opencode.json` under the `mcp` key. For this host (no PHP) the command is patched to a Docker bridge so the server runs inside the workspace container:

```json
{
    "$schema": "https://opencode.ai/config.json",
    "mcp": {
        "laravel-boost": {
            "type": "local",
            "enabled": true,
            "command": [
                "docker",
                "compose",
                "-f",
                "compose.development.yml",
                "exec",
                "-T",
                "workspace",
                "php",
                "artisan",
                "boost:mcp"
            ]
        }
    }
}
```

Notes:

- `-T` disables the TTY so stdio stays clean for the MCP protocol; `opencode` is started from `laravel/` so the compose file resolves.
- The workspace container must be up (`./planner-dev up`); if it is down, the MCP tools error out while the rest of the app works.
- `boost:update`/`boost:install` rewrites this file back to plain `["php", "artisan", "boost:mcp"]`, so **re-apply the Docker bridge after every Boost resource update**.

Tools exposed (`laravel-boost`): Application Info, Database Connections, Database Query, Database Schema, Get Absolute URL, Last Error, Read Log Entries, Record Rule, and Search Docs (the version-aware Laravel documentation query).

### Keeping resources up to date

```bash
./planner-dev shell
php artisan boost:update        # refresh guidelines/skills for installed versions
php artisan boost:update --discover   # also publish for newly installed packages
```

## Skills

Skills are on-demand knowledge modules the agent loads when relevant.

### Boost skills

Installed automatically based on the packages in `composer.json` plus Boost's always-on skills:

- `infer-conventions` — sweeps the code and bootstraps `.ai/rules`
- `laravel-best-practices`, `livewire-development`, `tailwindcss-development`, `testing-best-practices` (Pest)
- `main-project` — the project skill, mirrored from `.ai/skills/main-project`

Custom skills live in `.ai/skills/<name>/SKILL.md` and are copied into `.agents/skills/` by `boost:update`. To override a Boost skill, create `.ai/skills/<name>/SKILL.md` with the matching name.

### skills.sh (personal/third-party skills)

Personally selected skills are installed from the [skills.sh](https://skills.sh/) registry:

```bash
npx skills add <owner/repo> --skill <name>        # project-scoped
npx skills add <owner/repo> --skill <name> -g     # global (any project)
```

Currently installed project skills: `accessibility`, `emil-design-eng`, `find-skills`, `frontend-design`, `web-design-guidelines`, `ui-ux-pro-max`, `web-quality-audit`.

How skills are discovered by opencode:

| Scope | Path | Committed? |
| --- | --- | --- |
| Project | `.agents/skills/<name>/SKILL.md` | no (`.agents` gitignored) |
| Project (alt) | `.opencode/skills/`, `.claude/skills/` | no |
| User/global | `~/.agents/skills/`, `~/.config/opencode/skills/` | n/a |
| Project (shared) | `.ai/skills/<name>/` via Boost copy | yes |

Rules of thumb:

- **Restart opencode** after adding skills — they are only discovered at session start.
- **Project skills load only when opencode starts from `laravel/`**; starting from the repo root (`Planner/`) skips them (and the MCP server) entirely.
- Keep project-specific/team skills in `.ai/skills/`; keep personal skills in project `.agents/skills/` or global scope.

Some third-party skills ship scripts with hardcoded install paths (e.g. `ui-ux-pro-max` referenced `${CLAUDE_PLUGIN_ROOT}`); patch the skill's `SKILL.md` to the local path after install — `boost:update`/`skills update` may overwrite such edits.

## Project rules

Rules are durable, committed project conventions that agents must follow. See `AGENTS.md` (session ritual) and `.ai/rules/index.md`.

- Record new rules through the `record-rule` MCP tool (it creates/updates the rule file **and** the index).
- Bootstrap years of conventions in one pass with the `infer-conventions` skill.
- Rules are enabled by default; disable by setting `BOOST_RULES_ENABLED=false` (removes the tool).

## Verification checklist

After setup, confirm in a fresh session:

1. `opencode` is started in `laravel/` with the workspace container up.
2. The `laravel-boost` MCP server loads (tools listed above appear as available).
3. `main-project` + boost skills + personal skills are discoverable.
4. `AGENTS.md` instructions load (mandatory ritual: activate `main-project`, read `.ai/rules/index.md`).
5. Probe `search-docs` once to confirm version-aware Laravel docs queries work.