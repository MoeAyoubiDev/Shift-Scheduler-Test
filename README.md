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

## Deploy to DigitalOcean (Ubuntu + Nginx + PHP-FPM)

> These commands assume Ubuntu 22.04/24.04 on a DigitalOcean droplet.

1. Install system dependencies:

```bash
sudo apt update
sudo apt install -y nginx mysql-server php8.1-fpm php8.1-mysql php8.1-xml php8.1-mbstring php8.1-curl unzip
```

2. Install Composer:

```bash
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```

3. Clone the repo and install dependencies:

```bash
git clone <your-repo-url> /var/www/shift-scheduler
cd /var/www/shift-scheduler
composer install --no-dev --optimize-autoloader
```

4. Configure environment:

```bash
cp config/.env.example config/.env
sudo nano config/.env
```

5. Configure Nginx (replace `your-domain.com`):

```bash
sudo tee /etc/nginx/sites-available/shift-scheduler <<'NGINX'
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/shift-scheduler/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \\.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }

    location ~ /\\.ht {
        deny all;
    }
}
NGINX
```

6. Enable the site and restart services:

```bash
sudo ln -s /etc/nginx/sites-available/shift-scheduler /etc/nginx/sites-enabled/shift-scheduler
sudo nginx -t
sudo systemctl restart nginx php8.1-fpm
```

7. (Optional) Set up HTTPS with Certbot:

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

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
