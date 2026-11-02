# FirmHub — Progress

Status of every task, updated at each checkpoint. Plan and acceptance criteria: [ROADMAP.md](ROADMAP.md) and [0.10 §7](phase-0/0.10-architecture-consolidation-and-phase-1-plan.md).

## Current state

| | |
|---|---|
| Phase | 1 — Foundation |
| Last approved task | 1.11 Platform Admin Foundation (2026-11-02) |
| Current task | 1.13 Dashboard Shell & Branding |
| Next task | Deferred Phase 1 tasks (owner to choose the order) |
| Deferred | 1.4, 1.5, 1.9, 1.10, 1.12 (owner decision; required before Phase 2, see exit criteria E-2, E-4, E-5, E-8, E-9, E-11) |

## Phase 1 tasks

| Task | Name | Status | Commit |
|---|---|---|---|
| 1.1 | Baseline Validation | Approved | — (validation only) |
| 1.2 | Project Documentation & Identity | Approved | `0d0a041` (PR #3) |
| 1.3 | Frontend Toolchain Remediation | Approved | `80e35bc` (PR #4) |
| 1.4 | CI & Dependency Automation | Deferred | — |
| 1.5 | Test Foundation | Deferred | — |
| 1.6 | Authentication Hardening | Approved | `c031045` (PR #5) |
| 1.7 | Case-Insensitive Email Uniqueness | Approved | `14f86f9` (PR #8) |
| 1.8 | Platform Host, Trusted Hosts & Proxies | Approved | `78539ee` (PR #9) |
| 1.9 | Security Headers Baseline | Deferred | — |
| 1.10 | Production Configuration Guardrails | Deferred | — |
| 1.11 | Platform Admin Foundation | Approved | `270a392` (PR #10) |
| 1.12 | Localization Foundation | Deferred | — |
| 1.13 | Dashboard Shell & Branding | In review | — |

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
| F-6 | CI runs only on pushes to `main` and uses Node 22 | Trigger: CI runs on pull requests into `main` and pushes to `main`, not on `develop` (owner decision 2026-11-02, see DECISIONS). Node 22 → 24: task 1.4 (MD-2) |

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

## Task 1.8 — Platform Host, Trusted Hosts & Proxies (2026-10-31)

| Change | Detail |
|---|---|
| Configuration | `app.host` in `config/app.php`: the `APP_URL` host, used as the platform host and as the domain under which tenant sites live; `config/trustedproxy.php`: `proxies` from `TRUSTED_PROXIES`, read by the framework `TrustProxies` middleware |
| Trusted hosts | The framework `TrustHosts` middleware is enabled in `bootstrap/app.php` with two patterns: the application host and its single-label subdomains. Other hosts receive `400`. As in the framework default, the check is skipped in the local environment and in tests; there, platform routes still answer only on the platform host (404 elsewhere) |
| Platform routes | `routes/web.php` (incl. settings), Fortify (`fortify.domain`) and the Livewire update endpoint are bound to the platform host; on tenant hosts they return `404`. Livewire's update route is registered before Livewire boots, so its unconstrained default route is never added |
| Host-agnostic routes | `/up`, Flux and Livewire static assets, Livewire upload/preview (signed URLs, host included in the signature), `storage/{path}` (signed), `_boost` (development only) |
| Reset links | Forged `X-Forwarded-Host` from untrusted addresses is ignored; a host forwarded by a trusted proxy that is not the platform host reaches no platform route (404 locally, 400 elsewhere) |

Residual risks: generated platform URLs take the port from the request, so a trusted host with an unexpected port yields links with that port (no attacker-controlled host is possible). In local debug mode the `400` page shows exception details; production configuration is task 1.10.

## Task 1.11 — Platform Admin Foundation (2026-11-02)

| Change | Detail |
|---|---|
| Database | `users.is_platform_admin` (`boolean`, not null, default `false`); `down()` drops the column |
| Model | Cast to `boolean`, default `false` on new instances, not in `Fillable` (mass assignment is ignored) |
| Gate | `platform.admin` allows users with the flag; no `Gate::before`, so the flag grants no other ability |
| Commands | `platform-admin:grant` / `platform-admin:revoke {email} --reason= [--actor=]`: email matched in lowercase, `--reason` required, unknown user or actor fails, a call that changes nothing succeeds without writing or logging |
| Logging | `info` entry with `event`, `actor` (operating system user), `actor_user_id`, `user_id` and `reason`; no email addresses |
| Not included | 2FA requirement for platform admins (0.7 SEC-ADMIN-1), deferred by the owner |
| Applied to | Local development database `firmhub` (batch 3, rollback → migrate verified) |

Residual risk: platform admin accounts are protected by password only until SEC-ADMIN-1 is implemented; the flag has no effect yet because no admin route or action exists.

## Task 1.13 — Dashboard Shell & Branding (2026-11-02)

| Change | Detail |
|---|---|
| Brand | Placeholder "F" monogram (`components/app-logo-icon`) in the sidebar and auth layouts; the name comes from `APP_NAME`. Replaces the Laravel logo until a real FirmHub logo exists |
| Icons | `favicon.svg`, `favicon.ico` (16/32/48 px) and `apple-touch-icon.png` (180 px, square without transparency) generated from the monogram |
| Navigation | Starter-kit links (repository, documentation) removed; the sidebar has only Dashboard and is ready for the company sections of 0.5 §10, which are added once their pages exist |
| Pages | Home page (`/`) replaced by a FirmHub page with log-in / register (guests) or dashboard (signed in) links; dashboard placeholder boxes replaced by a welcome text |
| Removed | Unused starter-kit layouts `app/header`, `auth/card`, `auth/split`, the placeholder pattern component and unused Lucide icons |
| Indexing | `<meta name="robots" content="noindex, nofollow">` in `partials/head`, used by the home, auth, dashboard and settings pages; `public/robots.txt` unchanged (see DECISIONS) |
| Checked in the browser | Home, login and register pages on desktop (dark) and mobile (light); keyboard focus moves through the home page links with a visible focus ring. The dashboard was not opened in the browser because the development database has no user |

Icons are regenerated from `public/favicon.svg` with ImageMagick (not a project dependency):

```bash
magick -background none -density 576 public/favicon.svg -define icon:auto-resize=48,32,16 public/favicon.ico
sed 's/ rx="7"//' public/favicon.svg > /tmp/apple-touch-icon.svg
magick -density 864 /tmp/apple-touch-icon.svg -resize 180x180 -background '#18181b' -alpha remove -alpha off -strip -depth 8 public/apple-touch-icon.png
```
