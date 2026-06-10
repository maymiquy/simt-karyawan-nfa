# Changelog — SIMT-NFA
Employee Task Management Information System — NF Academy.

Format based on [Keep a Changelog](https://keepachangelog.com/).

---

## [Unreleased] — 2026-06-10

### Fixed
- **Attachment public URL** now returns a **root-relative** path (`/storage/...`) instead of an
  absolute URL built from `APP_URL`. This fixes a 404 when opening attachments while the app runs
  via `php artisan serve` (the absolute URL pointed to the wrong host/port). Applies to both the
  employee and manager task-detail views.

---

## [0.11.0] — 2026-06-09 — Phase 10: Employee Dashboard Expansion

### Added
- **"My Activity" page** — cross-task activity timeline (from `assignment_logs`) with type & date-range filters.
- **"My KPI Detail" page** — summary + 3-dimension breakdown vs target, a **6-month KPI trend chart (Chart.js)**,
  and a table of this month's tasks with quality score and drop reasons (late/revision), aligned with the KPI v2 formula.
- **Deadline Calendar** — monthly grid marking tasks by `due_date`; dot color = priority,
  cell color = status (done/overdue); month navigation.
- **In-app notifications** (Laravel Notifications, database channel):
  - Triggers: new task assigned (observer), revision requested (manager + Filament), deadline < 24h
    (scheduled command `tasks:notify-deadlines`, hourly, de-duplicated).
  - **Header bell** with unread badge, dropdown list, "mark all read", and Alpine polling every 60s.
- **Advanced filter & sort in "My Tasks"** — quick tabs (All / Today / This week / Overdue)
  plus sorting by nearest deadline, priority, or work duration.
- **File attachments on reports** — employees upload JPG/PNG/PDF (max 5 files @ 5 MB) when submitting
  activities; shown in both employee and manager task detail. Stored on the `public` disk
  (requires `php artisan storage:link`).

### Database
- New migrations: `create_notifications_table`, `create_assignment_attachments_table`.
- Model `AssignmentAttachment`; relation `Assignment::attachments()`; notification `App\Notifications\EmployeeAlert`.
- Command `tasks:notify-deadlines` + hourly schedule in `routes/console.php`.

---

## [0.10.0] — 2026-06-08 — Phase 9: KPI v2 (Composite)

### Added
- **KPI v2 implemented**, replacing the v1 aggregate:
  `KPI% = 0.5×Quality% + 0.3×OnTime% + 0.2×Completion%`, computed **per monthly period**.
  - **Quality%** = average of `max(0, 10 − 2×revisions)`, priority-weighted (high 1.5 / medium 1 / low 0.75).
  - **OnTime%** = % of approved tasks submitted ≤ deadline.
  - **Completion%** = approved tasks ÷ tasks due in the period (closes the v1 anti-gaming gap).
- **[`config/kpi.php`](config/kpi.php)** — configurable weights, priority weights, revision penalty, target (85%), bands.
- **`KpiService` v2** — `summaryForUser()` (3-dimension breakdown + **trend** this month vs last + **band** + target);
  lightweight `percentForUser()` for lists.
- **Employee dashboard**: KPI card (percent, band, trend ▲/▼, target) + **KPI Breakdown** card (3 dimension bars).
- Calculation verified via Tinker (sample scenarios consistent).

### Changed
- Employee KPI aggregate (dashboard, employee list, Activity Log) now uses **v2**.
- `assignments.kpi_score` (v1) **deprecated** as an aggregate — still used as a **per-task** score indicator.

### Fixed
- Manager dashboard **chart & "Active Tasks"** cards that were still light-themed → full dark mode.
- Employee task-detail **Action** panel widened (`lg:grid-cols-5`, action `col-span-2`);
  activity status select narrowed (`w-24`) so the activity input row is longer.

---

## [0.9.0] — 2026-06-07 — Phase 8: Activity Items, KPI, Timeline & Approval

### Added
- **Activity-list based reporting** (kanban style): employees add dynamic activity rows (Alpine) on submit —
  description + status (Done/Blocked). Table `assignment_activities`.
- **Communication note** field on the employee submit form (`communication_note`).
- **Per-employee KPI v1**: score `10 − (late?1) − 2×revisions` (finalized on approval),
  KPI% = normalized average of approved scores. `App\Services\KpiService`.
- **Assignment lifecycle timeline** (`assignment_logs`: created/started/submitted/revised/approved)
  + components `<x-assignment-timeline>` & `<x-kpi-badge>`.
- **Manager "Activity Log" page** — accordion per employee → task → timeline (single page).
- **Activity feed** on manager & employee dashboards; KPI card on the employee dashboard.
- **Work-duration indicator** (start → finish) on task detail.
- Filament admin: **Approve/Request Revision** actions compute KPI + write log + **toaster**; KPI & revision columns.

### Changed
- Assignment progress flow: `not_started → on_progress → submitted → done | revision → on_progress`
  (new **`submitted`** status = awaiting review).
- `tasks.due_date` changed from **date** to **datetime** (timeliness precise to the hour);
  manager & admin forms use a date-time picker.
- `assignments` gained columns: `assigned_at`, `started_at`, `communication_note`, `revision_count`, `kpi_score`.
- Manager `review` & Filament approval now increment `revision_count` and record the timeline.

### Migrations
- `change_due_date_to_datetime_on_tasks`, `add_kpi_and_timing_to_assignments`,
  `create_assignment_activities_table`, `create_assignment_logs_table`.

---

## [0.8.0] — 2026-06-06 — Toaster & Dark Mode

### Added
- **Dark mode** for all Employee & Manager pages (header toggle, persisted via `localStorage`, no flash).
  Tailwind v4 `@variant dark` configuration.
- **Toast notification** (`<x-toast />`) for login/logout on Employee, Manager & Login pages.
- **Toaster in the Filament admin panel** via the `FlashNotification` widget (reads session flash → Filament Notification).
- Welcome flash on login & message on logout.

### Fixed
- Several components still light-themed under dark mode → added `dark:` variants
  (status-badge, priority-badge, modal, toast, page-header, confirm-delete, error pages, etc.).

---

## [0.7.1] — 2026-06-05 — Auth & Access Fixes

### Fixed
- `User::canAccessPanel()` — Employees can no longer access `/admin` (Admin & Manager only).
- **403/419** errors on admin login & employee/manager logout: added a `GET /logout` fallback
  plus custom **403** & **419** error pages.
- `Call to undefined method User::assignments()` on the Manage Employees page → added
  `assignments()` & `createdTasks()` relations to the `User` model.

---

## [0.7.0] — 2026-06-04 — Phase 7: UI System & Shared Components

### Added
- Reusable Blade components: `stats-card`, `page-header`, `empty-state`, `modal`, `toast`, `confirm-delete`.
- **Real-time search** (Alpine, debounced) on the employee task list + filter pills.
- CSS animations & utilities (`page-fade`, `card-hover`, etc.).

### Changed
- Refactored Employee/Manager layouts & dashboards to use shared components; visual consistency
  (font, radius, shadow, colors).

---

## [0.6.0] — Phase 6: Overdue Scheduler

### Added
- Command `tasks:check-overdue` ([`UpdateOverdueTasks`](app/Console/Commands/UpdateOverdueTasks.php)):
  marks `pending`/`in_progress` tasks past their deadline as `overdue` + logs it.
- Daily `00:05` schedule in `routes/console.php`.

---

## [0.5.0] — Phase 5: Admin Panel Extension (Filament)

### Added
- **TaskResource**: priority column + status/priority/deadline-range/overdue filters, overdue due-date badge, "Cancel" bulk action.
- **AssignmentResource**: relations (not raw IDs), report/note fields, Approve & Request Revision actions.
- **UserResource**: role badge column, active task count, filter by role.
- **ActivityLogResource**: filter by action/user/date.
- Widgets **TasksChart** (done vs overdue, last 7 days) & **OverdueTasksTable**.
- **Reports** page (filter + summary + PDF/Excel download).

---

## [0.4.0] — Phase 4: Manager Dashboard & Pages

### Added
- Manager controllers: Dashboard, Task (CRUD), Assignment (assign/review/destroy), Employee, Report.
- Manager dashboard: 4 stat cards, weekly **Chart.js** bar chart, active tasks table, FAB.
- Manage Tasks (filter + table + actions), create/edit form (multi-assignee), task detail (assignment tracker).
- **Reports**: filter + preview + **PDF (DomPDF)** & **Excel (Maatwebsite)** download — `TaskReportExport`, `pdf/task-report` view.
- Manage Employees: list + add-account modal (auto Employee role).
- Manager layout (sidebar; Admin Panel link for Admins).

---

## [0.3.0] — Phase 3: Employee Dashboard & Pages

### Added
- Employee controllers: Dashboard & Task (index/show/updateProgress/submitReport).
- Employee layout (responsive sidebar), dashboard (stats + donut + recent tasks).
- Task list (filter + search + pagination) & task detail (progress-aware actions).
- `status-badge` & `priority-badge` components.

---

## [0.2.0] — Phase 2: Web Authentication

### Added
- **Custom login page** (split-screen, Alpine password toggle, remember me).
- `LoginController` (validation + role-based redirect: Admin → `/admin`, Manager → dashboard, Employee → dashboard).
- Spatie role middleware registered in `bootstrap/app.php`; Alpine.js added to the bundle.

---

## [0.1.0] — Phases 0 & 1: Setup, Database, Models & Observers

### Added
- Project setup (MySQL env, key generate, migrate, seed, build assets).
- Libraries: `barryvdh/laravel-dompdf`, `maatwebsite/excel`, `laravel/sanctum` (+ `HasApiTokens`).
- Migrations: `priority` on tasks (+ `overdue` status), completion fields on assignments, personal access tokens.
- `Task` model (overdue/active scopes, `is_overdue` accessor), `Assignment`, observers `TaskObserver` & `AssignmentObserver`
  (auto ActivityLog + task status sync; `Task::withoutEvents()` to prevent loops).

---

## Architecture Notes
- This application is a **Laravel 12 + Filament 5 semi monolith with micro services** (Blade calls Eloquent directly; no internal API).
- The **REST API** is a separate project, `simt-nfa-api`.

*Maintained since 2026-06-02. Last updated: 2026-06-10.*
