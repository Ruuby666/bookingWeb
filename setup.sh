#!/usr/bin/env bash
set -euo pipefail

echo "Cleaning up..."
docker compose down -v

echo "Setting up BookingWeb..."

echo "Checking environment file..."
if [ ! -f .env ]; then
    echo "   - .env not found. Copying from .env.example..."
    cp .env.example .env
else
    echo "   - .env already exists."
fi

echo "Starting Docker containers..."
docker compose up -d

echo "Waiting for MySQL..."

until docker compose exec -T db mysqladmin ping --silent >/dev/null 2>&1
do
    echo "   - Database not ready yet..."
    sleep 2
done

echo "✅ MySQL is ready."

echo "Installing Composer dependencies..."
docker compose exec -T app composer config process-timeout 1200
docker compose exec -T app composer install --prefer-dist --no-interaction

echo "Generating application key..."
docker compose exec -T app php artisan key:generate --force

echo "Running migrations..."
docker compose exec -T app php artisan migrate --force

echo "Running seeders..."
docker compose exec -T app php artisan db:seed --force

echo "Creating storage link and setting permissions..."
docker compose exec -T app rm -rf public/storage
docker compose exec -T app php artisan storage:link || true
docker compose exec -T app sh -c 'chmod -R 775 storage bootstrap/cache'
docker compose exec -T app sh -c 'chown -R www-data:www-data storage bootstrap/cache || true'

echo "Downloading images..."
docker compose exec -T app bash -c 'curl -L https://github.com/Ruuby666/bookingWeb/releases/download/images_v1/imagens.zip -o /var/www/html/imagens.zip && unzip /var/www/html/imagens.zip -d /var/www/html/storage/app/public/images/ && rm /var/www/html/imagens.zip'

echo "✅ Done! Visit http://localhost:8000"
