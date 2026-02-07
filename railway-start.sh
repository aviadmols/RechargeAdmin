#!/bin/sh
set -e
# מנקה cache ישן ואז בונה מחדש מהמשתנים הנוכחיים
echo "[railway-start] Clearing and caching config..."
php artisan config:clear
php artisan config:cache

# מחכה שה-DB (Railway proxy) יהיה מוכן – ה-proxy לפעמים לא זמין מיד בהפעלה
echo "[railway-start] Waiting 15s for DB proxy to be ready..."
sleep 15

# מיגרציות עם ניסיונות חוזרים – חיבור ל-DB לפעמים נופל (server closed the connection)
echo "[railway-start] Running migrations (updating DB tables)..."
migrate_attempt=1
migrate_max=8
migrate_ok=0
while [ $migrate_attempt -le $migrate_max ]; do
    if php artisan migrate --force 2>/dev/null; then
        echo "[railway-start] Migrations done."
        migrate_ok=1
        break
    fi
    echo "[railway-start] Migrate attempt $migrate_attempt/$migrate_max failed, retrying in 8s..."
    migrate_attempt=$((migrate_attempt + 1))
    [ $migrate_attempt -le $migrate_max ] && sleep 8
done
if [ $migrate_ok -eq 0 ]; then
    echo "[railway-start] Migrations failed after $migrate_max attempts. Start server anyway; run 'php artisan migrate --force' manually in Railway Shell if needed."
fi

echo "[railway-start] Starting server on port ${PORT:-8000}"
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
