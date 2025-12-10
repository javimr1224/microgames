# PHP con FPM
FROM php:8.2-fpm

# Dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libssl-dev pkg-config \
    && rm -rf /var/lib/apt/lists/*

# Extensiones PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# MongoDB
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www

# Permisos
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Copia el código
COPY . .

# Instala dependencias PHP
RUN php -d memory_limit=-1 /usr/bin/composer install --no-interaction --optimize-autoloader

# Exponer FPM
EXPOSE 9000

CMD ["php-fpm"]
