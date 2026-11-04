# FirmHub — Progress

Status of every task, updated at each checkpoint. Plan and acceptance criteria: [ROADMAP.md](ROADMAP.md) and [0.10 §7](phase-0/0.10-architecture-consolidation-and-phase-1-plan.md).

## Current state

| | |
|---|---|
| Phase | 2 — Companies & Tenancy (proof of concept) |
| Last approved task | 2.1 Industry Catalog (2026-11-04) |
| Current task | 2.2 Companies & Owner Membership |
| Next task | 2.3 Dashboard Authorization |
| Deferred | Phase 1: 1.5, 1.9, 1.10 (Phase 1 closed with exceptions; required before Phase 7). Phase 2: 2.6, 2.7, 2.8, 2.10 (after the proof of concept) |

## Phase 1 tasks

| Task | Name | Status | Commit |
|---|---|---|---|
| 1.1 | Baseline Validation | Approved | — (validation only) |
| 1.2 | Project Documentation & Identity | Approved | `0d0a041` (PR #3) |
| 1.3 | Frontend Toolchain Remediation | Approved | `80e35bc` (PR #4) |
| 1.4 | CI & Dependency Automation | Approved | `20ec73c` (PR #18) |
| 1.5 | Test Foundation | Deferred | — |
| 1.6 | Authentication Hardening | Approved | `c031045` (PR #5) |
| 1.7 | Case-Insensitive Email Uniqueness | Approved | `14f86f9` (PR #8) |
| 1.8 | Platform Host, Trusted Hosts & Proxies | Approved | `78539ee` (PR #9) |
| 1.9 | Security Headers Baseline | Deferred | — |
| 1.10 | Production Configuration Guardrails | Deferred | — |
| 1.11 | Platform Admin Foundation | Approved | `270a392` (PR #10) |
| 1.12 | Localization Foundation | Approved | `62203db` (PR #16) |
| 1.13 | Dashboard Shell & Branding | Approved | `d25aa57`, `206d3ba` (PR #14) |


## Phase 2 tasks

| Task | Name | Status | Commit |
|---|---|---|---|
| 2.0 | Phase 1 Closure & Phase 2 Plan | Approved | `7a213fb` (PR #19) |
| 2.1 | Industry Catalog | Approved | `94b9553`, `dd4681b` |
| 2.2 | Companies & Owner Membership | In review | — |
| 2.3 | Dashboard Authorization | Not started | — |
| 2.4 | Company Onboarding | Not started | — |
| 2.5 | Company Profile | Not started | — |
| 2.6 | Company Switcher | Deferred (after the proof of concept) | — |
| 2.7 | Member Management | Deferred (after the proof of concept) | — |
| 2.8 | Invitations | Deferred (after the proof of concept) | — |
| 2.9 | Account Deletion Guard | Not started | — |
| 2.10 | Company Deletion Lifecycle | Deferred (after the proof of concept) | — |

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
| F-2 | `livewire/livewire` 4.4.7 — `#[Authorize]` bypass, GHSA-mhf7-xp6r-6qrc (medium, fixed in 4.4.8). The project does not use `#[Authorize]` or `#[Json]` | **Closed in task 1.4** — `livewire/livewire` 4.4.8 |
| F-3 | `symfony/mime` 7.4.19 — CVE-2026-45070 email header injection (medium, fixed in 7.4.22). No custom MIME parameter handling in `app/` | **Closed in task 1.4** — `symfony/mime` 7.4.22 |
| F-4 | `npm audit`: `tinypool` (critical, prototype pollution to RCE), `oxfmt`, `@vitest/*` — all through `vite-plus@0.3.0`; build-time tools listed under `dependencies` | **Closed in task 1.3** — `vite-plus` 1.4.0, `npm audit` reports 0 vulnerabilities |
| F-5 | Local `.env` uses `QUEUE_CONNECTION=database`; `.env.example` and TDR-11 specify `sync` | Local file, not tracked; owner to align |
| F-6 | CI runs only on pushes to `main` and uses Node 22 | Trigger: CI runs on pull requests into `main` and pushes to `main`, not on `develop` (owner decision 2026-11-02, see DECISIONS). Node 22 → 24: **closed in task 1.4** (`node-version-file: .nvmrc`) |

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
| Checked in the browser | Home, login, register, dashboard and profile settings on desktop and mobile, light and dark; the mobile sidebar opens and closes; keyboard focus moves through the home page links with a visible focus ring, and the user menu opens with Enter and closes with Escape. Signed in as the seeder's `test@example.com` user, created in the local development database for this check |

Icons are regenerated from `public/favicon.svg` with ImageMagick (not a project dependency):

```bash
magick -background none -density 576 public/favicon.svg -define icon:auto-resize=48,32,16 public/favicon.ico
sed 's/ rx="7"//' public/favicon.svg > /tmp/apple-touch-icon.svg
magick -density 864 /tmp/apple-touch-icon.svg -resize 180x180 -background '#18181b' -alpha remove -alpha off -strip -depth 8 public/apple-touch-icon.png
```

## Task 1.12 — Localization Foundation (2026-11-03)

| Change | Detail |
|---|---|
| Languages | `app.supported_locales` (`en` English, `tr` Türkçe); default `APP_LOCALE=en` |
| Translations | Short keys in one folder per language, plus one JSON file per translated language for package strings. `lang/en/{auth,pagination,passwords,validation}.php` are Laravel's published files (safe to overwrite with `lang:publish --force`); `lang/tr/` mirrors them by hand. Application lines use short keys in `common` and `pages` (the authentication and settings pages are its `authentication` and `settings` sections); the 28 strings Laravel, Fortify and Flux pass by their English text (error page titles, notification emails, Fortify messages, Flux labels) are translated in `lang/tr.json` with that text as the key, as Laravel documents; English needs no JSON file. A 404 under a locale prefix is shown in that language without storing it. Terminology: "şifre" for password, "Panel" for Dashboard |
| Hard-coded text | `OTP Code` labels go through `__()`; Livewire page titles are translated in each component's `render()` |
| Selection (OD-6) | `SetLocale` (web group, platform host only; tenant hosts are left alone): URL prefix → user's `users.locale` → `locale` cookie → default; a 404 under a locale prefix is shown in that language; sets Fortify's `home` (`/{locale}/dashboard`). Livewire updates take the page's language from the checksummed component snapshot (`SupportLocales`). A language is stored (signed-in user's preference and visitor cookie) only when a page with a locale prefix is opened by a GET top-level navigation (`Sec-Fetch-Mode: navigate`, `Sec-Fetch-Dest: document`) with `Sec-Fetch-Site` `none` or `same-origin`, and without `Sec-Purpose` unless it is a prerender started from the address bar (Chrome shows it without a new request); links from other sites (including same-site tenant subdomains), prefetches, scripts, Livewire requests, unprefixed redirects and requests without fetch metadata store nothing. Links opened from email or chat apps send `Sec-Fetch-Site: none` like the address bar and do count. Unprefixed file paths (`/wp-login.php`, `/.env`) return 404 without a redirect. `URL::defaults(['locale' => …])` follows every locale change (`LocaleUpdated`), so email links use the email's language. Livewire page titles are translated in each component's `render()`; the layout prints titles as given |
| Accepted values | `App\Actions\Localization\UpdateLocalePreference` is the single allow-list (`supportedLocales`, `isSupported`, `rules`) and the only place that stores a choice (`handle`) (user preference + cookie); used by the middleware, `User::preferredLocale()` and the settings page, and mirrored by the `{locale}` route pattern. Unsupported prefixes never match a page; unsupported cookie or stored values fall back to `en`; unsupported submitted values are rejected. The cookie is encrypted and signed by Laravel, so it cannot be forged without `APP_KEY` |
| Routes | Every platform page has the locale prefix: home, Fortify (`fortify.prefix = {locale}`), dashboard and settings (`/en/login`, `/tr/dashboard`, `/en/settings/profile`). `Route::pattern('locale', 'en|tr')` applies to all of them. `/` and any other unprefixed GET path redirect to the same path and query in the request language (`RedirectToLocalizedPathController`, fallback route); unprefixed non-GET requests get 405; paths under an unsupported prefix end in 404. `/up`, Livewire and Flux endpoints stay unprefixed |
| Preference | Migration `users.locale` (`varchar(8)`, nullable); set at registration from the page language, by opening a page in another language and in Settings → Appearance; `User` implements `HasLocalePreference`, so password reset and verification emails use it |
| Switchers | Links to the same page in the other language on the home and authentication pages; a language select in Settings → Appearance that moves to the same page in the chosen language |
| Tests | Prefix rendering, unsupported prefixes, root and unprefixed redirects (cookie, stored preference, query string), storing the URL language for typed and same-origin navigation but not for cross-site, same-site, prefetch, script or header-less requests (preference and cookie), switcher links (query strings as sent, signed URLs to the home page), Livewire updates in the page's language without storing it, boot failure for an unsupported `APP_LOCALE`, settings field, registration language, email language and email link language (including unsupported stored values such as `tr/..`), tenant-host isolation of prefixed routes, and translation completeness (every used key defined in `en`, every language folder mirroring `en`, every string the used Laravel, Fortify and Flux views and classes pass to `__()` translated in each JSON file (with a list of strings of unused components), no JSON line the packages no longer render) |
| Applied to | Local development database `firmhub` (rollback → migrate verified) |
| Checked in the browser | `/dashboard` → `/en/login` as a guest; after login `/en/dashboard`; typed `/tr/dashboard` opens Turkish and makes `/dashboard` redirect to `/tr/dashboard`; on `/tr/settings/profile` with the stored preference set to `en`, a Livewire validation error is shown in Turkish and the preference stays `en`; no console errors |

Residual risks: Turkish texts are machine-assisted and should be reviewed by a native speaker before launch; Flux strings of Pro components and Laravel strings not used by the application are not translated. Open follow-up (owner, 2026-11-04): `RedirectToLocalizedPathController` answers 404 for any unprefixed path whose last segment contains a dot, so once pages take domain names as path segments (Phase 5 custom domains, e.g. `/sites/example.com`) an unprefixed link would 404 instead of redirecting; narrow the rule (known file extensions or names starting with a dot) before such routes are added.

## Task 1.4 — CI & Dependency Automation (2026-11-04)

| Change | Detail |
|---|---|
| Security patches | `livewire/livewire` 4.4.7 → 4.4.8 (GHSA-mhf7-xp6r-6qrc) and `symfony/mime` 7.4.19 → 7.4.22 (CVE-2026-45070), updated alone with `composer update livewire/livewire symfony/mime`; `composer audit` reports no advisories (F-2, F-3) |
| CI trigger | Pull requests into `main` and pushes to `main` only (owner rule: workflows and Dependabot always work on `main`); `develop` is tested locally |
| CI steps | Node from `.nvmrc` (24, was 22); `composer audit --locked` and `npm audit --audit-level=high` before the setup; migration rollback check after the setup (`migrate:fresh` → `migrate:rollback` → `migrate`, all with `--force`), then the existing `composer ci:check` (Pint, Larastan, Pest) |
| Dependabot | `composer` and `npm` added next to `github-actions`; all three weekly, `target-branch: main`, cooldown 5 days (30 days for major versions of Composer and npm packages); minor and patch updates grouped per ecosystem, major updates in their own pull requests |
| `laravel/chisel` | Installed by the starter kit, not referenced by application code, configuration or tests; kept by owner decision |

| Check | Result |
|---|---|
| `composer audit --locked` | Exit 1 with the previous lock file (2 advisories), exit 0 after the patches (run in the Sail container on copies of the lock files) |
| `npm audit --audit-level=high` | Exit 1 for a lock-only copy with `lodash` 4.17.20 (high advisories, no code installed), exit 0 for the project |
| Migration rollback check | Exit 0 on the Sail `testing` database; exit 1 with a temporary migration whose `down()` drops a missing table (probe removed, `testing` rebuilt; the development database was not touched) |
| Workflow and Dependabot files | Parsed as YAML; GitHub validates both after the push |

Not verified here: the workflow run on GitHub and Dependabot's acceptance of the configuration need a push and are checked on the next pull request into `main` or Dependabot's first pull request (also into `main`). Dependabot security updates (separate from version updates) are a repository setting the owner enables on GitHub.

## Task 2.1 — Industry Catalog (2026-11-04)

| Change | Detail |
|---|---|
| Table | `industries`: `bigint` identity key, `slug` (`text`, unique, CHECK lowercase words joined by hyphens so it is a valid language key), `is_active` (default `true`), `sort_order`, `timestampsTz()` (0.3 §5.2, C-6); no `name` column |
| Model | `Industry` with the `offered()` scope (active industries in display order) and `label()` (name in the current language); deactivated, never deleted |
| Catalog | `IndustrySeeder` with three industries for the proof of concept: `retail`, `legal`, `other`; adds missing industries and keeps the order, leaves the active state of existing ones; called from `DatabaseSeeder` and documented in the README for every deployment |
| Labels | `lang/en/industries.php` (Retail, Legal Services, Other) and `lang/tr/industries.php` (Perakende, Hukuk Hizmetleri, Diğer), keyed by slug (0.3 D-8) |
| Applied to | Local development database `firmhub`: migration run and `IndustrySeeder` seeded (owner approval, 2026-11-04) |
| Tests | Unique slug and slug format on PostgreSQL; seeder order and idempotence; deactivated industries stay hidden; `offered()` filtering and order; label in the current language; every seeded industry labelled in every language (plus the existing language-folder parity test) |

## Task 2.2 — Companies & Owner Membership (2026-11-04)

| Change | Detail |
|---|---|
| `companies` | UUIDv7 key (`HasUuids` generates `Str::uuid7()`, verified for 0.10 CR-12), `name`, `industry_id` (`RESTRICT`, indexed), `contact_email` (CHECK lower case), phone and WhatsApp, optional legal fields, `established_year` (CHECK 1800–2100), `onboarding_completed_at`, `suspended_at`, `suspension_reason`, soft deletes, `timestampsTz()` (0.3 §5.3) |
| `company_memberships` | UUIDv7 key, `company_id` and `user_id` (cascade), `role` (CHECK owner/admin/editor/viewer), unique `(company_id, user_id)`, temporary unique `user_id` for the one-company rule (it also indexes the foreign key), partial owner index; `invited_by_user_id` comes with invitations (2.8) (0.3 §5.4) |
| Enums | `App\Enums\CompanyRole` (rank Owner 4 … Viewer 1, `permissions()`, `grants()`) and `App\Enums\CompanyPermission` with the Phase 2 company permissions of 0.2 §7.1 (view, update, change industry, complete onboarding, delete) |
| Models | `Company` (industry, onboarding and suspension fields not fillable; contact email stored in lower case), `CompanyMembership` (nothing fillable, `MassAssignmentException` on attempts), `User::membership()`; factories with `withOwner()`, `role()` and `owner()` states |
| `CreateCompany` | `App\Actions\Company\CreateCompany::handle($owner, $name, $industry)`: verified owners only (403 otherwise), active industries only, one company per user; company and Owner membership in one transaction; a membership created by a simultaneous request makes the unique constraint fail and returns the same validation error; the contact email starts as the owner's email |
| Applied to | Local development database `firmhub` (owner approval, 2026-11-04) |
| Tests | PostgreSQL constraints (lower-case email, year range, industry `RESTRICT` → SQLSTATE `23001`, role values, one company per user, cascades); the action's success, unverified, inactive-industry, existing-company and simultaneous-request cases (company rolled back); mass assignment; contact email lower-casing; the role × permission matrix written out in the test (0.9 §4.2) and the role ranking |
