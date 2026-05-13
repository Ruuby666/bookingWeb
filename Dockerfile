FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    curl \
    git \
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
    && docker-php-ext-install pdo_mysql pdo_sqlite mbstring bcmath exif pcntl gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instala Composer directamente sin imagen externa
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html
