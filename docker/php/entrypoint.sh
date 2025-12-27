#!/bin/sh
set -e

echo "🔧 [Entrypoint] Corrigiendo permisos..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# --------------------------------------------------------
# 0. SINCRONIZAR ASSETS NUEVOS (Fix para volúmenes persistentes)
# --------------------------------------------------------
# Si existe el backup creado en el Dockerfile, lo sincronizamos al volumen montado.
if [ -d "/var/www/public_backup" ]; then
    echo "📦 [Entrypoint] Sincronizando nuevos assets al volumen compartido..."
    cp -R /var/www/public_backup/. /var/www/html/public/
fi

# --------------------------------------------------------
# 1. ESPERAR A LA BASE DE DATOS (Mantenemos la espera)
# --------------------------------------------------------
echo "🟡 [Entrypoint] Esperando conexión a la Base de Datos..."
until php artisan db:monitor > /dev/null 2>&1; do
    echo "   ... la base de datos aún no está lista, reintentando en 2s"
    sleep 2
done
echo "✅ [Entrypoint] Conexión a Base de Datos exitosa."

# --------------------------------------------------------
# 2. MIGRACIONES Y SEEDERS (ELIMINADOS)
#    Se ejecutan mediante 'docker compose run --rm migrate'
# --------------------------------------------------------
echo "⏩ [Entrypoint] Saltando Migraciones y Seeders (Responsabilidad del servicio 'migrate')."


# --------------------------------------------------------
# 3. OPTIMIZACIÓN Y ARRANQUE (Cacheo)
# --------------------------------------------------------
echo "🔥 [Entrypoint] Cacheando configuración..."
# Es seguro cachear aquí porque la DB ya está conectada.
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🏁 [Entrypoint] Iniciando PHP-FPM..."
exec php-fpm