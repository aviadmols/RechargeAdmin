#!/bin/sh
set -e
# No migrate at startup: Railway's Postgres proxy often drops connections from the container.
# Run migrations once from your machine (see SETUP-RAILWAY-DB.md).
echo "[railway-start] Clearing and caching config..."
php artisan config:clear
php artisan config:cache

echo "[railway-start] Starting server on port ${PORT:-8000}"
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
