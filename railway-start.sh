#!/bin/sh
set -e
# מנקה cache ישן (שאולי נשמר עם Postgres.railway.internal) ואז בונה מחדש מהמשתנים הנוכחיים
echo "[railway-start] Clearing and caching config..."
php artisan config:clear
php artisan config:cache

echo "[railway-start] Running migrations (updating DB tables)..."
php artisan migrate --force
echo "[railway-start] Migrations done."

echo "[railway-start] Starting server on port ${PORT:-8000}"
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
