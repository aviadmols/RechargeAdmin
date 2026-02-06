# Recharge Customer Portal

Laravel customer portal for Recharge: passwordless OTP login, order history, and subscription management. Admin dashboard via Filament.

## Requirements

- PHP 8.3+
- Composer
- Node.js & npm (for Tailwind/Alpine build)
- MySQL/PostgreSQL or SQLite
- Redis (optional, for cache/queue)

## Setup

1. **Clone and install**
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

2. **Database**
   - Use SQLite: `touch database/database.sqlite` and keep `DB_CONNECTION=sqlite` in `.env`
   - Or set `DB_CONNECTION=mysql` (or `pgsql`) and configure `DB_*` in `.env`

3. **Migrations**
   ```bash
   php artisan migrate
   ```

4. **Create admin user**
   ```bash
   php artisan tinker
   >>> \App\Models\Admin::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password')]);
   ```

5. **Frontend**
   ```bash
   npm install
   npm run build
   ```

6. **Recharge**
   - Log in at `/admin` with the admin user.
   - Go to Recharge Settings and set your Recharge API token, base URL, and feature toggles.

7. **Run**
   ```bash
   php artisan serve
   ```
   - Portal: http://localhost:8000/login  
   - Admin: http://localhost:8000/admin  

## Queue & scheduler

- Run a queue worker: `php artisan queue:work --sleep=3 --tries=3`
- Scheduler (cron): `* * * * * php /path/to/artisan schedule:run`
  - Cleans expired OTPs hourly.
  - Prunes audit logs older than 90 days daily.

## Railway

- Add MySQL/Postgres and optionally Redis from the Railway dashboard.
- Set env: `APP_KEY`, `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://<your-app>.railway.app`, `DATABASE_URL`, `REDIS_URL` (if used), `MAIL_*`, `CACHE_DRIVER`, `QUEUE_CONNECTION`.
- Build: `composer install --no-dev --optimize-autoloader` and `npm ci && npm run build`.
- Deploy: run `php artisan migrate --force` and start web + worker (see Procfile).

## Testing

```bash
php artisan test
```

## Security

- Recharge API token is stored encrypted; set only in Admin.
- Login/verify are rate-limited (e.g. 5 requests per 15 minutes).
- Subscription/order access is restricted to the logged-in customer.
- Security headers (X-Frame-Options, X-Content-Type-Options, Referrer-Policy) are applied globally.
