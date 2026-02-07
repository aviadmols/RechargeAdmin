# Deploying to Railway (Recharge Customer Portal)

Short guide to set up the project on [Railway](https://railway.com/). The start script does **not** run migrations (Railway's Postgres proxy often drops connections from the container). You run migrations **once from your machine** – see **SETUP-RAILWAY-DB.md**.

---

## Which database should I use?

**Recommended: PostgreSQL**  
On Railway add **PostgreSQL**. Railway injects `DATABASE_URL`. Laravel supports it. Use `DATABASE_PUBLIC_URL` (from Postgres Variables) in your app service if the internal host does not resolve.

**Alternative: MySQL**  
Add MySQL in Railway and set `DB_CONNECTION=mysql` plus `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, or build `DATABASE_URL`.

---

## Railway setup steps

### 1. Create a project and connect Git

1. Go to [railway.com](https://railway.com/), create a new project.
2. **New → GitHub Repo**, select your repo.
3. Railway will detect Laravel and use `railway.toml` / Procfile.

### 2. Add a database (PostgreSQL recommended)

1. **+ New → Database → PostgreSQL**.
2. Connect `DATABASE_URL` (or `DATABASE_PUBLIC_URL` for public URL) to your app service Variables.

### 3. Required environment variables

In your app service **Variables**:

| Variable | Required | Description |
|----------|----------|-------------|
| `DATABASE_URL` / `DATABASE_PUBLIC_URL` | Yes | From Railway Postgres (public URL if internal fails). |
| `APP_KEY` | Yes | Run locally: `php artisan key:generate --show` and paste. |
| `APP_URL` | Yes | Your app URL with **https**, e.g. `https://your-app.up.railway.app`. |
| `APP_ENV` | Recommended | `production` |
| `APP_DEBUG` | Recommended | `false` |

**Optional:** `CACHE_STORE`, `QUEUE_CONNECTION`, `SESSION_DRIVER`, mail settings.

### 4. What runs on deploy

- **preDeployCommand:** `php artisan config:cache`
- **startCommand:** `sh railway-start.sh` – clears config cache and starts `php artisan serve`. **Migrations do not run** in the container (to avoid DB connection failures). Run them once from your computer – see **SETUP-RAILWAY-DB.md**.

### 5. Worker (optional)

- New service from same repo. **Start Command:** `php artisan migrate --force && php artisan queue:work --sleep=3 --tries=3`
- Copy same Variables (including `DATABASE_PUBLIC_URL`) to the Worker.

### 6. Scheduler (optional)

- Railway Scheduled Jobs / Cron: `php artisan schedule:run` every minute if needed.

---

## Troubleshooting

### 502 Bad Gateway

1. **Correct URL:** If the URL is `worker-production-xxx.up.railway.app` that is the **Worker** (no HTTP server). Use the **Web** service URL (the one that runs `railway-start.sh`). If you only have a Worker, add a **Web** service with Start Command `sh railway-start.sh` and give it a Domain.
2. **Start Command:** Must be `sh railway-start.sh` (or empty to use Procfile). Remove any `php artisan queue:work` or `php artisan admin:create` from the Web service.
3. **Logs:** Deployments → View Logs. Look for `Starting server on port` and `Server running`. If the container stops before that, check the error above.
4. **Redeploy** after changing Start Command or Variables.

### Pre-deploy command failed

- View Logs for the failed deploy. If APP_KEY is missing, add it (run `php artisan key:generate --show` locally and paste).

### could not translate host name Postgres.railway.internal

Use the **public** database URL. In Railway: Postgres service → Variables → copy **DATABASE_PUBLIC_URL** or Public URL. Add it to your app service (and Worker) Variables. Redeploy.

### 500 error / "Attempting to connect to the database"

If Railway's **Postgres** service shows "Database Connection: Attempting to connect..." (in Postgres → Database tab), the DB is not ready. The app will show a **"Database temporarily unavailable"** page (503) instead of 500. Wait until Postgres shows **connected** in Railway, then refresh. You can also try restarting the Postgres service in Railway.

### server closed the connection unexpectedly / migrations fail at startup

The start script no longer runs migrations. Options:

1. Run migrations manually: Railway → Web service → Shell / Run Command → `php artisan migrate:fresh --force` (or `migrate --force`).
2. Ensure Postgres service is Online.
3. Run from your machine: set `DATABASE_PUBLIC_URL` in `.env` to Railway’s public URL, then run `php artisan migrate:fresh --force` and `php artisan admin:create ...` locally. See **SETUP-RAILWAY-DB.md**.

---

## Summary

- **Database:** PostgreSQL; set `DATABASE_PUBLIC_URL` (or `DATABASE_URL`) in app and Worker.
- **Required:** `APP_KEY`, `APP_URL`, `APP_ENV=production`, `APP_DEBUG=false`.
- **On deploy:** Start script only runs config cache and starts the server. **Run migrations once from your machine** (SETUP-RAILWAY-DB.md) so the DB has tables. Then log in at `/admin`.
