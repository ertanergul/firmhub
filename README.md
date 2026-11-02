# FirmHub

FirmHub is a multi-tenant website builder and company CMS. Companies, local businesses and professional service firms create, edit and publish their websites from one dashboard; each site is served on its own subdomain, with custom domains planned for a later phase.

> **Status:** Phase 1 — Foundation. The application is still the Laravel Livewire starter kit plus a PostgreSQL/Sail environment; product features start in Phase 2. See [docs/ROADMAP.md](docs/ROADMAP.md) and [docs/PROGRESS.md](docs/PROGRESS.md).

## Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13, PHP 8.3, Fortify |
| Frontend | Livewire 4, Flux, Blade, Alpine (bundled with Livewire), Tailwind CSS 4, Vite |
| Database | PostgreSQL 18 |
| Development | Laravel Sail (Docker): `laravel.test` + `pgsql` |
| Quality | Pest 4, Pint, Larastan (level 7) |

Architecture: [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md). Decisions: [docs/DECISIONS.md](docs/DECISIONS.md).

## Requirements

- Docker with Docker Compose
- Node.js 24 and npm (frontend assets are built on the host)
- PHP 8.3 and Composer on the host are optional; every PHP command can run through Sail

## Local setup

```bash
git clone git@github.com:ertanergul/firmhub.git
cd firmhub
cp .env.example .env
composer install            # or run it through the laravelsail/php83-composer image
vendor/bin/sail up -d
vendor/bin/sail artisan key:generate
vendor/bin/sail artisan migrate
npm install
npm run build
```

The application is available at <http://localhost> (`APP_PORT`, default `80`). The PostgreSQL container creates the `firmhub` development database and a separate `testing` database on first start.

`.npmrc` sets `ignore-scripts=true`; packages that need install scripts must be handled explicitly.

## Daily commands

| Task | Command |
|---|---|
| Start / stop containers | `vendor/bin/sail up -d` / `vendor/bin/sail stop` |
| Vite dev server | `npm run dev` |
| Production asset build | `npm run build` |
| Run migrations | `vendor/bin/sail artisan migrate` |
| Code style (fix) | `vendor/bin/sail composer lint` |
| Code style (check) | `vendor/bin/sail composer lint:check` |
| Static analysis | `vendor/bin/sail composer types:check` |
| Tests | `vendor/bin/sail artisan test --compact` |
| Pint + Larastan + tests | `vendor/bin/sail composer test` |
| PHP dependency audit | `vendor/bin/sail composer audit` |
| JS dependency audit | `npm audit` |

Use `vendor/bin/sail composer …` rather than host Composer when the host version is older than 2.4 (no `audit` command).

### Running tests

Run the suite inside Sail. `phpunit.xml` switches the database to `testing`, so tests never touch the development database. The database host is not overridden: inside Sail it comes from `.env` (`pgsql`); CI sets `DB_HOST=127.0.0.1` in the workflow environment.

Running tests with host PHP is not supported: compiled Blade views in `storage/framework/views` are shared between the host and the container and contain container paths, so mixing both breaks view rendering on whichever side did not compile them (`php artisan view:clear` on that side recovers).

## Tenant subdomains locally

Tenant sites will be served on `{subdomain}.localhost` (for example `http://acme.localhost`). Platform pages (login, dashboard, settings) answer only on the `APP_URL` host (`localhost`) and return 404 on any other host; in production company sites live under the same domain (e.g. `acme.firmhub.com` next to `firmhub.com`). Outside the local environment, requests for hosts other than the `APP_URL` host and its single-label subdomains are rejected with 400 before reaching the application. When using a tunnel such as ngrok, set `APP_URL` to the tunnel URL. Chrome, Firefox, Edge and curl resolve `*.localhost` to the loopback address without configuration; Safari behavior is not yet verified. A dnsmasq setup for `*.firmhub.test` is the documented fallback ([0.4 §8.1](docs/phase-0/0.4-subdomain-domain-and-publishing.md)).

## Platform admins

The platform admin flag is set only from the command line. `--reason` is required and is written to the application log together with the operating system user; `--actor` records the user who requested the change.

```bash
vendor/bin/sail artisan platform-admin:grant user@example.com --reason="Operations on-call"
vendor/bin/sail artisan platform-admin:revoke user@example.com --reason="Left the operations team"
```

## Contributing and security

- Workflow, commit conventions and Definition of Done: [CONTRIBUTING.md](CONTRIBUTING.md)
- Reporting vulnerabilities: [SECURITY.md](SECURITY.md)

## License

The project license has not been decided yet (owner decision OD-1 in [docs/DECISIONS.md](docs/DECISIONS.md)). The `license` field in `composer.json` is inherited from the starter kit and does not represent that decision; a `LICENSE` file will be added once it is made.
