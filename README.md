# BookingWeb
Step-by-step guide to build and run this Laravel web application with Docker.

## 1) Requirements
- Docker Desktop (with Docker Compose)
- Git

## 2) Build and start containers
Run from the project root:

```bash
docker compose up -d --build
```

This builds and starts:
- `app` (PHP-FPM / Laravel)
- `web` (Nginx)
- `db` (MySQL)

## 3) Install backend dependencies

```bash
docker compose exec app composer install
```

## 4) Create your environment file

```bash
docker compose exec app cp .env.example .env
```

## 5) Configure database in `.env`
Open `.env` and set:

```dotenv
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=bookingweb
DB_USERNAME=bookingweb
DB_PASSWORD=bookingweb
```

## 6) Generate app key

```bash
docker compose exec app php artisan key:generate
```

## 7) Run database migrations

```bash
docker compose exec app php artisan migrate
```

## 8) Build frontend assets
For a production asset build:

```bash
docker compose run --rm vite sh -c "npm install && npm run build"
```

For live frontend development (hot reload):

```bash
docker compose --profile frontend up -d vite
```

Vite dev server: `http://localhost:5173`

## 9) Open the application
Web app URL: `http://localhost:8000`

## Useful commands
Stop containers:

```bash
docker compose down
```

Stop and remove DB volume (full reset):

```bash
docker compose down -v
```

View logs:

```bash
docker compose logs -f app web db
```
