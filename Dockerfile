# ----------------------------
# Stage 1: Construcción del Frontend (React/Vite)
# ----------------------------
FROM node:20-alpine AS frontend-builder
WORKDIR /app
# Copiamos solo la carpeta frontend
COPY frontend/package*.json ./
RUN npm ci
COPY frontend/ .
# Construimos el frontend. Según tu vite.config.ts, el output va a 'build'
# y la base es '/play/'
RUN npm run build

# ----------------------------
# Stage 2: Construcción de Assets de Laravel
# ----------------------------
FROM node:20-alpine AS laravel-assets-builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
# Esto genera los archivos en public/build (CSS/JS de Laravel)
RUN npm run build

# ----------------------------
# Stage 3: Imagen Final de Producción (PHP + Nginx)
# ----------------------------
FROM php:8.2-fpm-alpine

# Instalar dependencias del sistema, Nginx y Supervisor
RUN apk add --no-cache \
    nginx \
    supervisor \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    libssl3 \
    freetype-dev \
    libjpeg-turbo-dev

# Instalar extensiones PHP necesarias
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar extensión MongoDB
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Copiar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copiar archivos de dependencias de Laravel primero (para cachear capas)
COPY composer.json composer.lock ./

# Instalar dependencias de PHP (Producción)
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts

# Copiar el código de la aplicación Laravel
COPY . .

# Copiar los assets compilados de Laravel (Stage 2)
COPY --from=laravel-assets-builder /app/public/build ./public/build

# Copiar el build del Frontend (Stage 1) a la carpeta pública de juegos
# IMPORTANTE: Tu Nginx espera los juegos en /var/www/public/games/
RUN mkdir -p public/games
COPY --from=frontend-builder /app/build ./public/games

# Configuración de Nginx
# Reemplazamos la configuración por defecto de Alpine
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Crear archivo de configuración de Supervisor al vuelo
# Esto gestiona que Nginx y PHP corran a la vez
RUN echo "[supervisord]" > /etc/supervisord.conf && \
    echo "nodaemon=true" >> /etc/supervisord.conf && \
    echo "user=root" >> /etc/supervisord.conf && \
    echo "[program:php-fpm]" >> /etc/supervisord.conf && \
    echo "command=php-fpm" >> /etc/supervisord.conf && \
    echo "autorestart=true" >> /etc/supervisord.conf && \
    echo "[program:nginx]" >> /etc/supervisord.conf && \
    echo "command=nginx -g 'daemon off;'" >> /etc/supervisord.conf && \
    echo "autorestart=true" >> /etc/supervisord.conf && \
    echo "stdout_logfile=/dev/stdout" >> /etc/supervisord.conf && \
    echo "stdout_logfile_maxbytes=0" >> /etc/supervisord.conf && \
    echo "stderr_logfile=/dev/stderr" >> /etc/supervisord.conf && \
    echo "stderr_logfile_maxbytes=0" >> /etc/supervisord.conf

# Permisos de carpetas para Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Limpieza y caché de Laravel al iniciar
RUN php artisan package:discover --ansi

# Puerto que expone Railway
EXPOSE 80

# Comando de inicio
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]