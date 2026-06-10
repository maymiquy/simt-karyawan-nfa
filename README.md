# SIMT-NFA — Employee Task Management Information System

A web-based **Employee Task Management Information System** for **NF Academy** that digitizes
the task lifecycle — assignment, execution, activity reporting, review/approval, and
**automated KPI scoring** — with role-based access for **Admin**, **Manager**, and **Employee**.

---

## System Overview

| Item | Detail |
|------|--------|
| Type | Semi Monolithic with Micro Service web application |
| Roles | Admin (`/admin`), Manager (`/manager`), Employee (`/employee`) |
| Architecture | MVC + Service, Observer, Notification (database), Scheduled Command, Blade Components |

### Tech Stack
PHP 8.2 · Laravel 12 · Filament 5 · Blade · Tailwind CSS 4 · Alpine.js 3 · Vite 7 ·
Chart.js 4 · MySQL/MariaDB · Spatie Laravel Permission 6 · Laravel Sanctum 4 ·
DomPDF & Maatwebsite/Excel (export).

---

## Features / Modules

- **Authentication & RBAC** — custom login, role-based redirect, route protection.
- **Task Management** — CRUD with datetime deadline, priority (low/medium/high) and status.
- **Assignment** — one task can be assigned to multiple employees.
- **Activity Reporting + Attachments** — kanban-style activity list (done/blocked),
  communication note, and proof files (JPG/PNG/PDF).
- **Review & Approval** — Approve or Request Revision (with notes).
- **KPI v2 (monthly, composite)** — `0.5×Quality + 0.3×OnTime + 0.2×Completion`,
  priority-weighted, with target, band, and 6-month trend chart.
- **Timeline & Activity Log** — assignment lifecycle; "My Activity" (employee) & "Activity Log" (manager).
- **In-App Notifications** — bell badge for new assignment, revision request, deadline < 24h.
- **Deadline Calendar** — monthly calendar colored by priority/status.
- **Reports** — export tasks to PDF and Excel.
- **Employee Management** — create accounts, KPI badge per employee.
- **Admin Panel (Filament)** — resources, widgets/charts, approval actions, toasters.
- **Automatic Scheduler** — mark overdue tasks daily, send deadline reminders hourly.
- **Dark mode** — across Employee and Manager interfaces.

---

## Prerequisites

- **PHP >= 8.2** with extensions: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd`, `zip`, `ctype`, `json`, `bcmath`
- **Composer** 2.x
- **Node.js >= 18** + **npm**
- **MySQL 8 / MariaDB 10.4+** (e.g. XAMPP / Laragon)
- **Git**, and symlink support for `php artisan storage:link`

---

## Quick Setup

```bash
composer install
npm install
cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate
# configure DB in .env, then create the database
php artisan migrate --seed
php artisan storage:link      # required for file attachments
npm run build
php artisan serve             # http://localhost:8000
```

For in-app deadline reminders & overdue marking, run the scheduler:
`php artisan schedule:work` (dev) or a cron `* * * * * php artisan schedule:run` (production).

### Default Accounts (seeder)

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@example.com` | `password` |
| Manager | `manager@example.com` | `password` |
| Employee | `employee@example.com` | `password` |

---

## Documentation

- [`CHANGELOG.md`](CHANGELOG.md)

---

*Internal academic project (NF Academy). Built on the Laravel framework (MIT License).*
