# FirmHub — Decision Register

The living register of architecture and product decisions. It starts from the register approved in Phase 0 ([0.10 §2](phase-0/0.10-architecture-consolidation-and-phase-1-plan.md)); later changes are added to the [change log](#change-log) instead of editing the Phase 0 documents. To reopen an `Accepted` decision, reference its ID.

## Status of Phase 0

Phase 0 (Architecture & Planning, documents 0.1–0.10) was **completed and approved by the product owner** before Phase 1 started (2026-10-30). The status lines inside the Phase 0 documents still read "Draft" because they predate that approval; this register is authoritative for current status.

## Statuses

| Status | Meaning |
|---|---|
| **Accepted** | Recommendation adopted; implement as written |
| **Accepted → amended** | Adopted with the change described in [0.10 §4](phase-0/0.10-architecture-consolidation-and-phase-1-plan.md) |
| **Owner input** | Product owner must decide or provide data |
| **Legal** | Engineering default adopted; legal review required before production |
| **Approval at use** | New dependency or container; approve when the implementing task starts |
| **Deferred** | Decided in the named later phase |
| **In task** | Resolved inside the named Phase 1 task, reported for approval |

## Architectural baseline

| ID | Decision |
|---|---|
| AD-1 | One website per company in the MVP; architecture ready for many |
| AD-2 | `CompanySiteSection` belongs to `CompanySite` |
| AD-3 | Subdomain first; custom domains via Cloudflare for SaaS (and a self-hosted provider) in Phase 5 |
| AD-4 | Cloudflare fields preserved as a future `company_site_domains` model (0.3 §6) |
| AD-5 | General-purpose platform; industry selected during onboarding; industry-specific theme recommendations later |
| AD-6 | Laravel + Livewire + PostgreSQL + modular monolith |

## Decisions from Phase 0 open questions

### 0.1 Product Scope

| ID | Decision | Status |
|---|---|---|
| Q1 | No billing / plan limits in MVP; design must not prevent them | Accepted |
| Q2 | Any verified user creates companies; configurable limit (default 5, 0.2 O-8) | Accepted |
| Q3 | No pre-publish moderation | Accepted |
| Q4 | Single offerings list with configurable heading | Accepted |
| Q5 | Single-page site + privacy page | Accepted |
| Q6 | Invitations in MVP; Viewer is read-only | Accepted |
| Q7 | Dashboard EN + TR; one content language per site | Accepted (dashboard locale selection: Section 6, OD-6) |
| Q8 | One base theme + 3–4 presets | Accepted |
| Q9 | Subdomain change allowed with cooldown and quarantine (0.4 §5) | Accepted |
| Q10 | Email notification for new messages (Should); DB is the source of truth | Accepted |
| Q11 | Contact message retention 24 months + purge | Legal |
| Q12 | Platform admin flag + Artisan; admin UI in Phase 6 | Accepted |
| Q13 | Company soft delete, 30-day grace, purge, subdomain quarantine | Accepted |
| Q14 | Legal fields optional, shown in footer when filled | Legal (display obligation unverified) |
| Q15 | Scale assumption: a few thousand tenants | Accepted; hosting region: Owner input |
| Q16 | Drop `Company.website` | Accepted |
| Q17 | External "open in maps" link, no embed | Accepted |
| Q18 | Self-hosted + hosted SaaS; license | Positioning Accepted; **license: Owner input** |

### 0.2 Multi-Tenancy & Authorization

| ID | Decision | Status |
|---|---|---|
| O-1 | Editors do not see contact messages | Accepted |
| O-2 | Multiple Owners allowed | Accepted |
| O-3 | Invitation acceptance requires matching verified email | Accepted |
| O-4 | Platform admins must have 2FA | Accepted (0.7 SEC-ADMIN-1) |
| O-5 | Owners and Admins can change the subdomain | Accepted |
| O-6 | UUIDv7 route keys / PKs | Accepted (0.3 §3) |
| O-7 | Separate registrable tenant domain in production | Accepted (0.7 SEC-HOST-3); domain names: Owner input (P-3) |
| O-8 | Company creation limit default 5 | Accepted |

### 0.3 Data Architecture

| ID | Decision | Status |
|---|---|---|
| D-1 | Offerings company-scoped | Accepted |
| D-2 | `company_locations` with one primary in MVP | Accepted |
| D-3 | UUIDv7 PKs | Accepted |
| D-4 | 30-day deletion grace period | Accepted |
| D-5 | No visitor IP stored | Accepted (0.7 PRIV-1) |
| D-6 | Lowercase emails + functional unique index | Accepted → Phase 1 task 1.7 |
| D-7 | `timestamptz` for new tables only | Accepted |
| D-8 | Industry labels in lang files | Accepted |
| D-9 | Media references in section settings | **Accepted → amended**: IDs in JSON + validation + usage finder (0.5 §4.5), not a pivot |
| D-10 | No `kind` column on offerings | Accepted |

### 0.4 Domains & Publishing

| ID | Decision | Status |
|---|---|---|
| P-1 | Live content publishing model | Accepted (risk R-6 accepted, 0.7 SQ-5) |
| P-2 | Separate tenant domain (Option B), configurable | Accepted |
| P-3 | Production platform and tenant domain names | Owner input (needed by Phase 7 / staging) |
| P-4 | 30-day cooldown, 90-day quarantine | Accepted |
| P-5 | Suspended sites return 404 | Accepted |
| P-6 | `*.localhost` default, dnsmasq fallback | Accepted (Safari check in 0.9 §10) |
| P-7 | Shareable preview links | Deferred (future) |

### 0.5 CMS & Website Builder

| ID | Decision | Status |
|---|---|---|
| C-1 | Hero pinned first | Accepted |
| C-2 | Plain text with paragraphs | Accepted |
| C-3 | No SVG uploads | Accepted (0.7 SEC-MEDIA-4) |
| C-4 | `intervention/image` | Approval at use (Phase 3, 0.8 ND-1) |
| C-5 | Explicit save, no autosave | Accepted |
| C-6 | Company info and social links as layout regions | Accepted |
| C-7 | 200 files / 250 MB, 50 offerings, configurable | Accepted |
| C-8 | Generic localized starter content | Accepted |
| C-9 | Theme recommendations in manifests | Accepted |

### 0.6 Public Website, SEO & Performance

| ID | Decision | Status |
|---|---|---|
| S-1 | "Powered by FirmHub" link: on for hosted SaaS, configurable | Accepted |
| S-2 | Privacy page `noindex, follow` | Accepted |
| S-3 | Industry → schema.org mapping | Accepted |
| S-4 | Public HTML `max-age=60` + validators | Accepted |
| S-5 | No visitor analytics in MVP | Accepted |
| S-6 | Tenant-themed 404 for unknown paths | Accepted |
| S-7 | Stateless contact form | Accepted (0.7 SEC-SESS-4) |
| S-8 | Browser / a11y test tooling | Approval at use (Phase 3, 0.8 ND-3) |

### 0.7 Security

| ID | Decision | Status |
|---|---|---|
| SQ-1 | Separate tenant domain | Accepted |
| SQ-2 | Media origin isolation in production | Accepted |
| SQ-3 | Contact limits 5/10 min per IP+site, 100/day per site, 20/h per IP | Accepted |
| SQ-4 | Notice + link; checkbox only if legally required | Legal |
| SQ-5 | Editors' edits go live immediately | Accepted |
| SQ-6 | No message body in notification emails | Accepted |
| SQ-7 | Encourage (not enforce) Owner 2FA | Accepted |
| SQ-8 | 14-day log retention | Accepted |
| SQ-9 | Hosting region / mail provider location | Owner input |
| SQ-10 | `SECURITY.md` + `security.txt` | Accepted → `SECURITY.md` in task 1.2; `security.txt` with the hosted launch |

### 0.8 Technology & Infrastructure

| ID | Decision | Status |
|---|---|---|
| 0.8 Q-1 | `intervention/image` | Approval at use (Phase 3) |
| 0.8 Q-2 | `vite-plus` remediation: evaluate replacement by plain `vite` first | Resolved in task 1.3: upgrade to `vite-plus` 1.x (see change log) |
| 0.8 Q-3 | PHP 8.3 for MVP | Accepted |
| 0.8 Q-4 | Caddy + PHP-FPM vs FrankenPHP | Deferred (Phase 7) |
| 0.8 Q-5 | Mailpit in Sail | Approval at use (Phase 2, invitations) |
| 0.8 Q-6 | `database` session and cache | Accepted → amends 0.1 NFR-OPS-01 (Section 4) |
| 0.8 Q-7 | License | Owner input (same as 0.1 Q18) |
| 0.8 Q-8 | `laravel/chisel` removal | In task 1.4 (verify; removal needs approval) |
| ND-1 … ND-3 | New dependencies | Approval at use |
| MD-1 … MD-9 | Maintenance of existing dependencies | Scheduled in Phase 1 (Section 5), MD-9 in Phase 3 |

### 0.9 Testing & Quality

| ID | Decision | Status |
|---|---|---|
| 0.9 Q-1 | Concurrency tests in the same CI job | Accepted |
| 0.9 Q-2 | `LazilyRefreshDatabase` | Accepted → task 1.5 |
| 0.9 Q-3 | Working `down()` migrations + rollback check in CI until first production release | Accepted → task 1.4 |
| 0.9 Q-4 | Coverage threshold | Deferred (after Phase 3) |
| 0.9 Q-5 | Browser testing plugin | Approval at use (Phase 3) |
| 0.9 Q-6 | Branch protection on `develop` and `main` | Owner input (repository setting) |

## Resolved contradictions

Conflicts between earlier Phase 0 documents (X-1 … X-11, for example `database` instead of `file` cache/session and Node 24 everywhere) are resolved in [0.10 §3](phase-0/0.10-architecture-consolidation-and-phase-1-plan.md); amendments to task documents are listed in [0.10 §4](phase-0/0.10-architecture-consolidation-and-phase-1-plan.md).

## Open owner decisions

| # | Decision | Needed by | Status |
|---|---|---|---|
| OD-1 | Project license (MIT proposed) — 0.1 Q18 / 0.8 Q-7 | Before a `LICENSE` file and public release | **Open** — deferred by the owner in task 1.2; no `LICENSE` file added, `composer.json` `license` field still inherited from the starter kit |
| OD-2 | Branch protection with required CI checks on `develop` / `main` — 0.9 Q-6 | After task 1.4 | Open |
| OD-3 | Production platform and tenant domain names — 0.4 P-3 | Public staging (before Phase 5) | Open |
| OD-4 | Hosting region and mail provider location — 0.1 Q15 / 0.7 SQ-9 | Phase 7 | Open |
| OD-5 | Legal review: retention (Q11), legal footer fields (Q14), consent checkbox (SQ-4), controller/processor roles and DPA (0.7 §7.1) | Before production | Open |
| OD-6 | Dashboard language selection (0.1 Q7). Recommendation: per-user preference on `users` (default `APP_LOCALE`), editable in settings; no URL locale prefix | Task 1.12 | Open |

## Change log

| Date | Task | Decision |
|---|---|---|
| 2026-10-30 | — | Phase 0 documents 0.1–0.10 approved by the product owner |
| 2026-10-30 | 1.1 | Baseline recorded ([PROGRESS.md](PROGRESS.md)); findings handed to later tasks, no decision changed |
| 2026-10-30 | 1.2 | Composer package name `ertanergul/firmhub`; security reports through GitHub private vulnerability reporting ([SECURITY.md](../SECURITY.md)); OD-1 left open; Phase 2–7 names in [ROADMAP.md](ROADMAP.md) derived from 0.1 §4–5 |
| 2026-10-30 | 1.3 | 0.8 Q-2 / MD-1: the owner chose upgrading `vite-plus` to 1.4.0 over the recommended replacement by plain `vite`. Consequence: `vite` is aliased to `@voidzero-dev/vite-plus-core` (VoidZero's Vite distribution) through `overrides`, so Vite updates follow `vite-plus` releases. Node pinned to 24 through `.nvmrc` only; the `engines` field planned in 0.10 §7.3 was dropped by the owner to stay aligned with the upstream starter kit and keep a single source for the Node version |
| 2026-10-30 | 1.4, 1.5 | Deferred by the owner; task 1.6 started before them. Both remain required for the Phase 1 exit criteria (E-2, E-4, E-5) and before Phase 2. Open findings F-1 … F-3 and F-6 stay open until then |
| 2026-10-30 | 1.6 | SEC-AUTH-1 limits: registration 10/hour per IP; reset links 5/min per IP and 3 per 10 min per normalized email. SEC-AUTH-2 message: "If an account exists for that email address, a password reset link has been sent." SEC-AUTH-8 log fields: event, guard, user ID (no email or IP). Password policy changed by the owner, deviating from 0.7 SEC-AUTH-3 / RF-6: production minimum length 12 → 8, composition rules kept, `uncompromised()` (HIBP) removed; accepted risk: breached passwords are no longer rejected (AU-1) |
| 2026-10-30 | F-1 | Fixed ahead of the deferred task 1.5 at the owner's request: `phpunit.xml` no longer sets `DB_HOST`; tests run inside Sail (`vendor/bin/sail artisan test`). Running tests with host PHP is not supported (shared compiled-view cache) |
