#!/bin/sh
set -e
echo "[railway-start] Caching config..."
php artisan config:cache

echo "[railway-start] Running migrations (updating DB tables)..."
php artisan migrate --force
echo "[railway-start] Migrations done."

echo "[railway-start] Starting server on port ${PORT:-8000}"
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
