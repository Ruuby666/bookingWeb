#!/bin/bash

echo "🚀 Setting up BookingWeb..."

# Levantar contenedores
docker compose up -d

# Esperar a que MySQL esté listo
echo "⏳ Waiting for database..."
sleep 5

# Instalar dependencias
docker compose exec app composer install

# Configurar entorno
docker compose exec app sh -c '[ ! -f .env ] && cp .env.example .env || true'
docker compose exec app php artisan key:generate

# Migraciones
docker compose exec app php artisan migrate --force

# Seeders
docker compose exec app php artisan db:seed --force

# Enlace simbólico de storage
docker compose exec app php artisan storage:link

# Permisos
docker compose exec app chmod -R 775 storage bootstrap/cache
docker compose exec app chown -R www-data:www-data storage bootstrap/cache

# Descargar imágenes desde GitHub Releases
echo "📸 Downloading images..."
curl -L https://github.com/Ruuby666/bookingWeb/releases/download/images_v1/imagens.zip -o imagens.zip
unzip imagens.zip -d storage/app/public/images/
rm imagens.zip

echo "✅ Done! Visit http://localhost:8000"
