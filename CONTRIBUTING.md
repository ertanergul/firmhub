# Contributing to FirmHub

FirmHub is built in small, reviewable steps against an approved architecture. Read [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) and the Phase 0 records in [docs/phase-0/](docs/phase-0/) before changing behavior.

## Working model

| Level | Meaning |
|---|---|
| Phase | A broad development stage ([docs/ROADMAP.md](docs/ROADMAP.md)) |
| Task | One independently implementable, testable and reviewable unit of work |
| Checkpoint | Verification, report and explicit approval at the end of each task |

- One task at a time. Approval of a phase is not approval of all its tasks.
- Every task follows: inspect → plan → implement → verify → report → approval → next task.
- Stay inside the approved scope: no unrelated refactoring, no early modules, no new dependency without approval ([0.8 §3](docs/phase-0/0.8-technology-and-infrastructure-decisions.md)).
- Architecture changes are recorded in [docs/DECISIONS.md](docs/DECISIONS.md); the Phase 0 documents are not edited silently.

## Branching

```text
feature/* → develop → main
```

- Branch from `develop` as `feature/<short-description>`.
- Open pull requests against `develop`; `develop` is merged into `main` for releases.
- Never force-push shared branches, rewrite published history or merge without review.

## Commits

Use [Conventional Commits](https://www.conventionalcommits.org/) in English, one task per commit where practical:

```text
<type>(<optional scope>): <imperative summary>
```

Common types: `feat`, `fix`, `docs`, `test`, `refactor`, `build`, `ci`, `chore`. Examples:

```text
feat(auth): enforce case-insensitive unique user emails
ci: run on develop, add dependency audits and migration rollback check
```

Never commit `.env`, credentials or other secrets.

## Code conventions

- Follow the conventions of sibling files and the Laravel defaults; create files with `php artisan make:*`.
- Business logic lives in `app/Actions/{Module}`; models stay flat in `app/Models` ([0.8 §6](docs/phase-0/0.8-technology-and-infrastructure-decisions.md)).
- Tenant isolation is explicit: company in the URL, membership middleware, policies on every action, database constraints ([0.2](docs/phase-0/0.2-multi-tenancy-and-authorization.md)). Never trust a client-supplied `company_id`.
- Source code, identifiers, documentation and commit messages are in English.

## Tests

- Pest on real PostgreSQL only; no SQLite runs ([0.9 §2](docs/phase-0/0.9-testing-and-quality-strategy.md)).
- Feature tests first; every security decision needs a failing-path test (denied role, foreign tenant, invalid input).
- Test files mirror the class or page under test ([0.9 §4.1](docs/phase-0/0.9-testing-and-quality-strategy.md)).
- Run the narrowest relevant tests while working and the full suite before reporting a task. See the [README](README.md#daily-commands) for commands.

## Definition of Done

From [0.9 §11](docs/phase-0/0.9-testing-and-quality-strategy.md):

1. Acceptance criteria of the task met.
2. Tests for every new decision written and passing on PostgreSQL.
3. Tenant isolation checklist completed for every new tenant resource.
4. Pint, Larastan and the full Pest suite green; frontend build green when relevant.
5. No new dependency without approval; audits clean or findings reported.
6. The task report lists every check actually run, with results; skipped checks are named explicitly.

## Quality gates

| Gate | When | Checks |
|---|---|---|
| G1 — Change | While implementing | Narrowest relevant tests, `vendor/bin/pint --dirty` |
| G2 — Task completion | Before the task report | Full Pest suite, Pint, Larastan, build if frontend changed, migrations fresh + rollback if migrations changed, audits if dependencies changed |
| G3 — Pull request | CI on every PR | Pint, Larastan, Pest, build, audits, migration rollback check (being completed in Phase 1) |

Full definitions: [0.9 §9](docs/phase-0/0.9-testing-and-quality-strategy.md).

## Security

Do not open public issues for vulnerabilities; follow [SECURITY.md](SECURITY.md).
