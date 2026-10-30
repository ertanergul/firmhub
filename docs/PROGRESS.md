# FirmHub — Progress

Status of every task, updated at each checkpoint. Plan and acceptance criteria: [ROADMAP.md](ROADMAP.md) and [0.10 §7](phase-0/0.10-architecture-consolidation-and-phase-1-plan.md).

## Current state

| | |
|---|---|
| Phase | 1 — Foundation |
| Last approved task | 1.6 Authentication Hardening (2026-10-30) |
| Current task | 1.7 Case-Insensitive Email Uniqueness |
| Next task | 1.8 Platform Host, Trusted Hosts & Proxies |
| Deferred | 1.4, 1.5 (owner decision; required before Phase 2, see exit criteria E-2, E-4, E-5) |

## Phase 1 tasks

| Task | Name | Status | Commit |
|---|---|---|---|
| 1.1 | Baseline Validation | Approved | — (validation only) |
| 1.2 | Project Documentation & Identity | Approved | `0d0a041` (PR #3) |
| 1.3 | Frontend Toolchain Remediation | Approved | `80e35bc` (PR #4) |
| 1.4 | CI & Dependency Automation | Deferred | — |
| 1.5 | Test Foundation | Deferred | — |
| 1.6 | Authentication Hardening | Approved | `c031045` (PR #5) |
| 1.7 | Case-Insensitive Email Uniqueness | In review | — |
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
| F-1 | `phpunit.xml` `DB_HOST=127.0.0.1` breaks `sail artisan test` inside the container (works in CI and from the host) | **Closed** (owner request, 2026-10-30): `DB_HOST` override removed from `phpunit.xml`; Sail uses `.env`, CI sets `DB_HOST` itself. Host-PHP test runs are unsupported because of the shared compiled-view cache ([README](../README.md#running-tests)) |
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

## Task 1.6 — Authentication Hardening (2026-10-30)

| Requirement | Implementation |
|---|---|
| SEC-AUTH-1 throttles | `ThrottleAuthenticationRequests` (web group) applies named limiters to Fortify routes Fortify does not throttle: `register` 10/hour per IP; `password-reset-link` 5/min per IP and 3 per 10 min per normalized email. Exceeding a limit returns `429` |
| SEC-AUTH-2 uniform response | `PasswordResetLinkRequestResponse` is bound to both Fortify reset-link response contracts and returns the same message for known and unknown emails (HTML and JSON) |
| SEC-AUTH-3 password policy | **Changed by owner decision:** production requires at least 8 characters with mixed case, letters, numbers and symbols; the Have I Been Pwned `uncompromised()` check is removed, so no outbound request is made. Other environments keep Laravel's default (8 characters) |
| SEC-AUTH-8 auth logging | `LogAuthenticationEvent` logs login, logout, failed login, lockout and 2FA events with event name, guard and user ID only — no email, IP, credentials, codes or tokens |

Residual risks: without the breach check, passwords that appear in public breach lists are accepted if they meet the length and composition rules (0.7 AU-1 credential stuffing). A reset link for an existing account is sent synchronously (`queue: sync`), so response timing can still differ between known and unknown emails. Queued notifications are evaluated in Phase 7.

## Task 1.7 — Case-Insensitive Email Uniqueness (2026-10-30)

| Change | Detail |
|---|---|
| Normalization | `CreateNewUser` and the profile settings page lowercase the email before validation; Fortify already lowercases it on the registration request (`lowercase_usernames`) |
| Database | Migration lowercases stored emails and adds `users_email_lower_unique` on `lower(email)`; `down()` drops the index (original letter case is not restored). The existing `users_email_unique` index is kept for exact lookups |
| Fixed defect | Before this task, changing the email on the profile page to a mixed-case value was saved as entered, after which login (lowercased by Fortify) could no longer find the account |
| Data check | 0 users and 0 case-variant duplicates in `firmhub` and `testing` before the migration; with duplicates present the migration fails with `23505` and changes nothing (verified on `testing`) |
| Applied to | `testing` (rollback → migrate verified twice) and the local development database `firmhub` (batch 2) |

Residual risk: two simultaneous requests with the same email can both pass validation; the index rejects the second with a server error instead of a validation message.

