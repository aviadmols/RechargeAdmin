#!/bin/sh
set -e
# מנקה cache ישן ואז בונה מחדש מהמשתנים הנוכחיים
echo "[railway-start] Clearing and caching config..."
php artisan config:clear
php artisan config:cache

# מחכה קצת שה-DB (Railway proxy) יהיה מוכן – מונע "server closed the connection unexpectedly"
echo "[railway-start] Waiting 5s for DB to be ready..."
sleep 5

# מיגרציות עם ניסיונות חוזרים – חיבור ל-DB לפעמים נופל בהפעלה
echo "[railway-start] Running migrations (updating DB tables)..."
migrate_attempt=1
migrate_max=5
while [ $migrate_attempt -le $migrate_max ]; do
    if php artisan migrate --force 2>/dev/null; then
        echo "[railway-start] Migrations done."
        break
    fi
    echo "[railway-start] Migrate attempt $migrate_attempt/$migrate_max failed, retrying in 5s..."
    migrate_attempt=$((migrate_attempt + 1))
    if [ $migrate_attempt -le $migrate_max ]; then
        sleep 5
    else
        echo "[railway-start] Migrations failed after $migrate_max attempts, starting server anyway."
    fi
done

echo "[railway-start] Starting server on port ${PORT:-8000}"
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
