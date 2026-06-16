# Architecture

A brief tour of how the Airport Incident Dashboard is structured.

## Overview

The application is a server-rendered Laravel 11 app. Blade templates are
progressively enhanced with Alpine.js (interactivity) and Chart.js (analytics).
Persistence defaults to SQLite for zero-config local use and runs on MySQL 8 in
the Docker stack. The codebase follows a thin-controller / rich-request style:
validation lives in FormRequests, domain constants in enums, and access control
in middleware.

## Request lifecycle

```
Browser
  -> routes/web.php  (auth + role middleware)  ->  Controller
       -> FormRequest (validation + authorization)
       -> Eloquent model / query
  <-  Blade view (Tailwind + Alpine + Chart.js)  or  JSON (drill-down endpoints)
```

## Layers

### Routing (`routes/`)
- `web.php` — application routes, grouped under `auth`. User management is
  additionally wrapped in `role:admin`.
- `auth.php` — NIP login/logout and the authenticated change-password route.
  Email-based Breeze flows are intentionally not registered.

### Authentication & authorization
- **Login**: `App\Http\Requests\Auth\LoginRequest` authenticates by `nip` +
  `password`, applies login throttling (5 attempts per NIP+IP), and regenerates
  the session on success.
- **RBAC**: `App\Http\Middleware\EnsureUserHasRole` (alias `role`) enforces an
  exact role match, with admins implicitly authorized for every role-guarded
  route.

### Controllers (`app/Http/Controllers/`)
Thin orchestration only. `DashboardController`, `MapController`, and
`IncidentDetailController` assemble aggregate/JSON data; `IncidentController`,
`FlightController`, and `UserController` provide CRUD.

### Validation (`app/Http/Requests/`)
Every state-changing request has a dedicated FormRequest
(`StoreIncidentRequest`, `UpdateIncidentRequest`, `StoreFlightRequest`,
`StoreUserRequest`, `ProfileUpdateRequest`, …) so controllers never touch raw
input.

### Domain models (`app/Models/`, `app/Enums/`)
- `User` — NIP, role, optional email, hashed password.
- `Incident` — casualty record (condition, hospital, gate/location, flight no,
  occurrence date).
- `Flight` — flight records.
- `IncidentCondition` (enum) — single source of truth for allowed casualty
  values plus their display labels and Tailwind chart/badge styling.

### Frontend (`resources/`)
Blade layouts and components, Tailwind for styling (dark-mode first), Alpine.js
for toasts/menus, and Chart.js for the dashboard doughnut chart. Built with Vite.

### Data (`database/`)
- Migrations define the schema (users, incidents, flights, sessions, cache,
  jobs).
- Factories generate synthetic Faker `id_ID` data.
- `DemoSeeder` provisions the demo admin (NIP 10001) and operator (NIP 10002)
  plus synthetic incidents and flights.

## Testing

38 Pest feature tests in `tests/Feature` cover authentication, RBAC,
incident/flight CRUD, the dashboard, the map, drill-down detail endpoints, and
user management. Run with `php artisan test`.

## Deployment

A multi-stage `Dockerfile` builds frontend assets (Node), installs production
PHP dependencies (Composer), and serves the app via PHP 8.3-FPM behind nginx,
supervised by `supervisord`. `docker-compose.yml` wires the app to MySQL 8.
See the README for usage.
