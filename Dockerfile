# ── Stage 1: build frontend assets ──────────────────────────────────────────
FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json* ./
RUN npm ci

COPY vite.config.js ./
COPY resources/ resources/
COPY public/ public/

RUN npm run build

# ── Stage 2: PHP application ─────────────────────────────────────────────────
FROM php:8.3-fpm-alpine AS app

RUN apk add --no-cache \
    git curl zip unzip \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    libzip-dev oniguruma-dev icu-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd pdo pdo_mysql pdo_sqlite \
        bcmath mbstring exif pcntl zip intl opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .
COPY --from=assets /app/public/build public/build

RUN composer dump-autoload --optimize --no-dev \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 755 storage bootstrap/cache

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["/entrypoint.sh"]
