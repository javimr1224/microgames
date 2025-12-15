# PHP con FPM
FROM php:8.2-fpm

# ------------------------------
# 1. Dependencias del sistema
# ------------------------------
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libssl-dev pkg-config \
    && rm -rf /var/lib/apt/lists/*

# ------------------------------
# 2. Extensiones PHP
# ------------------------------
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# ------------------------------
# 3. Driver MongoDB
# ------------------------------
RUN pecl install mongodb && docker-php-ext-enable mongodb

# ------------------------------
# 4. Composer
# ------------------------------
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ------------------------------
# 5. Node para Vite (usar versión oficial)
# ------------------------------
COPY --from=node:20 /usr/local/bin /usr/local/bin
COPY --from=node:20 /usr/local/lib /usr/local/lib

# ------------------------------
# 6. Directorio de trabajo
# ------------------------------
WORKDIR /var/www

# ------------------------------
# 7. Copiar archivos del proyecto
# ------------------------------
COPY . .

# ------------------------------
# 8. Crear carpetas necesarias y permisos
# ------------------------------
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# ------------------------------
# 9. Dependencias PHP
# ------------------------------
RUN php -d memory_limit=-1 /usr/bin/composer install --no-interaction --optimize-autoloader

# ------------------------------
# 10. Dependencias Node (Vite)
# ------------------------------
RUN npm install
RUN npm run build

# ------------------------------
# 11. Exponer PHP-FPM
# ------------------------------
EXPOSE 9000

CMD ["php-fpm"]
