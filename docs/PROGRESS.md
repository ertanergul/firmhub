# FirmHub — Progress

Status of every task, updated at each checkpoint. Plan and acceptance criteria: [ROADMAP.md](ROADMAP.md) and [0.10 §7](phase-0/0.10-architecture-consolidation-and-phase-1-plan.md).

## Current state

| | |
|---|---|
| Phase | 1 — Foundation |
| Last approved task | 1.2 Project Documentation & Identity (2026-10-30) |
| Current task | 1.3 Frontend Toolchain Remediation |
| Next task | 1.4 CI & Dependency Automation |

## Phase 1 tasks

| Task | Name | Status | Commit |
|---|---|---|---|
| 1.1 | Baseline Validation | Approved | — (validation only) |
| 1.2 | Project Documentation & Identity | Approved | `0d0a041` (PR #3) |
| 1.3 | Frontend Toolchain Remediation | In review | — |
| 1.4 | CI & Dependency Automation | Not started | — |
| 1.5 | Test Foundation | Not started | — |
| 1.6 | Authentication Hardening | Not started | — |
| 1.7 | Case-Insensitive Email Uniqueness | Not started | — |
| 1.8 | Platform Host, Trusted Hosts & Proxies | Not started | — |
| 1.9 | Security Headers Baseline | Not started | — |
| 1.10 | Production Configuration Guardrails | Not started | — |
| 1.11 | Platform Admin Foundation | Not started | — |
| 1.12 | Localization Foundation | Not started | — |
| 1.13 | Dashboard Shell & Branding | Not started | — |

## Task 1.1 — Baseline Validation (2026-10-30)

### Environment

| Component | Version |
|---|---|
| Laravel / PHP | 13.38.0 / 8.3.35 (host and Sail) |
| Livewire / Flux / Fortify | 4.4.7 / 2.20.1 / 1.41.0 |
| Pest / Larastan / Pint | 4.7.8 / 3.13.0 / 1.32.1 |
| Composer | 2.2.30 (host, no `audit`) / 2.10.3 (Sail) |
| Node / npm | 24.22.0 / 11.19.1 (host); 24.22.1 / 12.2.0 (Sail) |
| vite / vite-plus / Tailwind | 8.3.4 / 0.3.0 / 4.3.3 |
| PostgreSQL | 18.6 (`firmhub` and `testing` databases) |

All versions match [0.8 §1](phase-0/0.8-technology-and-infrastructure-decisions.md).

### Results

| Check | Result |
|---|---|
| Sail containers | Healthy |
| `migrate:fresh` (development database) | 4 migrations applied |
| `sail artisan test` as configured | 1 passed, 33 errors — `DB_HOST=127.0.0.1` from `phpunit.xml` is unreachable inside the container |
| Pest with `DB_HOST=pgsql` | 34 passed (81 assertions) on PostgreSQL 18.6 |
| Pint (`--test`) | Passed |
| Larastan level 7 | 0 errors |
| `npm run build` (host) | Passed; fonts self-hosted in `public/build/assets` |
| `composer audit` | 2 medium advisories (below) |
| `npm audit` | 7 advisories: 3 critical, 4 moderate (below) |
| `http://localhost`, `http://acme.localhost` | 200 (curl); Safari not verified |

### Findings handed to later tasks

| # | Finding | Planned handling |
|---|---|---|
| F-1 | `phpunit.xml` `DB_HOST=127.0.0.1` breaks `sail artisan test` inside the container (works in CI and from the host) | Fix in a later Phase 1 task (1.4 or 1.5) with approval; workaround documented in the [README](../README.md#known-issue-tests-inside-sail) |
| F-2 | `livewire/livewire` 4.4.7 — `#[Authorize]` bypass, GHSA-mhf7-xp6r-6qrc (medium, fixed in 4.4.8). The project does not use `#[Authorize]` or `#[Json]` | Patch update with approval (task 1.4 or a separate task before Phase 2) |
| F-3 | `symfony/mime` 7.4.19 — CVE-2026-45070 email header injection (medium, fixed in 7.4.22). No custom MIME parameter handling in `app/` | Patch update with approval, together with F-2 |
| F-4 | `npm audit`: `tinypool` (critical, prototype pollution to RCE), `oxfmt`, `@vitest/*` — all through `vite-plus@0.3.0`; build-time tools listed under `dependencies` | **Closed in task 1.3** — `vite-plus` 1.4.0, `npm audit` reports 0 vulnerabilities |
| F-5 | Local `.env` uses `QUEUE_CONNECTION=database`; `.env.example` and TDR-11 specify `sync` | Local file, not tracked; owner to align |
| F-6 | CI runs only on pushes to `main` and uses Node 22 | Task 1.4 (already planned, MD-2/MD-3) |

## Task 1.3 — Frontend Toolchain Remediation (2026-10-30)

| Change | Detail |
|---|---|
| `vite-plus` | 0.3.0 → 1.4.0 (major), installed by removing 0.3.0 first; no `--force` or `--legacy-peer-deps` |
| `vite` | Direct dependency aliased to `npm:@voidzero-dev/vite-plus-core@1.4.0` plus an `overrides` entry, as required by `vite-plus` 1.x; plain Vite 8.3.4 is no longer installed |
| Node | `.nvmrc` (`24`); no `engines` field (owner decision, see [DECISIONS.md](DECISIONS.md)) |
| Unchanged | `vp build` / `vp dev` scripts, `vite.config.js`, `vp migrate` not run (it reformats sources and installs hooks) |

