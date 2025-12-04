# Dockerfile optimizado para Laravel 12 con PHP 8.2+
FROM php:8.2-cli


# Instalar dependencias del sistema necesarias para Laravel y Node.js
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configurar directorio de trabajo
WORKDIR /var/www

# Copiar archivos de configuración primero para aprovechar el cache de Docker
COPY composer.json composer.lock package.json package-lock.json ./

# Instalar dependencias de PHP
RUN composer install --no-dev --no-scripts --no-interaction --optimize-autoloader

# Instalar dependencias de Node.js
RUN npm ci

# Copiar resto del código fuente
COPY . .

# Construir assets para producción
RUN npm run build

# Configurar permisos para Laravel
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Crear usuario no-root para mayor seguridad
RUN id -u www-data &>/dev/null || useradd -G www-data,root -u 1000 -d /home/www-data www-data
USER www-data

# Ejecutar scripts de post-instalación de Composer
RUN composer run-script post-autoload-dump

# Generar key de aplicación si no existe
RUN if [ -z "$APP_KEY" ]; then php artisan key:generate; fi

# Ejecutar migraciones si la base de datos está disponible

# Exponer puerto
EXPOSE 8000

# Comando para iniciar la aplicación
CMD ["composer", "run", "dev"]
