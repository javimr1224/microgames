FROM node:22-bookworm-slim AS node
FROM composer:2 AS composer
FROM php:8.3-cli-bookworm
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev libssl-dev \
    && docker-php-ext-install -j2 bcmath gd mbstring zip dom \
    && pecl install mongodb-2.3.0 && docker-php-ext-enable mongodb \
    && rm -rf /var/lib/apt/lists/*
COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
WORKDIR /app
COPY . .
# No app scripts, plugins, .env creation, migrations or credentials at build time.
RUN composer install --no-interaction --prefer-dist --no-progress --no-scripts --no-plugins \
    && npm ci --ignore-scripts --no-audit --no-fund \
    && npm --prefix frontend ci --ignore-scripts --no-audit --no-fund
ENV APP_ENV=testing APP_DEBUG=false \
    APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA= \
    APP_URL=http://localhost DB_CONNECTION=mongodb \
    DB_DSN=mongodb://127.0.0.1:27017 DB_DATABASE=microgames_testing \
    CACHE_STORE=array SESSION_DRIVER=array QUEUE_CONNECTION=sync MAIL_MAILER=array \
    VITE_API_BASE_URL=http://localhost CI=true
