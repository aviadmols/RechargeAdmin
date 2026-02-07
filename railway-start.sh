#!/bin/sh
set -e
echo "[railway-start] Running migrations..."
php artisan migrate --force

echo "[railway-start] Clearing and caching config..."
php artisan config:clear
php artisan config:cache

echo "[railway-start] Starting server on port ${PORT:-8000}"
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
