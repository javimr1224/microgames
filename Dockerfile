# Stage 1: Node / Vite build
FROM node:20 AS node-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: PHP / Laravel
FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libssl-dev pkg-config \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
RUN pecl install mongodb && docker-php-ext-enable mongodb

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --from=node:20 /usr/local/bin /usr/local/bin
COPY --from=node:20 /usr/local/lib /usr/local/lib

WORKDIR /var/www
COPY . .  # Código base
COPY --from=node-builder /app/public/build /var/www/public/build  # Assets generados por Vite

RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache public/build

RUN composer install --no-interaction --optimize-autoloader

EXPOSE 8080
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT} -t public"]
