# ------------------------------
# Dockerfile para Railway (Producción)
# ------------------------------

# 1. Base PHP con CLI
FROM php:8.2-cli

# 2. Dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libssl-dev pkg-config \
    && rm -rf /var/lib/apt/lists/*

# 3. Extensiones PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
RUN pecl install mongodb && docker-php-ext-enable mongodb

# 4. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Node para Vite
COPY --from=node:20 /usr/local/bin /usr/local/bin
COPY --from=node:20 /usr/local/lib /usr/local/lib

# 6. Directorio de trabajo
WORKDIR /var/www
COPY . .

# 7. Crear carpetas críticas y permisos
RUN mkdir -p storage bootstrap/cache public/build \
    && chown -R www-data:www-data storage bootstrap/cache public/build

# 8. Dependencias PHP
RUN composer install --no-interaction --optimize-autoloader

# 9. Dependencias Node + build Vite (genera manifest.json)
RUN npm install
RUN npm run build

# 10. Exponer puerto para Railway
EXPOSE 8080

# 11. Arranque de Laravel con PHP CLI server
#    Railway asigna el puerto automáticamente con la variable $PORT
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT} -t public"]
