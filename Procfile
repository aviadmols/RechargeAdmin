web: sh railway-start.sh
worker: php artisan migrate --force && php artisan queue:work --sleep=3 --tries=3
