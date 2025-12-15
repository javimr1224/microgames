# Stage 1: Node / Vite build
FROM node:20-alpine AS node-builder

WORKDIR /app

# Copiar solo archivos de dependencias primero (mejor caching)
COPY package*.json ./

# Instalar dependencias
RUN npm ci --only=production

# Copiar código fuente
COPY resources ./resources
COPY vite.config.js ./
COPY tailwind.config.js ./
COPY postcss.config.js ./

# Build assets
RUN npm run build

# Stage 2: PHP / Laravel
FROM php:8.2-cli-alpine

# Instalar dependencias del sistema
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    openssl-dev \
    autoconf \
    g++ \
    make

# Instalar extensiones PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar MongoDB extension
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Copiar Composer desde imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copiar archivos de dependencias de PHP
COPY composer.json composer.lock ./

# Instalar dependencias de PHP
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts

# Copiar código de la aplicación
COPY . .

# Copiar assets compilados desde node-builder
COPY --from=node-builder /app/public/build ./public/build

# Crear directorios necesarios y establecer permisos
RUN mkdir -p storage/framework/{sessions,views,cache} \
    storage/logs \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache public/build

# Optimizaciones de Laravel
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Exponer puerto
EXPOSE 8080

# Comando de inicio
CMD php artisan config:clear && php -S 0.0.0.0:${PORT:-8080} -t public