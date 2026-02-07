#!/bin/sh
set -e
# Clear old config and rebuild from current env
echo "[railway-start] Clearing and caching config..."
php artisan config:clear
php artisan config:cache

# Wait for DB proxy to be ready
echo "[railway-start] Waiting 15s for DB proxy..."
sleep 15

# Wipe DB and run all migrations from scratch (fresh install)
echo "[railway-start] Running migrate:fresh (wipe DB and reinstall tables)..."
migrate_attempt=1
migrate_max=8
migrate_ok=0
while [ $migrate_attempt -le $migrate_max ]; do
    if php artisan migrate:fresh --force 2>/dev/null; then
        echo "[railway-start] Migrate fresh done."
        migrate_ok=1
        break
    fi
    echo "[railway-start] Migrate attempt $migrate_attempt/$migrate_max failed, retrying in 8s..."
    migrate_attempt=$((migrate_attempt + 1))
    [ $migrate_attempt -le $migrate_max ] && sleep 8
done
if [ $migrate_ok -eq 0 ]; then
    echo "[railway-start] Migrate failed after $migrate_max attempts. Starting server anyway."
fi

echo "[railway-start] Starting server on port ${PORT:-8000}"
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
