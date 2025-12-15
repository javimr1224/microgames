# ----------------------------
# Stage 1: Build del Frontend (React)
# ----------------------------
FROM node:20 AS frontend-builder
WORKDIR /app
COPY frontend/package*.json ./
RUN npm ci
COPY frontend/ .
RUN npm run build

# ----------------------------
# Stage 2: Build de Laravel (Assets)
# ----------------------------
FROM node:20 AS laravel-assets-builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# ----------------------------
# Stage 3: Producción (Debian - Más simple)
# ----------------------------
FROM php:8.2-fpm

# 1. Instalar todo lo necesario con apt-get (Nginx, Supervisor y librerías)
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libssl-dev \
    # Herramientas de compilación para que funcione 'pecl install' sin errores
    autoconf \
    build-essential \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Instalar extensiones PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 3. Instalar MongoDB (Ahora compilará sin problemas gracias a build-essential)
RUN pecl install mongodb && docker-php-ext-enable mongodb

# 4. Copiar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 5. Instalar dependencias de Laravel
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts

# 6. Copiar código y builds
COPY . .
COPY --from=laravel-assets-builder /app/public/build ./public/build

# Crear carpeta de juegos y copiar el frontend
RUN mkdir -p public/games
COPY --from=frontend-builder /app/build ./public/games

# 7. Configurar Nginx (Ruta estándar de Debian)
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
# Borrar la config por defecto de Nginx para evitar conflictos
RUN rm -f /etc/nginx/sites-enabled/default

# 8. Permisos
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# 9. Configurar Supervisor (Para correr Nginx + PHP)
RUN echo "[supervisord]" > /etc/supervisor/conf.d/supervisord.conf && \
    echo "nodaemon=true" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "[program:php-fpm]" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "command=php-fpm" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "[program:nginx]" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "command=nginx -g 'daemon off;'" >> /etc/supervisor/conf.d/supervisord.conf

# Limpieza final de Laravel
RUN php artisan package:discover --ansi

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]