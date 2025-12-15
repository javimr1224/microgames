# Stage 1: Node / Vite build
FROM node:20-alpine AS node-builder

WORKDIR /app

# Configurar NODE_ENV para producción
ENV NODE_ENV=production

# Copiar archivos de dependencias
COPY package*.json ./

# Instalar TODAS las dependencias (incluidas devDependencies para el build)
RUN npm ci

# Copiar archivos necesarios para el build
COPY resources ./resources
COPY vite.config.js ./
COPY tailwind.config.js* ./
COPY postcss.config.js* ./
COPY public ./public

# Build assets con NODE_ENV=production
RUN npm run build

# Verificar que el build se creó correctamente
RUN ls -la public/build || echo "Build directory not found!"

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

# Copiar archivos de dependencias de PHP primero (mejor caching)
COPY composer.json composer.lock* ./

# Instalar dependencias de PHP
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts

# Copiar código de la aplicación
COPY . .

# Copiar assets compilados desde node-builder
COPY --from=node-builder /app/public/build ./public/build

# Verificar que los assets se copiaron
RUN ls -la public/build && cat public/build/manifest.json || echo "No manifest found"

# Crear directorios necesarios y establecer permisos
RUN mkdir -p storage/framework/{sessions,views,cache} \
    storage/logs \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache public/build 2>/dev/null || true

# Ejecutar scripts de composer
RUN composer run-script post-autoload-dump 2>/dev/null || true

# Exponer puerto
EXPOSE 8080

# Comando de inicio
CMD php artisan config:clear && \
    php artisan cache:clear && \
    php artisan view:clear && \
    php artisan route:clear && \
    php -S 0.0.0.0:${PORT:-8080} -t public