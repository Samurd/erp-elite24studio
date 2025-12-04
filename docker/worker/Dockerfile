# Stage 1: Build Frontend Assets
FROM node:20-alpine AS build
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: Worker
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

# Install Supervisor & Logs permissions
RUN apk add --no-cache supervisor && \
    mkdir -p /var/log/supervisor && \
    chown -R www-data:www-data /var/log/supervisor

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Composer dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy application code
COPY . .

# Copy built assets
COPY --from=build /app/public/build /var/www/html/public/build

# Copy Supervisor configuration
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Finish Composer setup (Env vars dummy para evitar error en package discovery)
ENV REVERB_APP_ID=dummy
ENV REVERB_APP_KEY=dummy
ENV REVERB_APP_SECRET=dummy

# ------------------------------------------------------------------------------
# ✔ OPTIMIZACIÓN: Solo asignar permisos a carpetas de escritura
# ------------------------------------------------------------------------------
RUN composer dump-autoload --optimize

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

CMD ["sh", "-c", "echo '🔧 [Worker] Ajustando permisos...'; chown -R www-data:www-data storage bootstrap/cache; chmod -R 775 storage bootstrap/cache; echo '🟡 [Worker] Esperando a la Base de Datos...'; until php artisan db:monitor > /dev/null 2>&1; do sleep 2; done; echo '✅ [Worker] DB lista. Iniciando Supervisor...'; supervisord -c /etc/supervisor/conf.d/supervisord.conf"]