# Stage 1: Build Frontend Assets
FROM node:20-alpine AS build

# Accept build arguments for Vite environment variables
ARG VITE_APP_NAME
ARG VITE_REVERB_APP_KEY
ARG VITE_REVERB_HOST
ARG VITE_REVERB_PORT
ARG VITE_REVERB_SCHEME

# Set them as environment variables for the build process
ENV VITE_APP_NAME=${VITE_APP_NAME}
ENV VITE_REVERB_APP_KEY=${VITE_REVERB_APP_KEY}
ENV VITE_REVERB_HOST=${VITE_REVERB_HOST}
ENV VITE_REVERB_PORT=${VITE_REVERB_PORT}
ENV VITE_REVERB_SCHEME=${VITE_REVERB_SCHEME}

WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: PHP-FPM (Production)
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

# ✔ AÑADIR: Configuración de PHP para límites de subida y timeouts (CRÍTICO para 422)
COPY docker/php/conf.d/custom.ini /usr/local/etc/php/conf.d/custom.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ------------------------------------------------------------------------------
# ✔ PRIMERO copiar solo composer.json / composer.lock
# ------------------------------------------------------------------------------
COPY composer.json composer.lock ./

# ------------------------------------------------------------------------------
# ✔ Instalar dependencias (SOLO DESCARGA)
# ------------------------------------------------------------------------------
# Mantenemos esto aquí para aprovechar la caché de capas de Docker si composer.json no cambia
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# ------------------------------------------------------------------------------
# ✔ AHORA sí copiar TODO el proyecto (Incluido el archivo 'artisan')
# ------------------------------------------------------------------------------
COPY . .

ENV REVERB_APP_ID=dummy
ENV REVERB_APP_KEY=dummy
ENV REVERB_APP_SECRET=dummy

# ------------------------------------------------------------------------------
# ✔ GENERAR AUTOLOAD (Ahora sí funcionará porque 'artisan' ya existe)
# ------------------------------------------------------------------------------
RUN composer dump-autoload --optimize

# ------------------------------------------------------------------------------
# Copy Vite build
# ------------------------------------------------------------------------------
COPY --from=build /app/public/build /var/www/html/public/build

# ------------------------------------------------------------------------------


# ------------------------------------------------------------------------------
# ✔ OPTIMIZACIÓN: Solo asignar permisos a carpetas de escritura
# ------------------------------------------------------------------------------
# No hacemos chown recursivo a todo /var/www/html (ahorra tiempo y espacio)
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Aplicamos dos2unix para asegurar finales de línea Linux (LF)
RUN dos2unix /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

CMD ["php-fpm"]
