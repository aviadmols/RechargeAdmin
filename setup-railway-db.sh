#!/bin/sh
# מריץ מהמחשב שלך – מתחבר ל-DB של Railway, מאפס טבלאות ויוצר אדמין.
# לפני ההרצה: העתק DATABASE_PUBLIC_URL מ-Railway (Postgres → Variables) ל-.env בפרויקט.

set -e
echo "==> Clearing config so Laravel uses DATABASE_PUBLIC_URL from .env..."
php artisan config:clear

echo "==> Connecting to Railway DB and resetting all tables..."
php artisan migrate:fresh --force

echo "==> Creating admin user..."
php artisan admin:create \
  --email="${RAILWAY_ADMIN_EMAIL:-aviadmols@gmail.com}" \
  --password="${RAILWAY_ADMIN_PASSWORD:-changeme123}" \
  --name="${RAILWAY_ADMIN_NAME:-Aviad}"

echo "==> Done. Log in at your app URL /admin"
