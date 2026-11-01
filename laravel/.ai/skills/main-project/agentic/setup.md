# Agentic & AI Developer Environment

How this project's optional AI-assisted development environment is set up: [Laravel Boost](https://github.com/laravel/boost), Model Context Protocol (MCP) integration, Boost skills, and project rules. *(AI tooling is entirely optional; the application and its test suite require only Docker and Composer).*

**See also:** [project rules](../../../rules/index.md) · [coding standards](../standards/coding.md) · [dev stack](../docker/development.md) · [docs map](../SKILL.md)

## Contents

- [Where everything runs](#where-everything-runs)
- [Laravel Boost & MCP Server](#laravel-boost--mcp-server)
- [Skills & Guidelines](#skills--guidelines)
- [Project rules](#project-rules)
- [Verification checklist](#verification-checklist)

## Where everything runs

- **PHP, Composer, and the app run inside the dev `workspace` container.** The host has no local `php`, and MariaDB/Redis live on the Docker network, so every Artisan command is executed inside the container:

```bash
./planner-dev up      # start workspace + mariadb + redis
./planner-dev shell   # open a shell in the container (cwd = /var/www = laravel/)
```

## Laravel Boost & MCP Server

[Boost](https://github.com/laravel/boost) is Laravel's developer toolkit: AI guidelines, agent skills, and an MCP server with app introspection tools plus a >17,000-document Laravel documentation search API.

### Install (one-time)

```bash
composer require laravel/boost --dev
php artisan boost:install
```

During setup, select the features you need (guidelines, skills, and MCP). `boost:install` and `boost:update` generate local integration files according to your chosen MCP client (e.g. OpenCode, Claude Desktop, Cursor, or any MCP-compatible environment):

| Piece | File | Where |
| --- | --- | --- |
| Configuration | `boost.json` | `laravel/` (gitignored) |
| AI guidelines | `AGENTS.md` | `laravel/` (gitignored) |
| Boost skills | `.agents/skills/<skill>/` | `laravel/` (gitignored) |
| Project skill mirror | `.agents/skills/main-project` → `.ai/skills/main-project` | symlink |
| MCP server config | Client-specific config (e.g. `opencode.json`) | `laravel/` (gitignored) |

Generated configuration is per-developer and gitignored. The committed, authoritative source of truth is under `.ai/`: custom guidelines in [`../../../guidelines/main-project.md`](../../../guidelines/main-project.md), project rules in [`../../../rules/index.md`](../../../rules/index.md), and the project skill in [`../SKILL.md`](../SKILL.md).

### Running the MCP Server in Docker

Because PHP runs inside the dev container rather than on the host, any MCP client configuration invoking `laravel-boost` should be routed through a Docker bridge so the server executes inside the workspace container:

```json
{
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
- `-T` disables the TTY so stdio stays clean for the MCP protocol.
- The workspace container must be running (`./planner-dev up`).
- `boost:update` or `boost:install` may overwrite client config back to plain `["php", "artisan", "boost:mcp"]` — re-apply the Docker wrapper if your client runs on the host.

Tools exposed by `laravel-boost`: Application Info, Database Connections, Database Query, Database Schema, Get Absolute URL, Last Error, Read Log Entries, Record Rule, and Search Docs.

### Keeping resources up to date

```bash
./planner-dev shell
php artisan boost:update        # refresh guidelines/skills for installed package versions
php artisan boost:update --discover   # discover tooling for newly added packages
```

## Skills & Guidelines

Skills are on-demand knowledge modules. This project includes Boost's standard development skills plus the Planner-specific project skill:

- `laravel-best-practices`, `livewire-development`, `tailwindcss-development`, `testing-best-practices` (Pest)
- `main-project` — the project skill mirrored from [`../SKILL.md`](../SKILL.md)

Rules of thumb:
- **Restart your AI assistant/MCP client** after adding or updating skills so they load into context.
- `.ai/skills/` is the committed source; `.agents/skills/` is its gitignored copy — edit `.ai/` only (see the [docs rule](../../../rules/main-project.md)).

## Project rules

Rules are durable, committed project conventions. See [`AGENTS.md`](../../../../AGENTS.md) (session ritual, if generated) and [`.ai/rules/index.md`](../../../rules/index.md) for the full rule map; cross-cutting quality patterns live in the [coding standards](../standards/coding.md).

- Record new rules through the `record-rule` MCP tool when available.
- Rules are enabled by default; disable by setting `BOOST_RULES_ENABLED=false`.

## Verification checklist

If you use an MCP-enabled assistant, confirm in a fresh session:

1. The workspace container is running (`./planner-dev up`).
2. The `laravel-boost` MCP server loads successfully.
3. The `main-project` skill and Boost skills are discoverable.
4. `search-docs` responds with version-aware Laravel documentation queries.
