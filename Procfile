# אותו סקריפט כמו ב-railway.toml – מיגרציות ואז שרת
web: sh railway-start.sh
worker: php artisan migrate --force && php artisan queue:work --sleep=3 --tries=3
