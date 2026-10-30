# Security Policy

FirmHub hosts websites for many independent companies on shared infrastructure, so tenant isolation and the safety of public sites are treated as core requirements. Thank you for helping keep FirmHub and its users safe.

## Supported versions

FirmHub has not had a release yet. Security fixes are applied to the `develop` branch and reach `main` with the next merge.

| Version | Supported |
|---|---|
| `develop` / `main` | Yes |

## Reporting a vulnerability

**Please do not report security vulnerabilities through public GitHub issues, discussions or pull requests.**

Report privately through GitHub's private vulnerability reporting:

1. Open the repository's **Security** tab.
2. Choose **Report a vulnerability**.
3. Describe the issue, the affected component or URL, steps to reproduce and the impact you observed.

Please include, where possible:

- The type of issue (for example cross-tenant data access, authorization bypass, XSS, host header injection, unsafe upload)
- The full paths or routes involved and the version or commit tested
- Proof-of-concept steps or code
- Whether the issue requires an authenticated account and which role

## What to expect

- Acknowledgement of your report within **5 business days**.
- An initial assessment, including whether the report is accepted, within **10 business days**.
- Progress updates until the issue is resolved, and credit in the release notes if you wish.

Please give us reasonable time to fix the issue before any public disclosure.

## Scope

In scope:

- The FirmHub application code in this repository
- Cross-tenant access: reading or changing another company's data, sites, media or messages
- Authentication, authorization, session and CSRF weaknesses
- Stored or reflected XSS on public sites, the dashboard or emails
- Host header, subdomain and domain handling

Out of scope:

- Vulnerabilities in third-party dependencies that are already publicly known and tracked upstream (reports showing a FirmHub-specific impact are welcome)
- Denial-of-service and volumetric attacks
- Social engineering and physical attacks
- Content published by tenants on their own sites (report abuse separately)

## Safe harbor

We will not pursue action against researchers who act in good faith: avoid privacy violations, data destruction and service disruption; only access data you own or are explicitly permitted to access; and stop and report as soon as you find a vulnerability.

## Security architecture

The threat model and security requirements are documented in [docs/phase-0/0.7-security-architecture-and-threat-model.md](docs/phase-0/0.7-security-architecture-and-threat-model.md).
