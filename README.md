# BookingOcra

> Laravel-based property booking web application — Docker powered, production ready.

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)
![Nginx](https://img.shields.io/badge/Nginx-1.27-009639?logo=nginx&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?logo=docker&logoColor=white)
![CI](https://github.com/Ruuby666/bookingWeb/actions/workflows/ci.yml/badge.svg)

---

## Table of Contents

- [Requirements](#requirements)
- [Project Structure](#project-structure)
- [Local Development Setup](#local-development-setup)
- [Environment Variables](#environment-variables)
- [Database](#database)
- [Frontend (Vite)](#frontend-vite)
- [Useful Commands](#useful-commands)
- [Deployment](#deployment)
- [Troubleshooting](#troubleshooting)

---

## Requirements

| Tool | Version |
|------|---------|
| [Docker Desktop](https://www.docker.com/products/docker-desktop/) | Latest |
| Git | Latest |

> No need to install PHP, Composer, Node, or MySQL locally — Docker handles everything.

---

## Project Structure

```
bookingWeb/
├── app/                    # Laravel application logic
├── database/
│   ├── migrations/         # Database schema
│   └── seeders/            # Seed data
├── docker/
│   └── nginx/
│       └── default.conf    # Nginx configuration
├── public/                 # Web root
├── resources/
│   └── js/                 # Vite / frontend assets
├── .env                    # Local environment (never commit)
├── .env.example            # Template for new developers
├── docker-compose.yml      # Docker services
└── Dockerfile              # PHP-FPM image
```

---

## Local Development Setup

### 1. Clone the repository

```bash
git clone https://github.com/Ruuby666/bookingWeb.git
cd bookingWeb
```

### 2. Create your environment file

```bash
cp .env.example .env
```

Then open `.env` and configure the variables (see [Environment Variables](#environment-variables)).

### 3. Build and start containers

```bash
docker compose up -d --build
```

This starts:

| Container | Description | Port |
|-----------|-------------|------|
| `bookingweb-app` | PHP 8.2-FPM / Laravel | `9000` (internal) |
| `bookingweb-web` | Nginx 1.27 | `http://localhost:8000` |
| `bookingweb-db` | MySQL 8.0 | `localhost:33060` |

### 4. Install PHP dependencies

```bash
docker compose exec app composer install
```

### 5. Generate application key

```bash
docker compose exec app php artisan key:generate
```

### 6. Run migrations and seed the database

```bash
docker compose exec app php artisan migrate:fresh --seed
```

This creates all tables and loads initial data including the admin user.

### 7. Open the application

```
http://localhost:8000
```

Default admin credentials (set in your `.env`):

```
Email:    ADMIN_EMAIL value
Password: ADMIN_PASSWORD value
```

---

## Environment Variables

Copy `.env.example` to `.env` and fill in the required values:

```dotenv
# Application
APP_NAME=BookingOcra
APP_ENV=local
APP_KEY=                        # auto-generated with artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=Atlantic/Canary

# Admin seeder credentials
ADMIN_EMAIL=your@email.com
ADMIN_PASSWORD=YourPassword123

# Database — points to the Docker container
DB_CONNECTION=mysql
DB_HOST=db                      # must be "db" (Docker service name)
DB_PORT=3306
DB_DATABASE=bookingweb
DB_USERNAME=bookingweb
DB_PASSWORD=YourPassword123

# Docker MySQL service
MYSQL_DATABASE=bookingweb
MYSQL_USER=bookingweb
MYSQL_PASSWORD=YourPassword123
MYSQL_ROOT_PASSWORD=root

# Cache & Sessions
CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_LIFETIME=20
QUEUE_CONNECTION=database

# Google Maps
API_GOOGLE_MAPS_KEY=your_google_maps_api_key

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## Database

### Run migrations

```bash
docker compose exec app php artisan migrate
```

### Reset and re-seed (wipes all data)

```bash
docker compose exec app php artisan migrate:fresh --seed
```

### Connect with a GUI client (TablePlus, DBeaver, etc.)

| Field | Value |
|-------|-------|
| Host | `127.0.0.1` |
| Port | `33060` |
| Database | `bookingweb` |
| Username | ... |
| Password | ... |

---

## Frontend (Vite)

### Production build (compiles assets into `public/build`)

```bash
docker compose run --rm vite sh -c "npm install && npm run build"
```

### Development server with hot reload

```bash
docker compose --profile frontend up -d vite
```

Vite dev server: `http://localhost:5173`

> The `vite` service uses the `frontend` profile and only starts when explicitly requested.

---

## Useful Commands

### Container management

```bash
# Start all services
docker compose up -d

# Stop all services
docker compose down

# Stop and remove database volume (full reset)
docker compose down -v

# Rebuild images from scratch
docker compose build --no-cache
```

### Laravel

```bash
# Clear all caches
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear

# Run all cache clears at once
docker compose exec app php artisan optimize:clear

# Open Tinker REPL
docker compose exec app php artisan tinker

# List all routes
docker compose exec app php artisan route:list
```

### Logs

```bash
# All services
docker compose logs -f

# Specific service
docker compose logs -f app
docker compose logs -f web
docker compose logs -f db
```

### Check container status

```bash
docker compose ps
```

---

## Deployment

Este proyecto se despliega en un **VPS con Docker Compose** y Nginx como reverse proxy.

### Requisitos en el servidor

- Docker + Docker Compose
- Dominio apuntando a la IP del servidor
- Certificado SSL (recomendado: [Caddy](https://caddyserver.com/) o Certbot)

### Pasos

```bash
git clone https://github.com/tu-usuario/bookingWeb.git
cd bookingWeb

cp .env.production .env
# Edita .env: APP_URL, credenciales DB, MAIL, etc.

docker compose up -d --build
docker compose exec app composer install --no-dev --optimize-autoloader
docker compose exec app php artisan migrate --force
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
docker compose run --rm vite sh -c "npm ci && npm run build"
` ` `
```

---

## Troubleshooting

### `no configuration file provided: not found`
You are not in the project root. Run `cd path/to/bookingWeb` first.

### `Database file at path [...].sqlite does not exist`
Laravel is reading a stale cached config. Fix with:
```bash
docker compose exec app rm -f bootstrap/cache/config.php
docker compose exec app php artisan config:clear
```

### `composer:2` image fails to pull (Cloudflare network error)
Your ISP may be blocking Cloudflare IPs. The `Dockerfile` installs Composer directly via `curl` to avoid this. If you still see pull errors on other images, try:
- Changing Docker DNS to `8.8.8.8` in Docker Desktop → Settings → Docker Engine
- Using a VPN

```
### Port `8000` already in use
Change the port in `docker-compose.yml`:
```yaml
ports:
  - "8080:80"   # use 8080 instead
```

---

## License

Private project — all rights reserved.
