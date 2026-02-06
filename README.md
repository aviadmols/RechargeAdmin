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

4. **Create admin user** (needed to log in at `/admin` and set Recharge token)
   ```bash
   php artisan admin:create --email=your@email.com --password=your-secure-password
   ```
   Or without options (you will be prompted):
   ```bash
   php artisan admin:create
   ```

5. **Frontend**
   ```bash
   npm install
   npm run build
   ```

6. **Recharge (required for portal to work)**
   - Open **https://your-domain/admin** (or http://localhost:8000/admin locally).
   - Log in with the admin email and password from step 4.
   - In the sidebar, open **Recharge Settings**.
   - Set **API Token** (from your Recharge dashboard), **Base URL** (e.g. `https://api.rechargeapps.com`), **API Version** (e.g. `2021-11`), and optionally **Store domain**.
   - Enable the feature toggles you need (cancel, swap, pause, address update), then click **Save**. Use **Test connection** to verify.
   - After saving, when a user signs in with OTP the app will call Recharge with this token to fetch their data (orders, subscriptions).

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
