# Contributing to UMS

Thank you for considering a contribution! This project welcomes issues, bug fixes, and feature proposals.

## Getting Started

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature-name`
3. Make your changes
4. Run the quality checks (see below)
5. Submit a Pull Request with a clear description

## Quality Checks

Before submitting, ensure your changes pass:

```bash
# PHP syntax check (required)
find . -name '*.php' -exec php -l {} \;

# Smoke tests (required for functional changes)
powershell -File tools\smoke_modules.ps1

# Mobile smoke tests (if touching mobile shell)
powershell -File tools\smoke_mobile.ps1
```

## Code Style

- **PHP 8.0+** with strict typing (`declare(strict_types=1);`)
- **PSR-12** compatible formatting
- **No framework conventions** — this is vanilla PHP
- **Prepared statements only** — never concatenate SQL
- **Output escaping** — all output through `e()` helper

## Commit Messages

Use conventional commits:
- `feat:` new feature
- `fix:` bug fix
- `docs:` documentation only
- `refactor:` code restructuring
- `test:` test additions/changes
- `chore:` maintenance

Example: `feat(grades): add bulk import from CSV`

## Pull Request Guidelines

- Keep PRs focused — one logical change per PR
- Update documentation if behavior changes
- Add tests for new functionality
- Ensure CI passes (syntax, smoke tests, security scan)

## Reporting Issues

Use the issue template. Include:
- PHP version, MySQL/MariaDB version
- Steps to reproduce
- Expected vs actual behavior
- Screenshots if UI-related

## Security Issues

Do not open public issues for security vulnerabilities. Email directly: shariif.cabdiraxman@gmail.com

## License

By contributing, you agree your contributions will be licensed under the MIT License.