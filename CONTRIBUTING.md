# Contributing

Thanks for helping improve UMS. Small, focused pull requests are easiest to
review.

## Before You Start

1. Check the existing issues and comment before starting substantial work.
2. Fork the repository and create a branch from `main`.
3. Keep one bug or feature per pull request.

## Local Setup

UMS requires PHP 8.0+ with `mysqli`, MySQL or MariaDB, and a PHP-capable web
server such as Apache or Nginx. Import `database.sql`, copy
`config/database.example.php` to `config/database.php`, and configure local
credentials. Never commit `config/database.php`.

## Verification

Run `php -l` on every changed PHP file. For browser behavior, start the local
server and run the smoke scripts in `tools/` from PowerShell:

```powershell
./tools/smoke.ps1
./tools/smoke_modules.ps1
./tools/smoke_mobile.ps1
```

If a script needs a running database or web server, document the prerequisite
and the result in the pull request.

## Pull Requests

Use the pull request template, link the related issue, describe verification,
and include screenshots for visible UI changes. Keep formatting and unrelated
refactors out of focused changes.

Please read [SECURITY.md](SECURITY.md) for vulnerability reports.
