# Stage 1: Build Frontend Assets
FROM node:20-alpine AS build
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: Reverb
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    build-base \
    autoconf \
    dos2unix \
    # Dependencias para PHP intl
    icu-libs \
    # Dependencias para PHP gd (IMÁGENES) - AQUÍ ESTABA EL ERROR
    libpng \
    libjpeg-turbo \
    freetype \
    libavif \
    libwebp \
    # Otras
    libzip

# Install system dependencies and PHP extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql bcmath intl zip pcntl redis gd opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Composer dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy application code
COPY . .

# Copy built assets
COPY --from=build /app/public/build /var/www/html/public/build

# Finish Composer setup
ENV REVERB_APP_ID=dummy
ENV REVERB_APP_KEY=dummy
ENV REVERB_APP_SECRET=dummy

# ------------------------------------------------------------------------------
# ✔ OPTIMIZACIÓN: Solo asignar permisos a carpetas de escritura
# ------------------------------------------------------------------------------
RUN composer dump-autoload --optimize

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD ["sh", "-c", "echo '🟡 [Reverb] Esperando a la Base de Datos...'; until php artisan db:monitor > /dev/null 2>&1; do sleep 2; done; echo '✅ [Reverb] DB lista. Iniciando servicio...'; while true; do php artisan reverb:start --host=0.0.0.0 --port=8080; sleep 1; done"]