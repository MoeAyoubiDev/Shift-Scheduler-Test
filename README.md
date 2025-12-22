# Shift Scheduler - Enterprise Workforce Management System

Shift Scheduler is a PHP 8+ and MySQL workforce management platform built for multi-section organizations. The system supports role-based access, request approvals, automated schedule generation, break compliance monitoring, and performance analytics.

## Features

- Role-based dashboards for directors, team leaders, supervisors, seniors, and employees.
- Section isolation for App After-Sales and Agent After-Sales.
- Shift request workflows with importance levels and work patterns.
- Automated schedule previews and editable assignments.
- Break monitoring with compliance tracking.
- Performance analytics and export-ready data.

## Project Structure

```
public/              # Front controller and assets
src/
  Controllers/       # Request handling and orchestration
  Models/            # Domain models and data access
  Views/             # PHP view templates
  Core/              # Config, routing, and utility classes
config/              # Environment configuration
storage/             # Logs and runtime artifacts
```

## Getting Started

1. Install PHP 8.1+ and MySQL 8.
2. Install Composer dependencies:

```bash
composer install
```

3. Copy the environment configuration:

```bash
cp config/.env.example config/.env
```

4. Update database credentials in `config/.env`.
5. Start the local PHP server:

```bash
php -S localhost:8000 -t public
```

6. Visit `http://localhost:8000`.

## Demo Credentials

| Role | Email | Password |
| --- | --- | --- |
| Director | director@shift.test | password |
| Team Leader | leader@app.test | password |
| Supervisor | supervisor@agent.test | password |
| Senior | senior@app.test | password |
| Employee | employee@agent.test | password |

## Database

Schema stubs and stored procedure outlines are available in `database/migrations/001_init.sql` to jump-start MySQL implementation.

## Next Steps

- Replace in-memory demo data in `src/Models` with MySQL stored procedure calls.
- Implement request approvals, schedule generation, and break tracking workflows.
- Add notifications and CSV exports for Team Leaders.
