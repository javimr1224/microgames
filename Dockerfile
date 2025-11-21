# Usa una imagen base de PHP con FPM (FastCGI Process Manager)
FROM php:8.2-fpm

# Instala dependencias del sistema necesarias para las extensiones de PHP y OpenSSL
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libssl-dev \
    pkg-config \
    && rm -rf /var/lib/apt/lists/*

# Instala extensiones de PHP comunes para Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instala y habilita la extensión de MongoDB con soporte SSL
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Instala Composer (gestor de dependencias de PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www

# Copia los archivos existentes al directorio de trabajo
COPY . .

# Instala las dependencias de Composer
RUN php -d memory_limit=-1 /usr/bin/composer install

# Expone el puerto 9000 para PHP-FPM
EXPOSE 9000

# El comando por defecto para ejecutar el contenedor
CMD ["php-fpm"]
