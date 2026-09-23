# syntax=docker/dockerfile:1

# ---- Frontend assets ------------------------------------------------------
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources/ resources/
COPY vite.config.js ./
RUN npm run build

# ---- Application image ----------------------------------------------------
FROM php:8.3-fpm AS runtime

RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libsqlite3-dev \
    zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql pdo_sqlite mbstring bcmath exif pcntl gd zip opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer installer checked against its published signature instead of
# piping it straight into php.
RUN curl -fsSL -o composer-setup.php https://getcomposer.org/installer \
    && curl -fsSL -o composer-setup.sig https://composer.github.io/installer.sig \
    && echo "$(cat composer-setup.sig)  composer-setup.php" | sha384sum -c - \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php composer-setup.sig

# Start from the hardened production defaults (display_errors off, etc.)
# instead of PHP's permissive compiled-in defaults, then layer our overrides.
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/html

# The application and its dependencies live in the image, so a deploy is
# "build a new image" and vendor/ always matches composer.lock.
COPY . .
COPY --from=frontend /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist \
    && php artisan storage:link \
    && chown -R www-data:www-data storage bootstrap/cache
