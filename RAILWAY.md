# Deploying to Railway (Recharge Customer Portal)

Short guide to set up the project on [Railway](https://railway.com/) so that on each deploy the database and schema are created/updated automatically.

---

## Which database should I use?

**Recommended: PostgreSQL**  
On Railway it’s easiest to add **PostgreSQL**. Railway detects it and injects the `DATABASE_URL` variable. Laravel supports PostgreSQL out of the box.

**Alternative: MySQL**  
If you prefer MySQL, add the MySQL plugin in Railway. Railway still provides a connection string; you’ll need to map it to `DATABASE_URL` or set `DB_CONNECTION=mysql` and the required DB_* variables in Laravel.

In code: `config/database.php` already supports `DATABASE_URL` (used as the connection `url`), so once `DATABASE_URL` is set, Laravel uses it and you don’t need to set host/port/database/user/password manually.

---

## Railway setup steps

### 1. Create a project and connect Git

1. Go to [railway.com](https://railway.com/) and create a new project.
2. **New → GitHub Repo** and select the repo `aviadmols/RechargeAdmin` (or push your code to GitHub first).
3. Railway will detect Laravel and build the project using `railway.toml` or default settings.

### 2. Add a database (PostgreSQL recommended)

1. In the project: **+ New → Database → PostgreSQL**.
2. Railway will create a PostgreSQL instance and provide **`DATABASE_URL`**.
3. **Important:** Connect `DATABASE_URL` to your app service:
   - Select your Laravel service → **Variables**.
   - Add or copy `DATABASE_URL` from the PostgreSQL service (or use **Reference** in Railway to link services).

If you chose **MySQL** instead of PostgreSQL:

- **+ New → Database → MySQL**.
- Connect the MySQL connection string to your app service. If Railway only gives host/user/password, set:
  - `DB_CONNECTION=mysql`
  - `DB_HOST=...`, `DB_DATABASE=...`, `DB_USERNAME=...`, `DB_PASSWORD=...`
  Or build `DATABASE_URL` yourself and set `DATABASE_URL` (or `DB_URL`) accordingly.

### 3. Required environment variables

In your app service **Variables**, add or update:

| Variable | Description |
|----------|-------------|
| `APP_KEY` | Run locally: `php artisan key:generate --show` and paste the value. |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | Your app URL on Railway, e.g. `https://your-app.up.railway.app` |
| `DATABASE_URL` | Set automatically if you linked the PostgreSQL/MySQL service (see above). |

If you don’t have `DATABASE_URL` (e.g. MySQL with separate variables):

- `DB_CONNECTION=mysql`
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

**Optional:**

- `CACHE_STORE` = `database` or `redis` (if you add Redis).
- `QUEUE_CONNECTION` = `database` or `redis`.
- `SESSION_DRIVER` = `database`.
- Mail (SMTP) settings if you send OTP emails.

### 4. What runs on deploy

The `railway.toml` file defines:

- **preDeployCommand:**  
  `php artisan config:cache && php artisan migrate --force`  
  → On every deploy, migrations run so the database (tables) are created or updated automatically.

- **startCommand:**  
  `php artisan serve --host=0.0.0.0 --port=${PORT:-8000}`  
  → The app starts with Laravel’s built-in server.

So once `DATABASE_URL` (or another DB connection) is set and Railway runs the pre-deploy step, the database and all migration-based configuration are created or updated on each deploy.

### 5. Worker (queue – optional)

If you use the queue (e.g. for OTP emails):

- **+ New → Empty Service** (or “Worker” if available).
- Connect it to the same repo and set **Start Command:**  
  `php artisan queue:work --sleep=3 --tries=3`
- Copy the same environment variables (especially `APP_KEY`, `DATABASE_URL`) to the Worker service.

### 6. Scheduler (cron – optional)

To run scheduled commands (OTP cleanup, log pruning):

- In Railway: **Scheduled Jobs** or Cron.
- Run every minute: `php artisan schedule:run` (or configure as needed in `app/Console/Kernel.php`).

---

## Summary

- **Database to add:** PostgreSQL (recommended) or MySQL.
- **Configuration:** `DATABASE_URL` (or explicit MySQL connection) + `APP_KEY`, `APP_URL`, `APP_ENV=production`, `APP_DEBUG=false`.
- **Database and tables:** Created/updated automatically on each deploy via `preDeployCommand` in `railway.toml` which runs `php artisan migrate --force`.

After the first deploy, log in to the Admin (Filament) and configure the Recharge token and settings in the UI.
