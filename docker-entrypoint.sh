#!/bin/sh
set -e

echo "[workbuddy-auth] Starting up..."

if [ ! -d "vendor" ]; then
    echo "[workbuddy-auth] vendor/ tidak ditemukan — menjalankan composer install..."
    composer install --no-interaction --prefer-dist
fi

chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo "[workbuddy-auth] Siap di http://localhost:8000"
exec php artisan serve --host=0.0.0.0 --port=8000