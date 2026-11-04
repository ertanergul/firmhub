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
| O-7 | Separate registrable tenant domain in production | **Superseded** 2026-11-01 (task 1.8): tenant sites live under the platform domain (Option A), see change log |
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
| P-2 | Separate tenant domain (Option B), configurable | **Superseded** 2026-11-01 (task 1.8): Option A, still configurable, see change log |
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
| OD-1 | Project license (MIT proposed) — 0.1 Q18 / 0.8 Q-7 | Before a `LICENSE` file and public release | **Decided** 2026-11-02: MIT, see change log |
| OD-2 | Branch protection with required CI checks on `develop` / `main` — 0.9 Q-6 | After task 1.4 | Open |
| OD-3 | Production platform and tenant domain names — 0.4 P-3 | Public staging (before Phase 5) | Open |
| OD-4 | Hosting region and mail provider location — 0.1 Q15 / 0.7 SQ-9 | Phase 7 | Open |
| OD-5 | Legal review: retention (Q11), legal footer fields (Q14), consent checkbox (SQ-4), controller/processor roles and DPA (0.7 §7.1) | Before production | Open |
| OD-6 | Dashboard language selection (0.1 Q7). Recommendation: per-user preference on `users` (default `APP_LOCALE`), editable in settings; no URL locale prefix | Task 1.12 | **Decided** 2026-11-02, **revised** 2026-11-03: locale prefix on every platform page, see change log |

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
| 2026-10-30 | 1.7 | 0.3 D-6 / SEC-AUTH-5 implemented: emails normalized to lowercase in the application and enforced by a functional unique index on `lower(email)`; no `CHECK (email = lower(email))` on `users` (0.3 C-8 applies to new tables only) |
| 2026-10-31 | 1.8 | Environment variables `PLATFORM_HOST`, `TENANT_DOMAIN`, `TENANT_SCHEME`, `TENANT_PORT`, `TRUSTED_PROXIES`. Trusted proxies use the framework's `trustedproxy.proxies` config instead of a custom middleware (simpler than planned, same behavior) |
| 2026-10-31 | 1.8 | Custom domain fallback origin (0.4 §8.2, Phase 5) is named `origin.{tenant_domain}` (owner decision, replaces the `customers.{tenant_domain}` example); `origin` must be added to the reserved subdomain list (0.4 §4.2) when it is implemented in Phase 3 |
| 2026-10-31 | 1.8 | Naming (owner decision): the platform/tenant host settings live in `config/domains.php` instead of the `config/firmhub.php` named in 0.4 §2.1/§3.3, 0.8 §6.2 and 0.9 §3. Mapping: `firmhub.platform_host` → `domains.platform_host`, `firmhub.tenant_domain` → `domains.tenant_domain`, `firmhub.tenant_scheme` → `domains.tenant_scheme`, `firmhub.tenant_port` → `domains.tenant_port`. Other settings planned for `config/firmhub.php` (subdomain rules, limits, quotas, indexing switch) go to topic-specific config files when introduced |
| 2026-11-01 | 1.8 | Owner revised the earlier decision to run the trusted-host check in every environment: the framework `TrustHosts` middleware is used as is (skipped in local and tests), configured with the platform host and single-label tenant subdomain patterns; the custom `App\Http\Middleware\TrustHosts` was removed. Tests assert the configured patterns; host isolation in local and tests comes from the platform-host route constraints |
| 2026-11-01 | 1.8 | Phase 5 constraint: the trusted host patterns accept only the platform host and single-label tenant subdomains, so outside local and tests a request for a verified custom domain (e.g. a customer-owned `www.example.com` whose DNS points at `origin.{tenant_domain}`) is rejected with 400 before routing. The custom domain work in Phase 5 must extend the `trustHosts(at: ...)` closure in `bootstrap/app.php` to also trust verified custom domains (for example from a cached list), without falling back to trusting every host |
| 2026-11-01 | 1.8 | Owner chose 0.4 Option A over the accepted Option B (0.2 O-7, 0.4 P-2, 0.7 SEC-HOST-3): tenant sites live under the platform domain (platform `firmhub.com`, company site `acme.firmhub.com`, custom domain e.g. `example.com` in Phase 5). `domains.tenant_domain` defaults to the `APP_URL` host and stays overridable through `TENANT_DOMAIN`. Consequences: tenant hosts are same-site with the dashboard, so SEC-SESS-2 (`__Host-` session cookie) is mandatory before launch (0.7 SEC-HOST-3, risk R-7) and platform cookies must stay host-only (0.4 SD-4); the platform host's reserved subdomains (e.g. `www`, `api`, `admin`, `origin`) must be on the reserved subdomain list in Phase 3; tenant content reputation is shared with the platform domain |
| 2026-11-01 | 1.8 | `TENANT_SCHEME` / `TENANT_PORT` (`domains.tenant_scheme`, `domains.tenant_port`) removed before use, deviating from the 0.10 §7.3 task 1.8 scope: nothing reads them yet. They return with `SiteUrlGenerator` in Phase 3, defaulting to the `APP_URL` scheme and port |
| 2026-11-01 | 1.8 | Follows from Option A: the platform host and the tenant domain are both the `APP_URL` host, so `config/domains.php`, `PLATFORM_HOST` and `TENANT_DOMAIN` were removed (this also supersedes the earlier `domains.*` naming entry and the 0.4 §2.1 requirement of independent configuration values). A single derived key `app.host` in `config/app.php` is used by the platform routes, Fortify, the Livewire update route and the trusted host patterns. Going back to a separate tenant domain (Option B) requires reintroducing a tenant domain setting. `config/trustedproxy.php` stays: the framework `TrustProxies` middleware reads `trustedproxy.proxies` at request time, while `trustProxies(at:)` in `bootstrap/app.php` takes no closure and `env()` returns null there once the configuration is cached |
| 2026-11-01 | 1.8 | `app.host` is `parse_url(env('APP_URL'), PHP_URL_HOST)` and falls back to `localhost` when no host can be read (missing `APP_URL`, or one without a scheme such as `firmhub.com`). The fallback keeps the platform routes host-constrained, so a malformed `APP_URL` makes the platform unreachable instead of exposing it on every host |
| 2026-11-01 | 1.9, 1.10 | Deferred by the owner. Both remain required for the Phase 1 exit criteria (E-8, E-9) and before Phase 2. Within 1.9, HSTS (SEC-HDR-4) and the report-only dashboard CSP with nonces (SEC-HDR-2) were already set aside for a later review; Livewire, Flux and Vite all read `Vite::cspNonce()`, so no Blade change is needed when it is done. Within 1.10, the `__Host-` session cookie (SEC-SESS-2) is mandatory before launch because of Option A (0.7 SEC-HOST-3) |
| 2026-11-02 | 1.11 | 2FA requirement for platform admins (0.2 O-4, 0.7 SEC-ADMIN-1) deferred by the owner, deviating from the 0.10 §7.3 task 1.11 scope: `platform-admin:grant` does not check 2FA and the `platform.admin` gate checks only the flag. Accepted risk (0.7 PL-3): a compromised admin password is enough for admin access. Must be implemented before any admin route or admin action uses the gate |
| 2026-11-02 | 1.11 | Platform admin commands: `--reason` required (SEC-ADMIN-2); actor logged as the operating system user plus the user ID given by `--actor`; email addresses are not logged (SEC-AUTH-8); a grant or revoke that changes nothing succeeds without writing or logging; revoking the last platform admin is allowed (shell access can grant again) |
| 2026-11-02 | — | CI (`.github/workflows/tests.yml`) runs on pull requests into `main` and pushes to `main` only, not on pull requests into `develop` (owner decision, deviating from 0.8 MD-3, 0.9 §9 G3 and the 0.10 §7.3 task 1.4 goal "CI on `develop`"). Feature branches are checked locally before merging into `develop` (G2: Pint, Larastan, full Pest suite in Sail); CI is the gate for releases. Consequence: a defect that only appears in a clean environment (missing file, untracked dependency, environment difference) is found when `develop` is merged into `main` instead of per feature |
| 2026-11-02 | 1.12 | Deferred by the owner. Required for exit criterion E-11 and before Phase 2; OD-6 (dashboard language selection) stays open until then (0.10 §7.5) |
| 2026-11-02 | 1.13 | Brand: a placeholder "F" monogram replaces the Laravel logo and icons until a real FirmHub logo is provided; the home page is a minimal FirmHub page with `noindex` until a marketing page exists |
| 2026-11-02 | 1.13 | `public/robots.txt` is not changed, deviating from 0.6 §4 ("disallowed in platform `robots.txt`"): the static file is served on every host, including tenant subdomains from Phase 3 (Option A), and 0.6 §1 already requires replacing it with a per-host route in Phase 3. Platform pages carry `noindex, nofollow` instead; a crawler blocked by `robots.txt` could not read that tag |
| 2026-11-02 | OD-1 | Owner chose the MIT license for FirmHub's own code: `LICENSE` (copyright FirmHub), `composer.json` `license: MIT`, README updated; `package.json` stays `private` without a license field. Dependency check (`composer licenses --no-dev`, direct npm packages): MIT, BSD-2/3-Clause and Apache-2.0, except `nette/schema` and `nette/utils` (BSD-3-Clause or GPL, BSD-3-Clause applies) and `livewire/flux` (`proprietary`). Open risk: the Flux Terms of Service (fluxui.dev/terms, updated 2024-09-24) are written for purchased licenses and list "using Flux in any open source project" and "a website builder where end users can build sites using Flux components" as prohibited; it is not stated whether they apply to the free edition, which Laravel's MIT-licensed starter kit uses. Tenant sites do not use Flux (0.8 TDR-4), but the dashboard does. Owner decision: keep using the free edition the same way as the official starter kit; accepted risk, not legal advice |
| 2026-11-02 | OD-6 | Dashboard language (owner decision, replaces the 0.10 recommendation "no URL locale prefix"): default `en`, supported `en` and `tr`. Only marketing pages carry a locale prefix with English slugs (`/en`, `/tr`, later `/en/privacy`, `/tr/privacy`); `/` redirects to the visitor's language. Authentication pages (`/login`, `/register`, `/forgot-password`, `/reset-password/{token}`, `/two-factor-challenge`) and signed-in pages (`/dashboard`, `/settings/*`, `/user/*`, `/email/verify`) have no prefix, so Fortify and Livewire routes stay unchanged. Resolution order: URL prefix (stored in a cookie) → signed-in user's `users.locale` → cookie → `en`. A visitor without a choice sees `en`; registration stores the current language as the user's preference, login does not change it; `User` implements `HasLocalePreference` so emails follow the preference. Switchers: EN/TR links on marketing and authentication pages, a language field in Settings → Appearance. Marketing pages get `hreflang` links when they become indexable. Implemented in task 1.12 |
| 2026-11-03 | 1.12 | Resumed by the owner and implemented as decided in OD-6. Implementation choices: the language switch on authentication pages is a `POST /locale` form (no state change on GET); the cookie `locale` lives one year and follows the signed-in user's preference, so `/` after logout opens the user's language; Livewire `#[Title]` values are passed through `__()` in `partials/head`; the default `URL::defaults(['locale' => APP_LOCALE])` is set at boot for links generated outside a request |
| 2026-11-03 | 1.12 | Locale input hardening (owner request): one allow-list in `App\Actions\Localization\UpdateLocalePreference` for every locale input, including `User::preferredLocale()`, so a stored value that bypassed validation cannot break mail delivery. No database `CHECK` constraint on `users.locale`: it would need a migration for every new language, and the allow-list plus `varchar(8)` covers it |
| 2026-11-03 | OD-6 | Revised by the owner, replacing the 2026-11-02 entry ("prefix on marketing pages only") and the `POST /locale` switch: every platform page carries the locale prefix (`/en/dashboard`, `/tr/login`, `/en/settings/profile`); Fortify uses `fortify.prefix = {locale}`. Unprefixed GET paths redirect to the user's stored language, else the visitor's cookie, else `en`. Opening a page in another language stores it as the signed-in user's preference, limited to `Sec-Fetch-Site: none` or `same-origin` so links from other sites (including tenant subdomains, which are same-site under Option A) cannot change it. Livewire requests keep the page's language through `Livewire::addPersistentMiddleware([SetLocale::class])`. The language switcher is a plain link to the same page in the other language |
| 2026-11-03 | 1.12 | Review fixes: a URL language is stored (user preference and visitor cookie) only when the user opened the page: a GET top-level navigation (`Sec-Fetch-Mode: navigate`, `Sec-Fetch-Dest: document`) from the address bar, a bookmark or a platform link, never a prefetch or prerender (`Sec-Purpose`), a script request or a link from another site; links from other sites no longer overwrite the visitor's cookie either. `URL::defaults` follows every `LocaleUpdated` event, so links in emails sent in the user's language use that language. `SetLocale` skips the Livewire update endpoint and runs once, as persistent middleware with the page's route. The language switcher links signed URLs to the home page, because the signature covers the prefix |
| 2026-11-03 | 1.12 | Second review fixes: `Livewire::addPersistentMiddleware([SetLocale::class])` is removed, because Livewire already restores the page's language from the checksummed component snapshot (`SupportLocales`); on the update endpoint `SetLocale` only sets a provisional language (from the user's preference or the cookie, used if the update fails before hydration) and stores nothing. Redirects and switcher links copy the query string as sent (`GenerateLocalizedUrl`), keeping dotted and repeated keys. The `locale` cookie is owned by `UpdateLocalePreference`. An `APP_LOCALE` outside `app.supported_locales` stops the boot, since every generated link would be a 404 |
| 2026-11-03 | 1.12 | Owner request: every language keeps all its files in its own folder (`lang/en`, `lang/tr`, later `lang/de`). The JSON strings move from `lang/tr.json` to `lang/tr/tr.json`, registered for each supported locale with `loadJsonTranslationsFrom(lang_path($locale))`; Laravel only reads JSON strings from files named after the locale, so the file keeps that name. A file left at `lang/{locale}.json` would still be loaded and would override the folder file |
| 2026-11-03 | 1.12 | Owner request, replacing the previous entry: no JSON translation files and no separate package file. Application strings use short keys in `lang/{locale}/{common,authentication,settings,pages}.php`; strings that Laravel, Fortify and Flux pass by their English text live in `mail.php`, `errors.php`, `common.flux` and `authentication.errors`, and `AppServiceProvider` resolves them with `Lang::handleMissingKeysUsing()` by matching the text against `lang/en` (no custom loader, no new folder). Laravel's `auth`, `pagination`, `passwords` and `validation` files are left untouched so `php artisan lang:publish --force` stays safe. Consequences: only one missing-key callback can be registered; `Lang::has()` is false for package strings; a package wording change silently falls back to English, which a test catches by checking the English lines against the package sources. A 404 under a locale prefix is now shown in that language (not stored) |
| 2026-11-03 | 1.12 | Owner request: package strings keep short keys, and the English-text lookup table is removed. Each package string's key is its English text in snake case (`Str::slug($text, '_')`: `'Not Found'` is `errors.not_found`, `'The provided password was incorrect.'` is `authentication.errors.the_provided_password_was_incorrect`), so the missing-key callback looks the key up directly in `AppServiceProvider::PACKAGE_STRING_SECTIONS` (`common.flux`, `authentication.errors`, `mail`, `errors`). `mail.php` is flat because only a section's top-level lines are looked up. Sentence keys are long; a test checks that every package line is found by its English text |
| 2026-11-03 | 1.12 | Owner request: `mail.php` and `errors.php` are merged into `common.php` as its `mail` and `errors` sections (`PACKAGE_STRING_SECTIONS` is `common.flux`, `common.mail`, `common.errors`, `authentication.errors`). `pagination.php` stays a separate file by owner choice: Laravel's pagination views look up `pagination.previous` and `pagination.next` directly, and `php artisan lang:publish --force` recreates `lang/en/pagination.php` |
| 2026-11-03 | 1.12 | Owner request: `settings.php` is merged into `pages.php` as its `settings` section (keys such as `pages.settings.profile.title`). The application's own files are now `common`, `authentication` and `pages`; Laravel's `auth`, `pagination`, `passwords` and `validation` stay separate for `lang:publish --force` |
| 2026-11-03 | 1.12 | Owner request: `authentication.php` is merged into `pages.php` as its `authentication` section (keys such as `pages.authentication.login.title`; Fortify's messages are `pages.authentication.errors`, so `PACKAGE_STRING_SECTIONS` is `common.flux`, `common.mail`, `common.errors`, `pages.authentication.errors`). The application's own files are now `common` and `pages` |
| 2026-11-03 | 1.12 | Owner request: the error page titles move from `common.errors` to `pages.errors`, since error pages are pages; Flux labels (shared components) and Laravel's emails stay in `common.flux` and `common.mail`. `PACKAGE_STRING_SECTIONS` is `common.flux`, `common.mail`, `pages.errors`, `pages.authentication.errors`. When the application sends its own emails (Phase 2), `common.mail` is expected to move to its own `mail.php` |
| 2026-11-03 | 1.12 | Owner decision, replacing the package-string entries above after comparing with the Laravel 13 localization docs: the application's own strings keep short keys in `lang/{locale}/{common,pages}.php`, and the 28 strings Laravel, Fortify and Flux pass to `__()` by their English text move to `lang/tr.json` (later `lang/de.json`), the documented way to translate such strings and the layout Laravel's starter kits and the laravel-lang translations use. Removed: the `Lang::handleMissingKeysUsing()` callback, `PACKAGE_STRING_SECTIONS`, the snake-case key rule and the package sections in `common.php` and `pages.php`. `lang/en.json` is not needed. `lang/vendor/{package}` overrides do not apply, because none of these packages registers a translation namespace. Known limit: with a single translated language, a line deleted from `lang/tr.json` is only caught by the sampled translation tests; once a second language exists, the JSON files are checked against each other |
| 2026-11-03 | 1.12 | Review fixes: the Flux OTP label (`Character {current} of {total}`) is added to `lang/tr.json`, and a test now extracts every string the used Laravel, Fortify and Flux sources pass to `__()` and fails when a JSON file misses one (strings of components the application does not use yet are listed in the test). A prerender started from the address bar now stores the language, because Chrome shows it without a new request. A language is stored only when a page with a locale prefix is opened, so unprefixed redirects and 404 probes set no cookie, and unprefixed file paths return 404 without a redirect. `SetLocale` only acts on the platform host, so tenant sites keep their own language handling. Livewire page titles are translated in each component's `render()` and the layout no longer passes titles through `__()`, which would return a whole language file for a title such as "pages". The `APP_LOCALE` check skips `config:cache`, `config:clear`, `optimize` and `optimize:clear`, so a bad cached value can be fixed. Accepted limit: links opened from email or chat apps send `Sec-Fetch-Site: none` like the address bar, so they store the language; telling them apart is not possible from the request, and not storing `none` would break the owner's rule that typing `/tr/dashboard` stores Turkish |
| 2026-11-04 | 1.4 | Implemented with owner choices, deviating from 0.10 §7: GitHub workflows and Dependabot always work on `main` (owner rule): CI runs on pull requests into `main` and pushes to `main` only, with no `develop` trigger, so the 0.10 acceptance "CI green on a pull request to `develop`" is met by a pull request into `main`; Dependabot version updates for Composer, npm and GitHub Actions target `main`. The livewire and symfony/mime security patches are applied in this task. `laravel/chisel` (0.8 Q-8) is unused but kept by the owner. Node comes from `.nvmrc` (0.8 MD-2) |
| 2026-11-04 | — | Phase 1 closed by the owner with exceptions, deviating from the 0.10 §7.5 entry conditions: tasks 1.5 (test foundation), 1.9 (security headers) and 1.10 (production guardrails) are not done, so exit criteria E-1, E-2 (`Arch` suite), E-8 and E-9 are open; E-10 already deviates (2FA check for platform admin commands deferred). These tasks stay required and must be done before Phase 7 (production readiness) starts; the `__Host-` session cookie (1.10, SEC-SESS-2) is mandatory before launch because of Option A. Until the `Arch` suite exists, architecture-style rules (SEC-AUTHZ-1, the dashboard route inventory) are written as feature tests |
| 2026-11-04 | — | Phase 2 starts as a proof of concept (owner decision): **a user belongs to at most one company**, temporarily deviating from 0.1 FR-MEM-01 and the company switcher (Must). The dashboard stays at `/{locale}/dashboard` without a company segment, deviating from 0.2 §4.1: a `CurrentCompany` middleware resolves the user's only membership, and policies and actions always receive the company explicitly, so moving to `/{locale}/dashboard/{company}` later only changes the middleware's source and the route group. A temporary unique constraint on `company_memberships.user_id` enforces the rule. Deferred until after the proof of concept: company switcher (2.6), member management (2.7), invitations (2.8) and the company deletion lifecycle (2.10); the company creation limit (0.2 O-8) has no effect while the rule holds. After the narrowed Phase 2, the proof of concept continues with a minimal Phase 3 (site, subdomain, core sections, preview, publishing, public rendering) |
