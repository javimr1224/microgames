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
# Stage 3: Producción (Debian)
# ----------------------------
FROM php:8.2-fpm

# 1. Instalar Nginx, Supervisor y herramientas
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
    autoconf \
    build-essential \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Instalar extensiones PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 3. Instalar MongoDB
RUN pecl install mongodb && docker-php-ext-enable mongodb

# 4. Copiar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 5. Instalar dependencias Laravel
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts

# 6. Copiar código y builds
COPY . .
COPY --from=laravel-assets-builder /app/public/build ./public/build
RUN mkdir -p public/games
COPY --from=frontend-builder /app/build ./public/games

# 7. Configurar Nginx y Logs
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
RUN rm -f /etc/nginx/sites-enabled/default

# --- FIX AUTO: Redirigir logs de Nginx a la consola de Railway ---
# Así podrás ver el error real si vuelve a fallar
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

# --- FIX AUTO: Corregir automáticamente el host de PHP (app -> 127.0.0.1) ---
# Esto asegura que Nginx encuentre a PHP aunque el archivo original esté mal
RUN sed -i 's/app:9000/127.0.0.1:9000/g' /etc/nginx/conf.d/default.conf

# 8. Permisos
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# 9. Configurar Supervisor
RUN echo "[supervisord]" > /etc/supervisor/conf.d/supervisord.conf && \
    echo "nodaemon=true" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "[program:php-fpm]" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "command=php-fpm" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "[program:nginx]" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "command=nginx -g 'daemon off;'" >> /etc/supervisor/conf.d/supervisord.conf && \
    # Redirigir logs de procesos a stdout también
    echo "stdout_logfile=/dev/stdout" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "stdout_logfile_maxbytes=0" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "stderr_logfile=/dev/stderr" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "stderr_logfile_maxbytes=0" >> /etc/supervisor/conf.d/supervisord.conf

# Cache de Laravel
RUN php artisan package:discover --ansi

EXPOSE 80

# 10. Script de inicio que ajusta el PUERTO dinámico de Railway
CMD ["/bin/sh", "-c", "sed -i 's/listen 80;/listen ${PORT:-80};/g' /etc/nginx/conf.d/default.conf && /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf"]