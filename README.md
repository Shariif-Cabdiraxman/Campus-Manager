# HAGMAH Campus Manager

**The registrar’s workspace for Hagmah University.** Keep students, courses, enrolments, grades, and campus announcements together in one straightforward app.

[![CI](https://github.com/Shariif-Cabdiraxman/Campus-Manager/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/Shariif-Cabdiraxman/Campus-Manager/actions/workflows/ci.yml)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL / MariaDB](https://img.shields.io/badge/MySQL%20%2F%20MariaDB-supported-4479A1?logo=mysql&logoColor=white)](https://mariadb.org/)
[![License: MIT](https://img.shields.io/badge/License-MIT-3A806D.svg)](LICENSE)

## A clearer way to manage academic records

HAGMAH Campus Manager is a lightweight registrar and university management system built with PHP and MySQL. It brings everyday academic administration into one place, with a responsive interface for desktop and mobile.

| | |
|---|---|
| **Academic records** | Students, lecturers, faculties, departments, and courses |
| **Registrar workflows** | Enrolments, grades, and announcements |
| **Access control** | Separate administrator and registrar permissions |
| **On any screen** | Responsive layouts, mobile navigation, and installable PWA support |
| **Simple to run** | No framework, Composer, npm, or build step |

## Try it locally with XAMPP

### 1. Start the services

Open the XAMPP Control Panel and start **Apache** and **MySQL**.

### 2. Get the project

Clone or download this repository into your XAMPP `htdocs` folder. With Git installed, for example:

```powershell
cd C:\xampp\htdocs
git clone https://github.com/Shariif-Cabdiraxman/Campus-Manager.git
```

### 3. Import the sample database

Open [phpMyAdmin](http://localhost/phpmyadmin), choose **Import**, select `database.sql` from the project folder, and run the import. It creates the `university_management` database and sample records.

### 4. Set database credentials

Copy `config/database.example.php` to `config/database.php`. The example uses the standard local XAMPP settings (`127.0.0.1`, port `3306`, user `root`, and an empty password). Change them in `database.php` if your MySQL setup differs. The real config file is ignored by Git so local credentials stay out of commits.

### 5. Open the app

Visit [http://localhost/Campus-Manager/](http://localhost/Campus-Manager/) and sign in with a sample account:

| Role | Username | Password |
|---|---|---|
| Administrator | `admin` | `Admin@123` |
| Registrar | `registrar` | `Registrar@123` |

These accounts are for local demonstration only. Change or remove them, disable demo mode, and configure secure credentials before any real deployment. Demo hints can be disabled by creating an empty `config/demo.off.local.php` file.

## What you can manage

- **University structure:** faculties and departments
- **People:** students, lecturers, and staff accounts
- **Teaching:** courses and student enrolments
- **Assessment:** marks and grades
- **Communication:** campus announcements
- **Dashboard:** at-a-glance academic totals and shortcuts

Student records, course data, and grades are stored in a relational database with foreign-key checks. Access to administrative actions is enforced on the server according to each account’s role.

## Built for desktop and mobile

Use the full registrar workspace on desktop, or switch to the mobile layout on a phone. The app includes a collapsible desktop sidebar, touch-friendly mobile navigation, and installable PWA support. The service worker caches static assets; signed-in pages and academic records are not cached for offline access.

## Technology

- PHP 8.0+ with `mysqli`
- MySQL 8 or MariaDB 10.4+
- Apache through XAMPP, or another PHP-capable web server
- Plain PHP, CSS, and JavaScript—no frontend build required

## Checks

GitHub Actions runs PHP syntax and repository checks, a security scan, and the desktop and mobile smoke suites. To run the smoke suites locally, make sure the app is configured and the web server and database are running, then use PowerShell:

```powershell
powershell -NoProfile -ExecutionPolicy Bypass -File tools\smoke_modules.ps1
powershell -NoProfile -ExecutionPolicy Bypass -File tools\smoke_mobile.ps1
```

## Contributing

Found a problem or have an idea? Open an issue with what you expected and what happened. Pull requests are welcome—please describe the change and include the checks you ran.

## License

Released under the [MIT License](LICENSE).

---

Made for Hagmah University · [@Shariif-Cabdiraxman](https://github.com/Shariif-Cabdiraxman)
