# ------------------------------
# Stage 1: Build de Node / Vite
# ------------------------------
FROM node:20 AS node-builder

WORKDIR /app

# Copiar package.json y package-lock.json
COPY package*.json ./

# Instalar dependencias Node
RUN npm install

# Copiar todo el proyecto (necesario para Vite)
COPY . .

# Build de Vite → genera public/build/manifest.json
RUN npm run build

# ------------------------------
# Stage 2: PHP con Laravel
# ------------------------------
FROM php:8.2-cli

# Dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libssl-dev pkg-config \
    && rm -rf /var/lib/apt/lists/*

# Extensiones PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Node para posible uso de npm scripts en runtime (opcional)
COPY --from=node:20 /usr/local/bin /usr/local/bin
COPY --from=node:20 /usr/local/lib /usr/local/lib

# Directorio de trabajo
WORKDIR /var/www
COPY . .

# Copiar los assets de Vite generados en el stage anterior
COPY --from=node-builder /app/public/build /var/www/public/build

# Crear carpetas críticas y permisos
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache public/build

# Dependencias PHP
RUN composer install --no-interaction --optimize-autoloader

# Exponer puerto para Railway
EXPOSE 8080

# Arranque de Laravel con PHP CLI server
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT} -t public"]
