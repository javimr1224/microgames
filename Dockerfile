# PHP CLI
FROM php:8.2-cli

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libssl-dev pkg-config \
    && rm -rf /var/lib/apt/lists/*

# Extensiones PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Node para Vite
COPY --from=node:20 /usr/local/bin /usr/local/bin
COPY --from=node:20 /usr/local/lib /usr/local/lib

# Directorio de trabajo
WORKDIR /var/www
COPY . .

# Crear carpetas y permisos
RUN mkdir -p storage bootstrap/cache public/build \
    && chown -R www-data:www-data storage bootstrap/cache public/build

# Dependencias PHP
RUN composer install --no-interaction --optimize-autoloader

# Dependencias Node + build Vite (produce manifest.json)
RUN npm install
RUN npm run build

# Arranque de Laravel (PHP server simple)
CMD ["sh", "-c", "php -S 0.0.0.0:$PORT -t public"]
