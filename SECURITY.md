# Security Policy

## Supported Versions

| Version | Supported |
|---------|-----------|
| 1.x (current) | ✅ Yes |

## Reporting a Vulnerability

**Do not open a public issue.** Email directly: **shariif.cabdiraxman@gmail.com**

Include:
- Description of the vulnerability
- Steps to reproduce
- Potential impact
- Suggested fix (if any)

You will receive acknowledgment within 48 hours. Fixes for critical issues are typically released within 7 days.

## Security Features

This project implements:

| Control | Implementation |
|---------|----------------|
| **Authentication** | bcrypt (cost 10) via `password_hash()`; passwords never logged |
| **Authorization** | Role-based (`admin`/`registrar`); server-side `require_permission()` on every write |
| **SQL Injection** | All queries use prepared statements with bound parameters (`mysqli_stmt`) |
| **XSS Prevention** | All output escaped via `e()`; CSP header with `script-src 'self'` |
| **CSRF Protection** | Per-session tokens on all state-changing forms |
| **Brute-Force** | 5 failed logins → 15-minute session lockout |
| **Timing Attacks** | `password_verify()` runs even for non-existent users (dummy hash) |
| **Session Security** | `HttpOnly`, `Secure` (when HTTPS), `SameSite=Strict`, 1h idle / 8h absolute timeout |
| **Headers** | `X-Content-Type-Options: nosniff`, `X-Frame-Options: DENY`, `Referrer-Policy: same-origin`, `Permissions-Policy`, `Content-Security-Policy` |
| **Delete Guards** | FK violations caught and reported with human-readable messages |

## Known Limitations

- Demo credentials exist in seed data (`APP_DEMO_MODE=1`) — disable in production
- No rate limiting at network level (recommend reverse proxy / WAF)
- No audit logging for data mutations (planned)
- Password reset flow not implemented (admin-only user management)

## Dependencies

This project has **zero runtime dependencies** (no Composer, no npm). Security surface is limited to:
- PHP core + `mysqli` extension
- MySQL/MariaDB server

Regularly update PHP and database server for security patches.